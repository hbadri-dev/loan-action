<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\BuyerProgress;
use App\Models\ContractAgreement;
use App\Models\LoanTransfer;
use App\Enums\ContractStatus;
use App\Enums\BidStatus;
use App\Services\SMS\KavenegarService;
use App\Services\BuyerProgressService;
use App\Services\BiddingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuctionFlowController extends Controller
{
    protected BuyerProgressService $progressService;
    protected BiddingService $biddingService;

    public function __construct(BuyerProgressService $progressService, BiddingService $biddingService)
    {
        $this->progressService = $progressService;
        $this->biddingService = $biddingService;
    }

    /**
     * Join auction - redirect to unified show page
     */
    public function joinAuction(Auction $auction)
    {
        $user = Auth::user();

        // Check if user has an accepted bid for this auction
        $acceptedBid = $auction->bids()
            ->where('buyer_id', $user->id)
            ->where('status', BidStatus::ACCEPTED)
            ->first();

        if ($acceptedBid) {
            // Check if progress exists, if not create it
            $progress = $this->progressService->getProgress($auction, $user);
            if (!$progress) {
                $this->progressService->updateProgress($auction, $user, 'purchase-payment', 6);
            }
        } else {
            // Check if user can bid on this auction
            if (!$user->can('bid', $auction)) {
                return redirect()->route('buyer.dashboard')
                    ->with('error', 'شما نمی‌توانید در این مزایده شرکت کنید.');
            }

            // Check if user already has progress for this auction
            $progress = $this->progressService->getProgress($auction, $user);

            if (!$progress) {
                // First time joining auction - initialize step 1
                $this->progressService->initializeStep1($auction, $user);
            }
        }

        // Always redirect to the unified show page
        return redirect()->route('buyer.auction.show', $auction);
    }

    /**
     * Show auction details (Step 1)
     */
    public function showDetails(Auction $auction)
    {
        $user = Auth::user();

        // Check if user has an accepted bid for this auction
        $acceptedBid = $auction->bids()
            ->where('buyer_id', $user->id)
            ->where('status', BidStatus::ACCEPTED)
            ->first();

        if ($acceptedBid) {
            // Check if progress exists, if not create it
            $progress = $this->progressService->getProgress($auction, $user);
            if (!$progress) {
                $this->progressService->updateProgress($auction, $user, 'purchase-payment', 6);
                $progress = $this->progressService->getProgress($auction, $user);
            }
        } else {
            // Check if user can bid on this auction
            if (!$user->can('bid', $auction)) {
                return redirect()->route('buyer.dashboard')
                    ->with('error', 'شما نمی‌توانید در این مزایده شرکت کنید.');
            }

            // Get progress for the view
            $progress = $this->progressService->getProgress($auction, $user);

            // If no progress exists, initialize step 1
            if (!$progress) {
                $this->progressService->initializeStep1($auction, $user);
                $progress = $this->progressService->getProgress($auction, $user);
            }
        }

        // Get highest bid
        $highestBid = $auction->bids()
            ->where('status', BidStatus::HIGHEST)
            ->with('buyer')
            ->first();

        // Get contract agreement if on step 2
        $contractAgreement = null;
        if ($progress && $progress->step_name === 'contract') {
            $contractAgreement = ContractAgreement::where('auction_id', $auction->id)
                ->where('user_id', $user->id)
                ->where('role', 'buyer')
                ->first();
        }

        // Get payment receipt if on step 3
        $paymentReceipt = null;
        if ($progress && $progress->step_name === 'payment') {
            $paymentReceipt = \App\Models\PaymentReceipt::where('auction_id', $auction->id)
                ->where('user_id', $user->id)
                ->where('type', \App\Enums\PaymentType::BUYER_FEE)
                ->first();
        }

        $biddingService = $this->biddingService;
        return view('buyer.auction.show', compact('auction', 'highestBid', 'progress', 'contractAgreement', 'paymentReceipt', 'biddingService'));
    }

    /**
     * Continue from details to payment (Step 1 -> Step 2)
     */
    public function continueToContract(Request $request, Auction $auction)
    {
        $user = Auth::user();

        // Update buyer progress to step 2 (payment)
        $this->progressService->updateProgress($auction, $user, 'payment', 2);

        // Redirect back to the same page (showDetails) which will now show step 2 payment content
        return redirect()->route('buyer.auction.details', $auction);
    }

    /**
     * Verify OTP for contract confirmation (Step 2 -> Step 3)
     */
    public function verifyContractOtp(Request $request, Auction $auction)
    {
        $user = Auth::user();
        $request->validate([
            'otp_code' => 'required|string|size:6'
        ]);

        // Check if user is on contract step
        $progress = $this->progressService->getProgress($auction, $user);
        if (!$progress || $progress->step_name !== 'contract') {
            return redirect()->route('buyer.auction.details', $auction);
        }

        // Verify OTP
        $otpRecord = \App\Models\OtpCode::where('phone', $user->phone)
            ->where('code', $request->otp_code)
            ->where('purpose', 'contract-confirmation')
            ->where('expires_at', '>', now())
            ->whereNull('used_at')
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp_code' => 'کد تأیید نامعتبر یا منقضی شده است.']);
        }

        // Mark OTP as used
        $otpRecord->markAsUsed();

        // Update contract agreement status
        $contract = ContractAgreement::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('role', 'buyer')
            ->first();

        if ($contract) {
            $contract->update(['status' => ContractStatus::CONFIRMED]);
        }

        // Update buyer progress to step 3 (payment)
        $this->progressService->updateProgress($auction, $user, 'payment', 3);

        // Create payment receipt for commission payment if it doesn't exist
        \App\Models\PaymentReceipt::updateOrCreate(
            [
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'type' => \App\Enums\PaymentType::BUYER_FEE,
            ],
            [
                'amount' => 3000000, // 3 million toman commission
                'status' => \App\Enums\PaymentStatus::PENDING_REVIEW,
            ]
        );

        return redirect()->route('buyer.auction.details', $auction)
            ->with('success', 'قرارداد با موفقیت تأیید شد.');
    }

    /**
     * Show contract text (Step 2)
     */
    public function showContract(Auction $auction)
    {
        $user = Auth::user();

        $contract = ContractAgreement::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('role', 'buyer')
            ->first();

        if (!$contract) {
            return redirect()->route('buyer.auction.details', $auction);
        }

        if ($contract->status === ContractStatus::CONFIRMED) {
            // Update progress to next step
            $this->progressService->updateProgress($auction, $user, 'payment', 3);
            return redirect()->route('buyer.auction.details', $auction);
        }

        // Update progress
        $this->progressService->updateProgress($auction, $user, 'contract', 2);

        $contractText = config('contract.text', 'متن قرارداد در حال آماده‌سازی است.');

        return view('buyer.auction.contract', compact('auction', 'contract', 'contractText'));
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
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطا در ارسال کد تأیید. لطفاً دوباره تلاش کنید.'
                ]);
            }
            return back()->withErrors([
                'otp' => 'خطا در ارسال کد تأیید. لطفاً دوباره تلاش کنید.'
            ]);
        }

        // Update contract status
        $contract = ContractAgreement::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('role', 'buyer')
            ->first();

        $contract->update(['status' => ContractStatus::OTP_SENT]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'کد تأیید به شماره موبایل شما ارسال شد.',
                'expires_in' => 120 // 2 minutes
            ]);
        }

        return redirect()->route('buyer.auction.verify-contract', $auction)
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
            ->first();

        if (!$contract || $contract->status !== ContractStatus::OTP_SENT) {
            return redirect()->route('buyer.auction.contract', $auction);
        }

        return view('buyer.auction.verify-contract', compact('auction', 'contract'));
    }

    /**
     * Verify contract OTP and confirm (Step 2 -> Step 3)
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
            ->first();

        $contract->update([
            'status' => ContractStatus::CONFIRMED,
            'confirmed_at' => now(),
        ]);

        return redirect()->route('buyer.auction.details', $auction)
            ->with('success', 'قرارداد تأیید شد و به مرحله پرداخت منتقل شدید');
    }

    /**
     * Show waiting for seller approval (Step 5)
     */
    public function showWaitingSeller(Auction $auction)
    {
        $user = Auth::user();

        $userBid = $auction->bids()
            ->where('buyer_id', $user->id)
            ->where('status', 'accepted')
            ->first();

        if (!$userBid) {
            return redirect()->route('buyer.dashboard');
        }

        return view('buyer.auction.waiting-seller', compact('auction', 'userBid'));
    }

    /**
     * Show waiting for loan transfer (Step 6)
     */
    public function showLoanTransfer(Auction $auction)
    {
        $user = Auth::user();

        $loanTransfer = LoanTransfer::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->first();

        if (!$loanTransfer) {
            return redirect()->route('buyer.dashboard');
        }

        // If transfer receipt is uploaded, redirect to confirm transfer
        if ($loanTransfer->transfer_receipt_path) {
            return redirect()->route('buyer.auction.confirm-transfer', $auction);
        }

        return view('buyer.auction.loan-transfer', compact('auction', 'loanTransfer'));
    }

    /**
     * Show confirm transfer (Step 7)
     */
    public function showConfirmTransfer(Auction $auction)
    {
        $user = Auth::user();

        $loanTransfer = LoanTransfer::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->first();

        if (!$loanTransfer || !$loanTransfer->transfer_receipt_path) {
            return redirect()->route('buyer.auction.loan-transfer', $auction);
        }

        return view('buyer.auction.confirm-transfer', compact('auction', 'loanTransfer'));
    }

    /**
     * Show awaiting seller transfer (Step 7)
     */
    public function showAwaitingSellerTransfer(Auction $auction)
    {
        $user = Auth::user();

        $userBid = $auction->bids()
            ->where('buyer_id', $user->id)
            ->where('status', 'accepted')
            ->first();

        if (!$userBid) {
            return redirect()->route('buyer.dashboard');
        }

        // Update progress to this step
        $this->progressService->updateProgress($auction, $user, 'awaiting-seller-transfer', 7);

        return view('buyer.auction.awaiting-seller-transfer', compact('auction', 'userBid'));
    }

    /**
     * Get seller transfer status for AJAX polling
     */
    public function getSellerTransferStatus(Auction $auction)
    {
        $user = Auth::user();

        $loanTransfer = LoanTransfer::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->first();

        if (!$loanTransfer) {
            return response()->json(['status' => 'not_found']);
        }

        $response = [
            'status' => 'pending',
            'seller_transferred' => (bool) $loanTransfer->transfer_receipt_path,
        ];

        // If seller has uploaded transfer receipt, redirect to confirmation step
        if ($loanTransfer->transfer_receipt_path) {
            $response['redirect'] = route('buyer.auction.confirm-transfer', $auction);
        }

        return response()->json($response);
    }

    /**
     * Confirm loan transfer (Step 8)
     */
    public function confirmLoanTransfer(Request $request, Auction $auction)
    {
        $user = Auth::user();

        $loanTransfer = LoanTransfer::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->first();

        if (!$loanTransfer) {
            return redirect()->route('buyer.dashboard');
        }

        $loanTransfer->update([
            'buyer_confirmed_at' => now(),
        ]);

        // Update progress to confirm-transfer step
        $this->progressService->updateProgress($auction, $user, 'confirm-transfer', 8);

        return redirect()->route('buyer.auction.complete', $auction)
            ->with('success', 'انتقال وام تأیید شد.');
    }

    /**
     * Show completion page (Final)
     */
    public function showComplete(Auction $auction)
    {
        $user = Auth::user();

        $userBid = $auction->bids()
            ->where('buyer_id', $user->id)
            ->where('status', 'accepted')
            ->first();

        if (!$userBid) {
            return redirect()->route('buyer.dashboard');
        }

        return view('buyer.auction.complete', compact('auction', 'userBid'));
    }

    /**
     * Upload buyer purchase payment receipt (Step 6)
     */
    public function uploadPurchasePayment(Request $request, Auction $auction)
    {
            \Log::info('Purchase payment upload started', [
            'user_id' => Auth::id(),
            'auction_id' => $auction->id,
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'national_id' => $request->input('national_id')
        ]);

        $user = Auth::user();

        // Check if user has access to this auction and is on correct step
        $progress = BuyerProgress::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->first();

        \Log::info('Progress check', [
            'progress_exists' => $progress ? true : false,
            'step_name' => $progress ? $progress->step_name : null,
            'expected_step' => 'purchase-payment'
        ]);

        if (!$progress || $progress->step_name !== 'purchase-payment') {
            \Log::warning('Unauthorized access to purchase payment upload', [
                'user_id' => $user->id,
                'auction_id' => $auction->id,
                'progress_step' => $progress ? $progress->step_name : 'no_progress'
            ]);
            return redirect()->route('buyer.dashboard')->with('error', 'دسترسی غیرمجاز');
        }

        \Log::info('Starting validation for purchase payment upload');
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id' => 'required|string|size:10',
            'receipt_image' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120', // 5MB max
        ]);
        \Log::info('Validation passed for purchase payment upload');

        try {
            \Log::info('Getting FileUploadService instance');
            $fileUploadService = app(\App\Services\FileUploadService::class);

            // Upload the receipt image
            \Log::info('Starting file upload for purchase payment');
            $imagePath = $fileUploadService->storeReceiptImage($request->file('receipt_image'), $user->id);
            \Log::info('File uploaded successfully for purchase payment', ['image_path' => $imagePath]);

            // Update user's name and national_id
            \Log::info('Updating user name and national_id');
            $fullName = trim($request->input('first_name') . ' ' . $request->input('last_name'));
            $user->update([
                'name' => $fullName,
                'national_id' => $request->input('national_id')
            ]);
            \Log::info('User updated successfully', [
                'user_id' => $user->id,
                'full_name' => $fullName,
                'national_id' => $request->input('national_id')
            ]);

            // Get accepted bid amount
            $userBid = $auction->bids()
                ->where('buyer_id', $user->id)
                ->where('status', \App\Enums\BidStatus::ACCEPTED)
                ->first();

            \Log::info('User bid found', [
                'user_bid_exists' => $userBid ? true : false,
                'bid_amount' => $userBid ? $userBid->amount : null
            ]);

            // Create or update payment receipt
            \Log::info('Creating or updating payment receipt for purchase amount');
            $paymentReceipt = \App\Models\PaymentReceipt::updateOrCreate(
                [
                    'auction_id' => $auction->id,
                    'user_id' => $user->id,
                    'type' => \App\Enums\PaymentType::BUYER_PURCHASE_AMOUNT,
                ],
                [
                    'image_path' => $imagePath,
                    'status' => \App\Enums\PaymentStatus::PENDING_REVIEW,
                    'amount' => $userBid ? $userBid->amount : 0,
                ]
            );

            \Log::info('Payment receipt created/updated for purchase amount', [
                'payment_receipt_id' => $paymentReceipt->id,
                'image_path' => $imagePath
            ]);

            \Log::info('Redirecting to buyer.auction.details for purchase payment');
            return redirect()->route('buyer.auction.details', $auction)
                ->with('success', 'رسید پرداخت مبلغ خرید با موفقیت آپلود شد و در انتظار بررسی مدیر است.');

        } catch (\Exception $e) {
            \Log::error('Purchase payment upload error', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'auction_id' => $auction->id,
                'request_data' => $request->all()
            ]);
            return redirect()->route('buyer.auction.details', $auction)
                ->with('error', 'خطا در آپلود رسید: ' . $e->getMessage());
        }
    }
}
