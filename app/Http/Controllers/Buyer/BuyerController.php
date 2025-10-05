<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\ContractAgreement;
use App\Models\PaymentReceipt;
use App\Models\LoanTransfer;
use App\Models\BuyerProgress;
use App\Enums\AuctionStatus;
use App\Enums\BidStatus;
use App\Enums\ContractRole;
use App\Enums\ContractStatus;
use App\Enums\PaymentType;
use App\Enums\PaymentStatus;
use App\Services\BuyerProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BuyerController extends Controller
{
    protected BuyerProgressService $progressService;

    public function __construct(BuyerProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Show buyer dashboard with active auctions
     */
    public function dashboard()
    {
        $user = Auth::user();

        $activeAuctions = Auction::where('status', AuctionStatus::ACTIVE)
            ->with(['creator', 'bids' => function($query) {
                $query->where('status', BidStatus::HIGHEST);
            }, 'buyerProgress' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get()
            ->filter(function($auction) use ($user) {
                $userProgress = $auction->buyerProgress->where('user_id', $user->id)->first();

                // If user has completed progress or is at 'complete' step, don't show auction
                if ($userProgress && ($userProgress->is_completed || $userProgress->step_name === 'complete')) {
                    return false;
                }

                // If auction is completed or cancelled, don't show
                if (in_array($auction->status->value, ['completed', 'cancelled'])) {
                    return false;
                }

                return true;
            });

        // Get user's bids in progress - exclude completed and inaccessible
        $inProgressBids = Bid::where('buyer_id', $user->id)
            ->whereIn('status', [BidStatus::PENDING, BidStatus::HIGHEST, BidStatus::ACCEPTED])
            ->whereHas('auction', function($query) {
                $query->whereNotIn('status', [AuctionStatus::COMPLETED])
                      ->where('status', '!=', AuctionStatus::CANCELLED);
            })
            ->with(['auction', 'auction.creator'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->filter(function($bid) {
                // Additional filter: exclude bids where user has no access to continue the process
                $userProgress = $bid->auction->buyerProgress->where('user_id', auth()->id())->first();

                // If auction is completed or cancelled, exclude
                if (in_array($bid->auction->status->value, ['completed', 'cancelled'])) {
                    return false;
                }

                // If user progress is completed or at 'complete' step, exclude
                if ($userProgress && ($userProgress->is_completed || $userProgress->step_name === 'complete')) {
                    return false;
                }

                // If bid is outbid and user has no active progress, exclude
                if ($bid->status === BidStatus::OUTBID && (!$userProgress || $userProgress->is_completed)) {
                    return false;
                }

                // If bid is rejected and user has no active progress, exclude
                if ($bid->status === BidStatus::REJECTED) {
                    return false;
                }

                return true;
            });

        return view('buyer.dashboard', compact('activeAuctions', 'inProgressBids'));
    }

    /**
     * Show auction details (Step 1)
     */
    public function showAuction(Auction $auction)
    {
        $user = Auth::user();

        // Check if user has an accepted bid for this auction
        $acceptedBid = $auction->bids()
            ->where('buyer_id', $user->id)
            ->where('status', BidStatus::ACCEPTED)
            ->first();

        if ($acceptedBid) {
            // Redirect to purchase payment page
            return redirect()->route('buyer.auction.purchase-payment', $auction);
        }

        // Check if auction is active
        if ($auction->status !== AuctionStatus::ACTIVE) {
            return redirect()->route('buyer.dashboard')
                ->with('error', 'این مزایده فعال نیست.');
        }

        // Get highest bid
        $highestBid = $auction->bids()
            ->where('status', BidStatus::HIGHEST)
            ->first();

        return view('buyer.auction.show', compact('auction', 'highestBid'));
    }

    /**
     * Start auction participation (Step 1 -> Step 2)
     */
    public function startParticipation(Auction $auction)
    {
        $user = Auth::user();

        // Check if user already has a contract agreement for this auction
        $existingContract = ContractAgreement::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingContract) {
            // Redirect to appropriate step based on contract status
            return $this->redirectToStep($auction, $existingContract);
        }

        // Create new contract agreement
        $contract = ContractAgreement::create([
            'auction_id' => $auction->id,
            'user_id' => $user->id,
            'role' => ContractRole::BUYER,
            'status' => ContractStatus::PENDING,
        ]);

        return redirect()->route('buyer.auction.contract', $auction);
    }

    /**
     * Show contract text (Step 2)
     */
    public function showContract(Auction $auction)
    {
        $user = Auth::user();

        $contract = ContractAgreement::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$contract) {
            return redirect()->route('buyer.auction.show', $auction);
        }

        $contractText = config('contract.buyer_text', 'متن قرارداد در حال آماده‌سازی است.');

        return view('buyer.auction.contract', compact('auction', 'contract', 'contractText'));
    }

    /**
     * Send contract confirmation OTP (Step 2)
     */
    public function sendContractOtp(Request $request, Auction $auction)
    {
        $user = Auth::user();

        $contract = ContractAgreement::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$contract) {
            return redirect()->route('buyer.auction.show', $auction);
        }

        // Generate and send OTP
        $kavenegarService = app(\App\Services\SMS\KavenegarService::class);
        $code = $kavenegarService->generateOTP(6);

        // Store OTP
        \App\Models\OtpCode::create([
            'phone' => $user->phone,
            'code' => $code,
            'purpose' => 'contract-otp',
            'expires_at' => now()->addMinutes(2),
        ]);

        // Send OTP
        $sent = $kavenegarService->sendContractOTP($user->phone, $code);

        if (!$sent) {
            return back()->withErrors([
                'otp' => 'خطا در ارسال کد تأیید. لطفاً دوباره تلاش کنید.'
            ]);
        }

        // Update contract status
        $contract->update(['status' => ContractStatus::OTP_SENT]);

        return redirect()->route('buyer.auction.verify-contract', $auction)
            ->with('success', 'کد تأیید ارسال شد.');
    }

    /**
     * Show contract OTP verification form
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

        return redirect()->route('buyer.auction.payment', $auction)
            ->with('success', 'قرارداد تأیید شد.');
    }

    /**
     * Show buyer fee payment (Step 3)
     */
    public function showPayment(Auction $auction)
    {
        $user = Auth::user();

        $contract = ContractAgreement::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('status', ContractStatus::CONFIRMED)
            ->first();

        if (!$contract) {
            return redirect()->route('buyer.auction.show', $auction);
        }

        // Get or create payment receipt
        $paymentReceipt = PaymentReceipt::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::BUYER_FEE)
            ->first();

        if (!$paymentReceipt) {
            $paymentReceipt = PaymentReceipt::create([
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'type' => PaymentType::BUYER_FEE,
                'amount' => 3000000, // 3,000,000 Toman
                'status' => PaymentStatus::PENDING_REVIEW,
            ]);
        }

        return view('buyer.auction.payment', compact('auction', 'paymentReceipt'));
    }

    /**
     * Upload payment receipt (Step 3)
     */
    public function uploadPaymentReceipt(Request $request, Auction $auction)
    {
        $request->validate([
            'receipt_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB max
        ]);

        $user = Auth::user();

        $paymentReceipt = PaymentReceipt::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::BUYER_FEE)
            ->first();

        if (!$paymentReceipt) {
            return redirect()->route('buyer.auction.show', $auction);
        }

        // Store the uploaded file
        $imagePath = $request->file('receipt_image')->store('payment-receipts', 'public');

        // Update payment receipt - allow re-upload if rejected
        $paymentReceipt->update([
            'image_path' => $imagePath,
            'status' => PaymentStatus::PENDING_REVIEW,
            'reject_reason' => null, // Clear previous reject reason
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        return redirect()->route('buyer.auction.payment', $auction)
            ->with('success', 'رسید پرداخت آپلود شد و در انتظار بررسی است.');
    }

    /**
     * Show bid submission form (Step 4)
     */
    public function showBid(Auction $auction)
    {
        $user = Auth::user();

        // Check if payment is approved
        $paymentReceipt = PaymentReceipt::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::BUYER_FEE)
            ->where('status', PaymentStatus::APPROVED)
            ->first();

        if (!$paymentReceipt) {
            return redirect()->route('buyer.auction.payment', $auction)
                ->with('error', 'ابتدا باید پرداخت کارمزد تأیید شود.');
        }

        // Get current highest bid
        $highestBid = $auction->bids()
            ->where('status', BidStatus::HIGHEST)
            ->first();

        return view('buyer.auction.bid', compact('auction', 'highestBid'));
    }

    /**
     * Submit bid (Step 4 -> Step 5)
     */
    public function submitBid(Request $request, Auction $auction)
    {
        $user = Auth::user();

        // Check authorization using policy
        if (!$user->can('create', [Bid::class, $auction])) {
            abort(403, 'شما مجاز به ثبت پیشنهاد در این مزایده نیستید.');
        }

        $request->validate([
            'amount' => 'required|integer|min:' . $auction->min_purchase_price,
        ]);

        $amount = $request->input('amount');

        // Business rule: Only active auctions accept new bids
        if ($auction->status !== AuctionStatus::ACTIVE) {
            return back()->withErrors([
                'amount' => 'این مزایده دیگر پیشنهاد نمی‌پذیرد.'
            ])->withInput();
        }

        // Get current highest bid
        $currentHighest = $auction->bids()
            ->where('status', BidStatus::HIGHEST)
            ->first();

        // Validate bid amount
        if ($currentHighest && $amount <= $currentHighest->amount) {
            return back()->withErrors([
                'amount' => 'مبلغ پیشنهادی باید بیشتر از بالاترین پیشنهاد فعلی باشد.'
            ])->withInput();
        }

        DB::transaction(function () use ($auction, $user, $amount, $currentHighest) {
            // Mark current highest as outbid
            if ($currentHighest) {
                $currentHighest->update(['status' => BidStatus::OUTBID]);
            }

            // Create new bid
            Bid::create([
                'auction_id' => $auction->id,
                'buyer_id' => $user->id,
                'amount' => $amount,
                'status' => BidStatus::HIGHEST,
            ]);
        });

        return redirect()->route('buyer.auction.waiting-seller', $auction)
            ->with('success', 'پیشنهاد شما ثبت شد.');
    }

    /**
     * Show waiting for seller approval (Step 5)
     */
    public function showWaitingSeller(Auction $auction)
    {
        $user = Auth::user();

        $bid = Bid::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->where('status', BidStatus::HIGHEST)
            ->first();

        if (!$bid) {
            return redirect()->route('buyer.auction.show', $auction);
        }

        return view('buyer.auction.waiting-seller', compact('auction', 'bid'));
    }

    /**
     * Get bid status for AJAX polling
     */
    public function getBidStatus(Auction $auction)
    {
        $user = Auth::user();

        $bid = Bid::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->first();

        if (!$bid) {
            return response()->json(['status' => 'not_found']);
        }

        return response()->json([
            'status' => $bid->status->value,
            'label' => $bid->status->label(),
            'color' => $bid->status->color(),
        ]);
    }

    /**
     * Show purchase payment (Step 6)
     */
    public function showPurchasePayment(Auction $auction)
    {
        $user = Auth::user();

        $bid = Bid::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->where('status', BidStatus::ACCEPTED)
            ->first();

        if (!$bid) {
            return redirect()->route('buyer.auction.show', $auction);
        }

        // Get or create purchase payment receipt
        $paymentReceipt = PaymentReceipt::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::BUYER_PURCHASE_AMOUNT)
            ->first();

        if (!$paymentReceipt) {
            $paymentReceipt = PaymentReceipt::create([
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'type' => PaymentType::BUYER_PURCHASE_AMOUNT,
                'amount' => $bid->amount,
                'status' => PaymentStatus::PENDING_REVIEW,
            ]);
        }

        return view('buyer.auction.purchase-payment', compact('auction', 'bid', 'paymentReceipt'));
    }

    /**
     * Upload purchase payment receipt (Step 6)
     */
    public function uploadPurchaseReceipt(Request $request, Auction $auction)
    {
        $request->validate([
            'receipt_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB max
        ]);

        $user = Auth::user();

        $paymentReceipt = PaymentReceipt::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::BUYER_PURCHASE_AMOUNT)
            ->first();

        if (!$paymentReceipt) {
            return redirect()->route('buyer.auction.show', $auction);
        }

        // Store the uploaded file
        $imagePath = $request->file('receipt_image')->store('payment-receipts', 'public');

        // Update payment receipt
        $paymentReceipt->update([
            'image_path' => $imagePath,
            'status' => PaymentStatus::PENDING_REVIEW,
        ]);

        return redirect()->route('buyer.auction.purchase-payment', $auction)
            ->with('success', 'رسید پرداخت آپلود شد و در انتظار بررسی است.');
    }

    /**
     * Show loan transfer waiting (Step 7)
     */
    public function showLoanTransfer(Auction $auction)
    {
        $user = Auth::user();

        // Check if purchase payment is approved
        $paymentReceipt = PaymentReceipt::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::BUYER_PURCHASE_AMOUNT)
            ->where('status', PaymentStatus::APPROVED)
            ->first();

        if (!$paymentReceipt) {
            return redirect()->route('buyer.auction.purchase-payment', $auction);
        }

        // Get loan transfer record
        $loanTransfer = LoanTransfer::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->first();

        return view('buyer.auction.loan-transfer', compact('auction', 'loanTransfer'));
    }

    /**
     * Confirm loan transfer (Step 7 -> Complete)
     */
    public function confirmLoanTransfer(Request $request, Auction $auction)
    {
        $request->validate([
            'national_id' => 'required|string|size:10',
        ]);

        $user = Auth::user();
        $nationalId = $request->input('national_id');

        $loanTransfer = LoanTransfer::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->where('national_id_of_buyer', $nationalId)
            ->first();

        if (!$loanTransfer) {
            return back()->withErrors([
                'national_id' => 'کد ملی وارد شده صحیح نیست.'
            ])->withInput();
        }

        // Confirm the transfer
        $loanTransfer->update([
            'buyer_confirmed_at' => now(),
        ]);

        // Mark auction as completed
        $auction->update([
            'status' => AuctionStatus::COMPLETED,
            'completed_at' => now(),
        ]);

        return redirect()->route('buyer.auction.complete', $auction)
            ->with('success', 'انتقال وام تأیید شد.');
    }

    /**
     * Show completion page
     */
    public function showComplete(Auction $auction)
    {
        $user = Auth::user();

        $bid = Bid::where('auction_id', $auction->id)
            ->where('buyer_id', $user->id)
            ->where('status', BidStatus::ACCEPTED)
            ->first();

        if (!$bid) {
            return redirect()->route('buyer.dashboard');
        }

        return view('buyer.auction.complete', compact('auction', 'bid'));
    }

    /**
     * Show buyer orders page
     */
    public function orders()
    {
        $user = Auth::user();

        // Get user's auction progress - filter out completed and inaccessible
        $userProgress = $this->progressService->getUserProgress($user)
            ->filter(function($progress) {
                // Exclude completed progress
                if ($progress->is_completed) {
                    return false;
                }

                // Exclude progress for completed or cancelled auctions
                if (in_array($progress->auction->status->value, ['completed', 'cancelled'])) {
                    return false;
                }

                // Exclude progress where user has no access to continue
                // Check if auction is locked and user doesn't have accepted bid
                if ($progress->auction->status->value === 'locked') {
                    $userBid = $progress->auction->bids()
                        ->where('buyer_id', auth()->id())
                        ->where('status', \App\Enums\BidStatus::ACCEPTED)
                        ->first();

                    if (!$userBid) {
                        return false; // User doesn't have accepted bid for locked auction
                    }
                }

                return true;
            });

        // Get user's bids with related auctions
        $bids = Bid::where('buyer_id', $user->id)
            ->with(['auction', 'auction.creator', 'auction.buyerProgress' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Categorize bids - exclude completed and inaccessible
        $inProgress = $bids->filter(function ($bid) {
            // Exclude completed or cancelled auctions
            if (in_array($bid->auction->status->value, ['completed', 'cancelled'])) {
                return false;
            }

            // Only include bids where user can continue the process
            if (in_array($bid->status->value, ['pending', 'highest', 'accepted'])) {
                return true;
            }

            // For outbid bids, only include if user has active progress
            if ($bid->status->value === 'outbid') {
                $userProgress = $bid->auction->buyerProgress->first();
                return $userProgress && !$userProgress->is_completed;
            }

            return false;
        });

        $completed = $bids->filter(function ($bid) {
            return $bid->auction->status->value === 'completed';
        });

        $otherSelected = $bids->filter(function ($bid) {
            // Include rejected bids and outbid bids without active progress
            if ($bid->status->value === 'rejected') {
                return true;
            }

            if ($bid->status->value === 'outbid') {
                $userProgress = $bid->auction->buyerProgress->first();
                return !$userProgress || $userProgress->is_completed;
            }

            return false;
        });

        return view('buyer.orders', compact('userProgress', 'inProgress', 'completed', 'otherSelected'));
    }

    /**
     * Helper method to redirect to appropriate step
     */
    private function redirectToStep(Auction $auction, ContractAgreement $contract)
    {
        switch ($contract->status) {
            case ContractStatus::PENDING:
                return redirect()->route('buyer.auction.contract', $auction);
            case ContractStatus::OTP_SENT:
                return redirect()->route('buyer.auction.verify-contract', $auction);
            case ContractStatus::CONFIRMED:
                return redirect()->route('buyer.auction.payment', $auction);
            default:
                return redirect()->route('buyer.auction.show', $auction);
        }
    }
}
