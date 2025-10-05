<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Auction;
use App\Models\SellerSale;
use App\Services\ZarinpalService;
use App\Enums\PaymentType;
use App\Enums\PaymentStatus;
use App\Enums\SaleStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected ZarinpalService $zarinpalService;

    public function __construct(ZarinpalService $zarinpalService)
    {
        $this->zarinpalService = $zarinpalService;
    }

    /**
     * Initiate payment process
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'auction_id' => 'required|exists:auctions,id',
            'type' => 'required|string|in:buyer_fee,seller_fee,buyer_purchase_amount',
            'amount' => 'required|integer|min:1000',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id' => 'required|string|size:10|regex:/^\d{10}$/',
        ]);

        $user = Auth::user();
        $auction = Auction::findOrFail($request->auction_id);

        // Check if payment already exists and is completed
        $existingCompletedPayment = Payment::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::from($request->type))
            ->where('status', PaymentStatus::COMPLETED)
            ->first();

        if ($existingCompletedPayment) {
            // Payment already completed, redirect to next step
            return $this->redirectAfterSuccessfulPayment($existingCompletedPayment);
        }

        // Cancel/expire any existing pending/failed/cancelled payments
        Payment::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::from($request->type))
            ->whereIn('status', [PaymentStatus::PENDING, PaymentStatus::FAILED, PaymentStatus::CANCELLED, PaymentStatus::EXPIRED])
            ->update(['status' => PaymentStatus::CANCELLED]);

        // Create payment data
        $paymentData = [
            'user_id' => $user->id,
            'auction_id' => $auction->id,
            'type' => PaymentType::from($request->type),
            'amount' => $request->amount,
            'description' => $this->getPaymentDescription($request->type, $auction),
            'callback_url' => route('payment.callback'),
            'mobile' => $user->phone,
            'email' => $user->email,
        ];

        $result = $this->zarinpalService->createPaymentRequest($paymentData);

        if ($result['success']) {
            return redirect($result['gateway_url']);
        } else {
            return back()->with('error', $result['error']);
        }
    }

    /**
     * Handle payment callback
     */
    public function callback(Request $request)
    {
        $request->validate([
            'Authority' => 'required|string',
            'Status' => 'required|string',
        ]);

        $authority = $request->Authority;
        $status = $request->Status;

        $payment = Payment::where('authority', $authority)->first();

        if (!$payment) {
            return redirect()->route('dashboard')->with('error', 'پرداخت یافت نشد');
        }

        if ($status === 'OK') {
            // Verify payment
            $amount = $this->zarinpalService->formatAmount($payment->amount);
            $result = $this->zarinpalService->verifyPayment($authority, $amount);

            if ($result['success']) {
                // Payment successful - handle based on type
                $this->handleSuccessfulPayment($payment);

                // Redirect based on payment type and user role
                return $this->redirectAfterSuccessfulPayment($payment);
            } else {
                return redirect()->route('payment.failed', $payment->id)
                    ->with('error', $result['error']);
            }
        } else {
            // Payment cancelled or failed
            $payment->update(['status' => PaymentStatus::CANCELLED]);

            return redirect()->route('payment.failed', $payment->id)
                ->with('error', 'پرداخت لغو شد');
        }
    }

    /**
     * Show payment success page
     */
    public function success(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        return view('payment.success', compact('payment'));
    }

    /**
     * Show payment failed page
     */
    public function failed(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        return view('payment.failed', compact('payment'));
    }

    /**
     * Get payment description based on type
     */
    private function getPaymentDescription(string $type, Auction $auction): string
    {
        return match($type) {
            'buyer_fee' => "کارمزد خریدار - مزایده {$auction->loan_amount} تومان",
            'seller_fee' => "کارمزد فروشنده - مزایده {$auction->loan_amount} تومان",
            'buyer_purchase_amount' => "مبلغ خرید - مزایده {$auction->loan_amount} تومان",
            default => "پرداخت - مزایده {$auction->loan_amount} تومان",
        };
    }

    /**
     * Handle successful payment based on type
     */
    private function handleSuccessfulPayment(Payment $payment): void
    {
        switch ($payment->type) {
            case PaymentType::SELLER_FEE:
                $this->handleSellerFeePayment($payment);
                break;
            case PaymentType::BUYER_FEE:
                $this->handleBuyerFeePayment($payment);
                break;
            case PaymentType::BUYER_PURCHASE_AMOUNT:
                $this->handleBuyerPurchasePayment($payment);
                break;
        }
    }

    /**
     * Redirect user to appropriate page after successful payment
     */
    private function redirectAfterSuccessfulPayment(Payment $payment)
    {
        switch ($payment->type) {
            case PaymentType::SELLER_FEE:
                // Redirect seller back to auction show page (step 3 - bid acceptance)
                return redirect()->route('seller.auction.show', $payment->auction_id)
                    ->with('success', 'پرداخت کارمزد با موفقیت انجام شد. می‌توانید به مرحله پذیرش پیشنهادات بروید.');

            case PaymentType::BUYER_FEE:
                // Redirect buyer back to auction show page (step 3 - bid)
                return redirect()->route('buyer.auction.show', $payment->auction_id)
                    ->with('success', 'پرداخت کارمزد با موفقیت انجام شد. می‌توانید پیشنهاد خود را ثبت کنید.');

            case PaymentType::BUYER_PURCHASE_AMOUNT:
                // Redirect buyer back to auction show page (step 6 - awaiting seller transfer)
                return redirect()->route('buyer.auction.show', $payment->auction_id)
                    ->with('success', 'پرداخت مبلغ خرید با موفقیت انجام شد. در انتظار انتقال وام توسط فروشنده.');

            default:
                // Fallback to success page
                return redirect()->route('payment.success', $payment->id)
                    ->with('success', 'پرداخت با موفقیت انجام شد');
        }
    }

    /**
     * Handle seller fee payment
     */
    private function handleSellerFeePayment(Payment $payment): void
    {
        $sellerSale = SellerSale::where('auction_id', $payment->auction_id)
            ->where('seller_id', $payment->user_id)
            ->first();

        if ($sellerSale) {
            $sellerSale->update([
                'status' => SaleStatus::FEE_APPROVED,
                'current_step' => 3, // Step 3 is bid acceptance
            ]);
        }
    }

    /**
     * Handle buyer fee payment
     */
    private function handleBuyerFeePayment(Payment $payment): void
    {
        // Update buyer progress to next step (bid)
        $progressService = app(\App\Services\BuyerProgressService::class);
        $progressService->updateProgress($payment->auction, $payment->user, 'bid', 3);

        Log::info('Buyer fee payment completed', [
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'auction_id' => $payment->auction_id,
        ]);
    }

    /**
     * Handle buyer purchase amount payment
     */
    private function handleBuyerPurchasePayment(Payment $payment): void
    {
        // Update buyer progress to next step (awaiting seller transfer)
        $progressService = app(\App\Services\BuyerProgressService::class);
        $progressService->updateProgress($payment->auction, $payment->user, 'awaiting-seller-transfer', 6);

        // Update seller sale status to buyer payment approved
        $sellerSale = SellerSale::where('auction_id', $payment->auction_id)->first();
        if ($sellerSale) {
            $sellerSale->update([
                'status' => SaleStatus::BUYER_PAYMENT_APPROVED,
                'current_step' => 5,
            ]);
        }

        Log::info('Buyer purchase payment completed', [
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'auction_id' => $payment->auction_id,
        ]);
    }
}
