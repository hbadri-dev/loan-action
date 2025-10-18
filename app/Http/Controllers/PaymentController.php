<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\Auction;
use App\Models\SellerSale;
use App\Services\Payments\PaymentGatewayInterface;
use App\Services\Payments\CustomRedirectService;
use App\Enums\PaymentType;
use App\Enums\PaymentStatus;
use App\Enums\SaleStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PaymentGatewayInterface $gateway;

    public function __construct(PaymentGatewayInterface $gateway)
    {
        $this->gateway = $gateway;
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
            'full_name' => 'required|string|max:255',
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

        // Use custom redirect service for seller fees
        if ($request->type === 'seller_fee') {
            $customGateway = new CustomRedirectService();
            $result = $customGateway->createPaymentRequest($paymentData);
        } else {
            $result = $this->gateway->createPaymentRequest($paymentData);
        }

        Log::info('Payment gateway response', [
            'success' => $result['success'] ?? false,
            'gateway_url' => $result['gateway_url'] ?? null,
            'error' => $result['error'] ?? null,
        ]);

        if ($result['success']) {
            // For seller fee, remember where to return if gateway cancels without authority
            if ($paymentData['type'] === PaymentType::SELLER_FEE) {
                session(['seller_fee_return_auction_id' => $auction->id]);
            }
            // Update payment with user info
            $payment = Payment::find($result['payment_id']);
            if ($payment) {
                $payment->update([
                    'full_name' => trim($request->full_name),
                    'national_id' => $request->national_id,
                ]);
            }

            // Update user info (name and national_id)
            $user->update([
                'name' => trim($request->full_name),
                'national_id' => $request->national_id,
            ]);

            Log::info('Redirecting to gateway', ['url' => $result['gateway_url']]);
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
        Log::info('Payment callback received', [
            'method' => $request->method(),
            'all_params' => $request->all(),
            'gateway' => $this->gateway->getName(),
        ]);

        // Extract gateway-specific callback params
        [$authority, $status] = $this->gateway->extractCallback($request);

        $gatewayName = $this->gateway->getName();
        $authorityForVerify = $authority;

        // Try to locate payment by saved authority (general case)
        $payment = null;
        $authTrim = trim((string) $authority);
        if ($authTrim !== '') {
            $payment = Payment::where('authority', $authTrim)->first();
        }

        // Payping-specific fallbacks: it returns 'code' (purchase code) and 'refid' (bank ref id), plus 'clientrefid' (our payment id)
        if ($gatewayName === 'payping' && !$payment) {
            $code = trim((string) ($request->get('code') ?? ''));
            $clientRefId = trim((string) ($request->get('clientrefid') ?? ''));
            $refId = trim((string) ($request->get('refid') ?? ''));

            // Try by code against our stored authority
            if ($code !== '') {
                $payment = Payment::where('authority', $code)->first();
            }

            // Try by our payment id (clientRefId)
            if (!$payment && $clientRefId !== '' && ctype_digit($clientRefId)) {
                $payment = Payment::find((int) $clientRefId);
            }

            // Try via PaymentTransaction linkage
            if (!$payment && $code !== '') {
                $txn = PaymentTransaction::where('authority', $code)->first();
                if ($txn) {
                    $payment = Payment::find($txn->payment_id);
                }
            }

            // For verification, Payping verify requires refId (bank ref id) and amount
            if ($refId !== '' && $refId !== '-1') {
                $authorityForVerify = $refId;
            }
        }

        // Fallback: some gateways (cancel/no authority) may not send authority; try session for seller fee flow
        if (!$payment) {
            $sellerFeeReturnAuctionId = (int) session('seller_fee_return_auction_id', 0);
            if ($sellerFeeReturnAuctionId > 0) {
                // Clear session key after reading
                session()->forget('seller_fee_return_auction_id');

                // Redirect back to seller auction page with error
                return redirect()->route('seller.auction.show', $sellerFeeReturnAuctionId)
                    ->with('error', 'پرداخت لغو شد');
            }
        }

        if (!$payment) {
            return redirect()->route('dashboard')->with('error', 'پرداخت یافت نشد');
        }

        // Ensure user is logged in (gateway redirect might not preserve session)
        if (!Auth::check() && $payment->user) {
            Auth::login($payment->user);
        }

        // Determine success for payping if refid is positive number
        $isSuccess = in_array(strtoupper((string) $status), ['OK', 'SUCCESS', 'SUCCESSFUL']);
        if ($gatewayName === 'payping') {
            $refId = (string) ($request->get('refid') ?? '');
            if ($refId !== '' && ctype_digit($refId) && (int) $refId > 0) {
                $isSuccess = true;
            }
        }

        if ($isSuccess) {
            // Verify payment
            $amount = $this->gateway->formatAmount($payment->amount);
            $result = $this->gateway->verifyPayment($authorityForVerify, $amount);

            if ($result['success']) {
                // Payment successful - handle based on type
                $this->handleSuccessfulPayment($payment);

                // Redirect based on payment type and user role
                return $this->redirectAfterSuccessfulPayment($payment);
            } else {
                // If seller fee, always return to seller auction page (even on failure)
                if ($payment->type === PaymentType::SELLER_FEE) {
                    return redirect()->route('seller.auction.show', $payment->auction_id)
                        ->with('error', $result['error'] ?? 'پرداخت ناموفق بود. لطفاً مجدداً تلاش کنید.');
                }

                return redirect()->route('payment.failed', $payment->id)
                    ->with('error', $result['error']);
            }
        } else {
            // Payment cancelled or failed
            $payment->update(['status' => PaymentStatus::CANCELLED]);

            // If seller fee, always return to seller auction page (even on cancel)
            if ($payment->type === PaymentType::SELLER_FEE) {
                return redirect()->route('seller.auction.show', $payment->auction_id)
                    ->with('error', 'پرداخت لغو شد');
            }

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
     * Handle seller fee callback
     */
    public function sellerFeeCallback(Request $request)
    {
        Log::info('Seller fee callback received', [
            'method' => $request->method(),
            'all_params' => $request->all(),
        ]);

        $payment = $request->get('payment', '');

        if ($payment === 'success') {
            // Find the latest pending seller fee payment for the current user
            $pendingPayment = Payment::where('user_id', Auth::id())
                ->where('type', PaymentType::SELLER_FEE)
                ->where('status', PaymentStatus::PENDING)
                ->latest()
                ->first();

            if ($pendingPayment) {
                // Mark payment as completed
                $pendingPayment->update([
                    'status' => PaymentStatus::COMPLETED,
                    'ref_id' => 'custom_success_' . time(),
                ]);

                // Handle successful payment
                $this->handleSuccessfulPayment($pendingPayment);

                Log::info('Seller fee payment completed via callback', [
                    'payment_id' => $pendingPayment->id,
                    'user_id' => $pendingPayment->user_id,
                    'auction_id' => $pendingPayment->auction_id,
                ]);

                // Redirect back to auction show page with success message
                return redirect()->route('seller.auction.show', $pendingPayment->auction_id)
                    ->with('success', 'پرداخت کارمزد با موفقیت انجام شد. می‌توانید به مرحله پذیرش پیشنهادات بروید.');
            } else {
                Log::warning('No pending seller fee payment found for user', [
                    'user_id' => Auth::id(),
                ]);

                return redirect()->route('seller.dashboard')
                    ->with('error', 'پرداخت کارمزد یافت نشد');
            }
        } else {
            // Payment failed or cancelled
            Log::info('Seller fee payment failed or cancelled', [
                'payment_param' => $payment,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('seller.dashboard')
                ->with('error', 'پرداخت کارمزد لغو شد یا ناموفق بود');
        }
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

        // Send SMS notification to seller
        $this->notifySellerOfBuyerPayment($payment);

        Log::info('Buyer purchase payment completed', [
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'auction_id' => $payment->auction_id,
        ]);
    }

    /**
     * Notify seller that buyer has completed payment
     */
    private function notifySellerOfBuyerPayment(Payment $payment): void
    {
        try {
            $auction = $payment->auction;
            $buyer = $payment->user;
            $seller = $auction->creator;

            if (!$seller || !$seller->phone) {
                Log::warning('Cannot send SMS to seller - missing seller or phone', [
                    'payment_id' => $payment->id,
                    'auction_id' => $auction->id,
                ]);
                return;
            }

            $kavenegarService = app(\App\Services\SMS\KavenegarService::class);

            $sent = $kavenegarService->sendBuyerPaymentCompletedSMS(
                $seller->phone,
                $seller->name ?: '',
                $buyer->name ?: 'خریدار',
                $buyer->national_id ?: 'نامشخص'
            );

            if ($sent) {
                Log::info('Buyer payment completion SMS sent to seller', [
                    'payment_id' => $payment->id,
                    'seller_id' => $seller->id,
                    'buyer_id' => $buyer->id,
                    'auction_id' => $auction->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send buyer payment completion SMS to seller', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - SMS failure shouldn't break the payment flow
        }
    }
}
