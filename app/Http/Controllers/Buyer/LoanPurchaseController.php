<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\SellerSale;
use App\Models\BuyerProgress;
use App\Models\Bid;
use App\Models\Payment;
use App\Enums\SaleStatus;
use App\Enums\BidStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Services\AdminNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanPurchaseController extends Controller
{
    protected AdminNotifier $adminNotifier;

    public function __construct(AdminNotifier $adminNotifier)
    {
        $this->adminNotifier = $adminNotifier;
    }
    /**
     * Show payment link to buyer
     */
    public function show(Auction $auction)
    {
        $user = Auth::user();

        // Find accepted bid for this buyer
        $acceptedBid = Bid::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->where('status', BidStatus::ACCEPTED)
            ->first();

        if (!$acceptedBid) {
            return redirect()->route('buyer.dashboard')
                ->with('error', 'شما برای این مزایده پیشنهاد پذیرفته‌شده‌ای ندارید.');
        }

        // Find seller sale
        $sellerSale = SellerSale::where('auction_id', $auction->id)->first();

        if (!$sellerSale || !$sellerSale->payment_link) {
            return redirect()->route('buyer.dashboard')
                ->with('error', 'لینک پرداخت هنوز آماده نشده است.');
        }

        return view('buyer.loan-purchase.show', compact('auction', 'acceptedBid', 'sellerSale'));
    }

    /**
     * Handle payment callback
     */
    public function callback(Request $request)
    {
        \Log::info('LoanPurchaseController::callback called', [
            'payment_status' => $request->get('payment'),
            'auction_id' => $request->get('auction_id'),
            'all_params' => $request->all()
        ]);

        $paymentStatus = $request->get('payment');

        // Get auction_id from session or query parameter
        $auctionId = $request->get('auction_id') ?? session('purchase_auction_id');

        if (!$auctionId) {
            return redirect()->route('buyer.dashboard')
                ->with('error', 'اطلاعات مزایده یافت نشد.');
        }

        $auction = Auction::find($auctionId);

        if (!$auction) {
            return redirect()->route('buyer.dashboard')
                ->with('error', 'مزایده یافت نشد.');
        }

        $user = Auth::user();

        if (!$user) {
            // Try to get user from payment record
            $payment = Payment::where('auction_id', $auction->id)
                ->where('type', PaymentType::BUYER_PURCHASE_AMOUNT)
                ->where('status', PaymentStatus::PENDING)
                ->first();

            if ($payment) {
                $user = $payment->user;
            }
        }

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'لطفاً ابتدا وارد شوید.');
        }

        // Find accepted bid
        $acceptedBid = Bid::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->where('status', BidStatus::ACCEPTED)
            ->first();

        if (!$acceptedBid) {
            return redirect()->route('buyer.dashboard')
                ->with('error', 'پیشنهاد پذیرفته‌شده‌ای یافت نشد.');
        }

        // Find seller sale
        $sellerSale = SellerSale::where('auction_id', $auction->id)->first();

        if (!$sellerSale) {
            return redirect()->route('buyer.dashboard')
                ->with('error', 'اطلاعات فروش یافت نشد.');
        }

        // Find payment record
        $payment = Payment::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::BUYER_PURCHASE_AMOUNT)
            ->first();

        if ($paymentStatus === 'success') {
            DB::transaction(function () use ($sellerSale, $user, $auction, $payment) {
                // Update payment status to COMPLETED
                if ($payment) {
                    $payment->update([
                        'status' => PaymentStatus::COMPLETED,
                        'ref_id' => 'EXT-' . uniqid(),
                        'paid_at' => now(),
                    ]);
                }

                // Mark payment link as used
                $sellerSale->update([
                    'payment_link_used' => true,
                    'status' => SaleStatus::BUYER_PAYMENT_APPROVED,
                    'current_step' => 5,
                ]);

                // Update or create buyer progress
                BuyerProgress::updateOrCreate(
                    [
                        'auction_id' => $auction->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'current_step' => 6,
                        'step_name' => 'awaiting-seller-transfer',
                    ]
                );
            });

            // Notify seller about buyer payment completion
            $seller = $sellerSale->auction->seller;
            \Log::info('About to notify seller', [
                'seller_id' => $seller ? $seller->id : 'null',
                'seller_name' => $seller ? $seller->name : 'null',
                'seller_phone' => $seller ? $seller->phone : 'null',
                'sale_id' => $sellerSale->id
            ]);
            if ($seller) {
                $seller->notify(new \App\Notifications\BuyerPaymentCompletedNew($sellerSale));
                \Log::info('Seller notification sent successfully');
            } else {
                \Log::error('Seller not found for sale', ['sale_id' => $sellerSale->id]);
            }

            // Notify admin about buyer payment completion
            $this->adminNotifier->notifyBuyerAction('buyer_payment_completed', $user, [
                'auction_title' => $auction->title
            ]);

            return redirect()->route('buyer.auction.show', $auction)
                ->with('success', 'پرداخت با موفقیت تأیید شد. منتظر انتقال وام توسط فروشنده باشید.');
        } else {
            // Payment failed - update payment status
            if ($payment) {
                $payment->update([
                    'status' => PaymentStatus::FAILED,
                ]);
            }

            return redirect()->route('buyer.loan.purchase.show', $auction)
                ->with('error', 'پرداخت ناموفق بود. لطفاً دوباره تلاش کنید.');
        }
    }

    /**
     * Redirect to external payment link
     */
    public function redirectToPayment(Request $request, Auction $auction)
    {
        // Validate form data
        $validated = $request->validate([
            'full_name' => 'required|string|min:3|max:255',
            'national_id' => 'required|digits:10',
        ], [
            'full_name.required' => 'نام و نام خانوادگی الزامی است.',
            'full_name.min' => 'نام و نام خانوادگی باید حداقل 3 کاراکتر باشد.',
            'national_id.required' => 'کد ملی الزامی است.',
            'national_id.digits' => 'کد ملی باید دقیقاً 10 رقم باشد.',
        ]);

        $user = Auth::user();

        // Find accepted bid
        $acceptedBid = Bid::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->where('status', BidStatus::ACCEPTED)
            ->first();

        if (!$acceptedBid) {
            return redirect()->route('buyer.dashboard')
                ->with('error', 'شما برای این مزایده پیشنهاد پذیرفته‌شده‌ای ندارید.');
        }

        // Find seller sale
        $sellerSale = SellerSale::where('auction_id', $auction->id)->first();

        if (!$sellerSale || !$sellerSale->payment_link) {
            return redirect()->route('buyer.dashboard')
                ->with('error', 'لینک پرداخت هنوز آماده نشده است.');
        }

        // Check if payment already exists
        $existingPayment = Payment::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::BUYER_PURCHASE_AMOUNT)
            ->whereIn('status', [PaymentStatus::PENDING, PaymentStatus::COMPLETED])
            ->first();

        if ($existingPayment && $existingPayment->status === PaymentStatus::COMPLETED) {
            return redirect()->route('buyer.auction.show', $auction)
                ->with('info', 'پرداخت شما قبلاً انجام شده است.');
        }

        DB::transaction(function () use ($auction, $user, $acceptedBid, $validated, &$payment) {
            // Calculate total amount (bid + 1% commission)
            $bidAmount = $acceptedBid->amount;
            $totalAmount = $bidAmount + ($bidAmount * 0.01);

            // Create or update payment record with PENDING status
            $payment = Payment::updateOrCreate(
                [
                    'auction_id' => $auction->id,
                    'user_id' => $user->id,
                    'type' => PaymentType::BUYER_PURCHASE_AMOUNT,
                ],
                [
                    'amount' => $totalAmount,
                    'status' => PaymentStatus::PENDING,
                    'description' => 'پرداخت مبلغ خرید وام - مزایده #' . $auction->id,
                    'metadata' => json_encode([
                        'full_name' => $validated['full_name'],
                        'national_id' => $validated['national_id'],
                        'bid_amount' => $bidAmount,
                        'commission' => $bidAmount * 0.01,
                    ]),
                ]
            );
        });

        // Store data in session for callback
        session([
            'purchase_auction_id' => $auction->id,
            'purchase_payment_id' => $payment->id ?? null,
        ]);

        // Redirect to external payment link
        return redirect($sellerSale->payment_link);
    }
}
