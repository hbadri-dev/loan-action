<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\SellerSale;
use App\Models\ContractAgreement;
use App\Models\LoanTransfer;
use App\Models\Bid;
use App\Enums\SaleStatus;
use App\Enums\ContractStatus;
use App\Enums\BidStatus;
use App\Enums\AuctionStatus;
use App\Services\SMS\KavenegarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleFlowController extends Controller
{
    /**
     * Start sale process
     */
    public function startSale(Request $request, Auction $auction)
    {
        $user = Auth::user();

        // Check if user can start sale
        if (!$user->can('startSale', $auction)) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'شما نمی‌توانید فروش این مزایده را شروع کنید.');
        }

        // Create seller sale record
        $sellerSale = SellerSale::firstOrCreate(
            [
                'auction_id' => $auction->id,
                'seller_id' => $user->id,
            ],
            [
                'status' => SaleStatus::INITIATED,
                'current_step' => 1,
            ]
        );

        return redirect()->route('seller.sale.details', $auction);
    }

    /**
     * Show sale details (Step 1)
     */
    public function showSaleDetails(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        // Get highest bid
        $highestBid = $auction->bids()
            ->where('status', BidStatus::HIGHEST)
            ->first();

        return view('seller.sale.details', compact('auction', 'sellerSale', 'highestBid'));
    }

    /**
     * Continue from details to payment (Step 1 -> Step 2)
     * Skip contract step as requested
     */
    public function continueToContract(Request $request, Auction $auction)
    {
        \Log::info('continueToContract called', ['auction_id' => $auction->id, 'user_id' => Auth::id()]);

        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'فروش یافت نشد.');
        }

        // Skip contract step and go directly to payment step
        $sellerSale->update([
            'current_step' => 2,
            'status' => SaleStatus::FEE_APPROVED // Skip contract confirmation
        ]);

        return redirect()->route('seller.auction.show', $auction)
            ->with('success', 'مرحله اول تکمیل شد و به مرحله پرداخت منتقل شدید.');
    }

    /**
     * Show contract (Step 2)
     */
    public function showContract(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale || $sellerSale->current_step < 2) {
            return redirect()->route('seller.sale.details', $auction);
        }

        $contract = ContractAgreement::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('role', 'seller')
            ->first();

        if ($contract && $contract->status === ContractStatus::CONFIRMED) {
            return redirect()->route('seller.sale.payment', $auction);
        }

        $contractText = config('contract.seller_text', 'متن قرارداد در حال آماده‌سازی است.');

        return view('seller.sale.contract', compact('auction', 'sellerSale', 'contract', 'contractText'));
    }

    /**
     * Send contract OTP (Step 2)
     */
    public function sendContractOtp(Request $request, Auction $auction, KavenegarService $kavenegarService)
    {
        $user = Auth::user();
        $code = $kavenegarService->generateOTP(6);

        // Store OTP
        \App\Models\OtpCode::create([
            'phone' => $user->phone,
            'code' => $code,
            'purpose' => 'contract-confirmation',
            'expires_at' => now()->addMinutes(2),
        ]);

        // Send OTP
        $sent = $kavenegarService->sendContractOTP($user->phone, $code);

        if (!$sent) {
            return back()->withErrors([
                'otp' => 'خطا در ارسال کد تأیید. لطفاً دوباره تلاش کنید.'
            ]);
        }

        // Create or update contract agreement
        ContractAgreement::updateOrCreate(
            [
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'role' => 'seller',
            ],
            [
                'status' => ContractStatus::OTP_SENT,
            ]
        );

        return redirect()->route('seller.sale.verify-contract', $auction)
            ->with('success', 'کد تأیید ارسال شد.');
    }

    /**
     * Show contract verification form (Step 2)
     */
    public function showContractVerification(Auction $auction)
    {
        $user = Auth::user();

        $contract = ContractAgreement::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('role', 'seller')
            ->first();

        if (!$contract || $contract->status !== ContractStatus::OTP_SENT) {
            return redirect()->route('seller.sale.contract', $auction);
        }

        return view('seller.sale.verify-contract', compact('auction', 'contract'));
    }

    /**
     * Verify contract OTP (Step 2 -> Step 3)
     */
    public function verifyContract(Request $request, Auction $auction)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $code = $request->input('code');

        // Find valid OTP code
        $otpCode = \App\Models\OtpCode::where('phone', $user->phone)
            ->where('code', $code)
            ->where('purpose', 'contract-confirmation')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpCode) {
            return back()->withErrors([
                'code' => 'کد تأیید نامعتبر یا منقضی شده است.'
            ])->withInput();
        }

        // Mark OTP as used
        $otpCode->markAsUsed();

        // Update contract status
        $contract = ContractAgreement::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('role', 'seller')
            ->first();

        $contract->update([
            'status' => ContractStatus::CONFIRMED,
            'confirmed_at' => now(),
        ]);

        // Update seller sale step
        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        $sellerSale->update(['current_step' => 3]);

        return redirect()->route('seller.sale.payment', $auction)
            ->with('success', 'قرارداد تأیید شد.');
    }

    /**
     * Show bid acceptance (Step 4)
     */
    public function showBidAcceptance(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        // Get highest bid
        $highestBid = $auction->bids()
            ->where('status', BidStatus::HIGHEST)
            ->with('buyer')
            ->first();

        if (!$highestBid) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.sale.bid-acceptance', compact('auction', 'sellerSale', 'highestBid'));
    }

    /**
     * Accept bid (Step 4 -> Step 5)
     */
    public function acceptBid(Request $request, Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'فروش یافت نشد.');
        }

        // Check authorization using policy
        $highestBid = $auction->bids()
            ->where('status', BidStatus::HIGHEST)
            ->first();


        if (!$highestBid) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'پیشنهادی برای پذیرش یافت نشد.');
        }

        if (!$user->can('accept', $highestBid)) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'شما مجاز به پذیرش این پیشنهاد نیستید.');
        }

        // Business rule: Only active auctions can accept bids
        if ($auction->status !== AuctionStatus::ACTIVE) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'این مزایده دیگر فعال نیست.');
        }

        // Check if another seller has already accepted an offer (race condition protection)
        $existingAcceptedSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', '!=', $user->id)
            ->whereIn('status', [
                SaleStatus::OFFER_ACCEPTED,
                SaleStatus::AWAITING_BUYER_PAYMENT,
                SaleStatus::BUYER_PAYMENT_APPROVED,
                SaleStatus::LOAN_TRANSFERRED,
                SaleStatus::TRANSFER_CONFIRMED
            ])
            ->first();

        if ($existingAcceptedSale) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'فروشنده دیگری قبلاً این پیشنهاد را پذیرفته است.');
        }

        DB::transaction(function () use ($auction, $highestBid, $sellerSale, $user) {
            // Double-check auction status in transaction (race condition protection)
            $freshAuction = Auction::lockForUpdate()->find($auction->id);
            if ($freshAuction->status !== AuctionStatus::ACTIVE) {
                throw new \Exception('مزایده دیگر فعال نیست.');
            }

            // Update bid status
            $highestBid->update(['status' => BidStatus::ACCEPTED]);

            // Lock auction - prevents new bids and other sellers from accepting
            $freshAuction->update([
                'status' => AuctionStatus::LOCKED,
                'locked_at' => now(),
            ]);

            // Update seller sale - this seller becomes the official seller
            $sellerSale->update([
                'selected_bid_id' => $highestBid->id,
                'status' => SaleStatus::AWAITING_BUYER_PAYMENT,
                'current_step' => 4,
            ]);

            // Cancel other seller sales for this auction
            SellerSale::where('auction_id', $auction->id)
                ->where('seller_id', '!=', $user->id)
                ->whereNotIn('status', [SaleStatus::COMPLETED, SaleStatus::CANCELLED])
                ->update(['status' => SaleStatus::CANCELLED]);

            // Create loan transfer record
            LoanTransfer::create([
                'auction_id' => $auction->id,
                'seller_id' => $sellerSale->seller_id,
                'buyer_id' => $highestBid->buyer_id,
                'national_id_of_buyer' => $highestBid->buyer->phone ?? '',
                'transfer_receipt_path' => '',
            ]);

            // Notify buyer that their bid was accepted
            $highestBid->buyer->notify(new \App\Notifications\BidAccepted($highestBid));
        });

        return redirect()->route('seller.sale.awaiting-buyer-payment', $auction)
            ->with('success', 'پیشنهاد پذیرفته شد و مزایده قفل شد.');
    }

    /**
     * Show awaiting buyer payment (Step 5)
     */
    public function showAwaitingBuyerPayment(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->with('selectedBid.buyer')
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.sale.awaiting-buyer-payment', compact('auction', 'sellerSale'));
    }

    /**
     * Get buyer payment status (Step 5)
     */
    public function getBuyerPaymentStatus(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            return response()->json(['status' => 'not_found']);
        }

        // Check if buyer purchase payment is approved
        $buyerPayment = \App\Models\PaymentReceipt::where('auction_id', $auction->id)
            ->where('type', 'buyer_purchase_amount')
            ->where('status', 'approved')
            ->first();

        $response = [
            'status' => $sellerSale->status->value,
            'payment_approved' => (bool) $buyerPayment,
        ];

        // If payment is approved, update seller sale status and redirect to next step
        if ($buyerPayment) {
            // Update seller sale status to loan transfer step
            $sellerSale->update([
                'status' => SaleStatus::BUYER_PAYMENT_APPROVED,
                'current_step' => 5,
            ]);

            $response['redirect'] = route('seller.sale.loan-transfer', $auction);
        }

        return response()->json($response);
    }

    /**
     * Show loan transfer (Step 6)
     */
    public function showLoanTransfer(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->with('selectedBid.buyer')
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        $loanTransfer = LoanTransfer::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        return view('seller.sale.loan-transfer', compact('auction', 'sellerSale', 'loanTransfer'));
    }

    /**
     * Upload loan transfer receipt (Step 6)
     */
    public function uploadLoanTransferReceipt(Request $request, Auction $auction)
    {
        $request->validate([
            'transfer_receipt' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB max
        ]);

        $user = Auth::user();

        $loanTransfer = LoanTransfer::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$loanTransfer) {
            return redirect()->route('seller.dashboard');
        }

        // Store the uploaded file
        $imagePath = $request->file('transfer_receipt')->store('loan-transfers', 'public');

        // Update loan transfer
        $loanTransfer->update([
            'transfer_receipt_path' => $imagePath,
        ]);

        // Update seller sale status
        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        $sellerSale->update([
            'status' => SaleStatus::LOAN_TRANSFERRED,
            'current_step' => 7,
        ]);

        return redirect()->route('seller.sale.awaiting-transfer-confirmation', $auction)
            ->with('success', 'رسید انتقال وام آپلود شد.');
    }

    /**
     * Show awaiting transfer confirmation (Step 7)
     */
    public function showAwaitingTransferConfirmation(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->with('selectedBid.buyer')
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        $loanTransfer = LoanTransfer::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        return view('seller.sale.awaiting-transfer-confirmation', compact('auction', 'sellerSale', 'loanTransfer'));
    }

    /**
     * Get transfer confirmation status (Step 7)
     */
    public function getTransferConfirmationStatus(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            return response()->json(['status' => 'not_found']);
        }

        $loanTransfer = LoanTransfer::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        $response = [
            'status' => $sellerSale->status->value,
            'buyer_confirmed' => (bool) $loanTransfer->buyer_confirmed_at,
            'admin_confirmed' => (bool) $loanTransfer->admin_confirmed_at,
        ];

        // If both are confirmed, redirect to completion
        if ($loanTransfer->buyer_confirmed_at && $loanTransfer->admin_confirmed_at) {
            $response['redirect'] = route('seller.sale.completion', $auction);
        }

        return response()->json($response);
    }

    /**
     * Show sale completion (Step 8)
     */
    public function showSaleCompletion(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->with('selectedBid.buyer')
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.sale.completion', compact('auction', 'sellerSale'));
    }

    /**
     * Complete sale (Step 8)
     */
    public function completeSale(Request $request, Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        // Update seller sale status
        $sellerSale->update([
            'status' => SaleStatus::COMPLETED,
            'current_step' => 6,
        ]);

        return redirect()->route('seller.dashboard')
            ->with('success', 'فروش تکمیل شد. منتظر تأیید نهایی ادمین باشید.');
    }
}
