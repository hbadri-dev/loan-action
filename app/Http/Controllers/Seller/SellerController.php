<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\SellerSale;
use App\Models\ContractAgreement;
use App\Models\PaymentReceipt;
use App\Models\LoanTransfer;
use App\Enums\AuctionStatus;
use App\Enums\BidStatus;
use App\Enums\ContractRole;
use App\Enums\ContractStatus;
use App\Enums\SaleStatus;
use App\Enums\PaymentType;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\SMS\KavenegarService;
use App\Services\AdminNotifier;

class SellerController extends Controller
{
    protected AdminNotifier $adminNotifier;

    public function __construct(AdminNotifier $adminNotifier)
    {
        $this->adminNotifier = $adminNotifier;
    }
    /**
     * Show auction details for seller
     */
    public function showAuction(Auction $auction)
    {
        $user = Auth::user();

        // Get seller sale record first
        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        // Check if auction is accessible
        if ($auction->status->value !== 'active') {
            // If auction is locked, check if seller has an active sale
            if ($auction->status->value === 'locked') {
                if (!$sellerSale || in_array($sellerSale->status, [SaleStatus::COMPLETED, SaleStatus::CANCELLED])) {
                    abort(403, 'فقط مزایده‌های فعال یا مزایده‌هایی که در آن فروش فعال دارید قابل دسترسی هستند.');
                }
            } else {
                abort(403, 'فقط مزایده‌های فعال یا مزایده‌هایی که در آن فروش فعال دارید قابل دسترسی هستند.');
            }
        }

        // If no seller sale exists, create one for step 1
        if (!$sellerSale) {
            $sellerSale = SellerSale::create([
                'auction_id' => $auction->id,
                'seller_id' => $user->id,
                'status' => SaleStatus::INITIATED,
                'current_step' => 1,
            ]);
        } else {
            // Refresh the model to get the latest data
            $sellerSale->refresh();
        }

        // Get highest bid
        $highestBid = $auction->bids()
            ->where('status', BidStatus::HIGHEST)
            ->with('buyer')
            ->first();

        return view('seller.auction.show', compact('auction', 'sellerSale', 'highestBid'));
    }


    /**
     * Verify OTP for contract confirmation
     */
    public function verifyContractOtp(Request $request, Auction $auction)
    {
        $user = Auth::user();
        $otpCode = $request->input('otp_code');

        // Check if user has access to this auction
        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale || $sellerSale->current_step != 2) {
            return redirect()->route('seller.dashboard')->with('error', 'دسترسی غیرمجاز');
        }

        // Verify OTP
        $otp = \App\Models\OtpCode::where('phone', $user->phone)
            ->where('code', $otpCode)
            ->where('purpose', 'contract-confirmation')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return redirect()->route('seller.auction.show', $auction)
                ->with('error', 'کد تأیید نامعتبر یا منقضی شده است');
        }

        // Mark OTP as used
        $otp->markAsUsed();

        // Update seller sale to step 3
        $sellerSale->update([
            'current_step' => 3,
            'status' => SaleStatus::CONTRACT_CONFIRMED
        ]);

        // Create contract agreement
        \App\Models\ContractAgreement::updateOrCreate(
            [
                'auction_id' => $auction->id,
                'user_id' => $user->id,
            ],
            [
                'status' => ContractStatus::CONFIRMED,
                'role' => ContractRole::SELLER,
                'confirmed_at' => now(),
            ]
        );

        return redirect()->route('seller.auction.show', $auction)
            ->with('success', 'قرارداد تأیید شد و به مرحله بعد منتقل شدید');
    }

    /**
     * Upload seller payment receipt (Step 3)
     */
    public function uploadReceipt(Request $request, Auction $auction)
    {
        $user = Auth::user();

        // Check if user has access to this auction
        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale || $sellerSale->current_step != 2) {
            return redirect()->route('seller.dashboard')->with('error', 'دسترسی غیرمجاز');
        }

        $request->validate([
            'receipt_image' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);

        try {
            $fileUploadService = app(\App\Services\FileUploadService::class);

            // Upload the receipt image
            $imagePath = $fileUploadService->storeReceiptImage($request->file('receipt_image'), $user->id);

            // Create or update payment receipt
            \App\Models\PaymentReceipt::updateOrCreate(
                [
                    'auction_id' => $auction->id,
                    'user_id' => $user->id,
                    'type' => \App\Enums\PaymentType::SELLER_FEE,
                ],
                [
                    'image_path' => $imagePath,
                    'status' => \App\Enums\PaymentStatus::PENDING_REVIEW,
                    'amount' => 200000, // 200,000 Toman
                ]
            );

            return redirect()->route('seller.auction.show', $auction)
                ->with('success', 'رسید پرداخت با موفقیت آپلود شد و در انتظار بررسی مدیر است.');

        } catch (\Exception $e) {
            return redirect()->route('seller.auction.show', $auction)
                ->with('error', 'خطا در آپلود رسید. لطفاً دوباره تلاش کنید.');
        }
    }


    /**
     * Show seller dashboard with active auctions
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get all active auctions (including those created by admin) with highest bids
        $activeAuctions = Auction::where('status', AuctionStatus::ACTIVE)
            ->with(['bids' => function($query) {
                $query->where('status', BidStatus::HIGHEST);
            }, 'creator'])
            ->paginate(10);

        // Get seller's sales in progress - exclude completed and inaccessible
        $salesInProgress = SellerSale::where('seller_id', $user->id)
            ->whereNotIn('status', [SaleStatus::COMPLETED, SaleStatus::CANCELLED])
            ->whereHas('auction', function($query) {
                $query->whereNotIn('status', [AuctionStatus::COMPLETED])
                      ->where('status', '!=', AuctionStatus::CANCELLED);
            })
            ->with(['auction', 'selectedBid'])
            ->get()
            ->filter(function($sale) {
                // Additional filter: exclude sales where user has no access to continue

                // If auction is completed or cancelled, exclude
                if (in_array($sale->auction->status->value, ['completed', 'cancelled'])) {
                    return false;
                }

                // If sale is completed or cancelled, exclude
                if (in_array($sale->status->value, ['completed', 'cancelled'])) {
                    return false;
                }

                // If sale is at final step and user can't continue, exclude
                if ($sale->status === SaleStatus::TRANSFER_CONFIRMED) {
                    return false;
                }

                return true;
            });

        return view('seller.dashboard', compact('activeAuctions', 'salesInProgress'));
    }

    /**
     * Start sale process for an auction
     */
    public function startSale(Auction $auction)
    {
        $user = Auth::user();

        // Check if auction is active
        if ($auction->status !== AuctionStatus::ACTIVE) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'این مزایده فعال نیست.');
        }

        // Check if auction has at least one bid
        $highestBid = $auction->bids()->where('status', BidStatus::HIGHEST)->first();
        if (!$highestBid) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'ابتدا باید حداقل یک پیشنهاد دریافت کنید.');
        }

        // Check if this seller already has a sale for this auction
        $existingSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->whereNotIn('status', [SaleStatus::COMPLETED, SaleStatus::CANCELLED])
            ->first();
        if ($existingSale) {
            return $this->redirectToSaleStep($auction, $existingSale);
        }

        // Check if another seller has already accepted an offer (final stage)
        $acceptedOffer = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', '!=', $user->id)
            ->whereIn('status', [
                SaleStatus::OFFER_ACCEPTED,
                SaleStatus::AWAITING_BUYER_PAYMENT,
                SaleStatus::BUYER_PAYMENT_APPROVED,
                SaleStatus::LOAN_TRANSFERRED,
                SaleStatus::TRANSFER_CONFIRMED
            ])
            ->first();
        if ($acceptedOffer) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'پیشنهاد توسط فروشنده دیگری پذیرفته شده است.');
        }

        // Check if auction is locked (offer accepted)
        if ($auction->status === AuctionStatus::LOCKED) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'این مزایده قفل شده و پیشنهاد پذیرفته شده است.');
        }

        // Create new seller sale
        $sellerSale = SellerSale::create([
            'auction_id' => $auction->id,
            'seller_id' => $user->id,
            'status' => SaleStatus::INITIATED,
            'current_step' => 1,
        ]);

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
        $highestBid = $auction->bids()->where('status', BidStatus::HIGHEST)->first();

        return view('seller.sale.details', compact('auction', 'sellerSale', 'highestBid'));
    }

    /**
     * Continue to contract step (Step 1 -> Step 2)
     */
    public function continueToContract(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        return redirect()->route('seller.sale.contract', $auction);
    }

    /**
     * Show contract text (Step 2)
     */
    public function showContract(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        $contract = ContractAgreement::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('role', ContractRole::SELLER)
            ->first();

        $contractText = config('contract.seller_text', 'متن قرارداد در حال آماده‌سازی است.');

        return view('seller.sale.contract', compact('auction', 'sellerSale', 'contract', 'contractText'));
    }

    /**
     * Send contract confirmation OTP (Step 2)
     */
    public function sendContractOtp(Request $request, Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'فروش یافت نشد'], 403);
            }
            return redirect()->route('seller.dashboard')->with('error', 'فروش یافت نشد');
        }

        if ($sellerSale->current_step != 2) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'شما باید در مرحله 2 باشید. مرحله فعلی: ' . $sellerSale->current_step], 403);
            }
            return redirect()->route('seller.dashboard')->with('error', 'شما باید در مرحله 2 باشید');
        }

        try {
            // Generate and send OTP
            $kavenegarService = app(\App\Services\SMS\KavenegarService::class);
            $code = $kavenegarService->generateOTP(6);

            // Store OTP
            \App\Models\OtpCode::create([
                'phone' => $user->phone,
                'code' => $code,
                'purpose' => 'contract-confirmation',
                'expires_at' => now()->addMinutes(5),
            ]);

            // Send OTP
            $sent = $kavenegarService->sendContractOTP($user->phone, $code);

            if (!$sent) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'خطا در ارسال کد تأیید'], 500);
                }
                return back()->withErrors([
                    'otp' => 'خطا در ارسال کد تأیید. لطفاً دوباره تلاش کنید.'
                ]);
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'کد تأیید ارسال شد']);
            }
            return redirect()->route('seller.auction.show', $auction)
                ->with('success', 'کد تأیید ارسال شد.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'خطا در ارسال کد تأیید'], 500);
            }
            return redirect()->route('seller.auction.show', $auction)
                ->with('error', 'خطا در ارسال کد تأیید');
        }
    }

    /**
     * Show contract OTP verification form
     */
    public function showContractVerification(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.sale.verify-contract', compact('auction', 'sellerSale'));
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

        // Create contract agreement
        ContractAgreement::create([
            'auction_id' => $auction->id,
            'user_id' => $user->id,
            'role' => ContractRole::SELLER,
            'status' => ContractStatus::CONFIRMED,
            'confirmed_at' => now(),
        ]);

        // Update seller sale status
        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        $sellerSale->update([
            'status' => SaleStatus::CONTRACT_CONFIRMED,
            'current_step' => 3,
        ]);

        return redirect()->route('seller.sale.payment', $auction)
            ->with('success', 'قرارداد تأیید شد.');
    }

    /**
     * Show seller fee payment (Step 3) - Now uses Zarinpal
     */
    public function showPayment(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->where('status', SaleStatus::CONTRACT_CONFIRMED)
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        // Check if payment already exists and is completed
        $payment = \App\Models\Payment::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::SELLER_FEE)
            ->where('status', \App\Enums\PaymentStatus::COMPLETED)
            ->first();

        if ($payment) {
            // Payment already completed, redirect to next step
            return redirect()->route('seller.sale.bid-acceptance', $auction);
        }

        return view('seller.sale.payment', compact('auction', 'sellerSale'));
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
            ->where('type', PaymentType::SELLER_FEE)
            ->first();

        if (!$paymentReceipt) {
            return redirect()->route('seller.dashboard');
        }

        // Store the uploaded file
        $imagePath = $request->file('receipt_image')->store('payment-receipts', 'public');

        // Update payment receipt
        $paymentReceipt->update([
            'image_path' => $imagePath,
            'status' => PaymentStatus::PENDING_REVIEW,
        ]);

        return redirect()->route('seller.sale.payment', $auction)
            ->with('success', 'رسید پرداخت آپلود شد و در انتظار بررسی است.');
    }

    /**
     * Show bid acceptance (Step 4)
     */
    public function showBidAcceptance(Auction $auction)
    {
        $user = Auth::user();

        // Check if payment is approved
        $paymentReceipt = PaymentReceipt::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->where('type', PaymentType::SELLER_FEE)
            ->where('status', PaymentStatus::APPROVED)
            ->first();

        if (!$paymentReceipt) {
            return redirect()->route('seller.sale.payment', $auction)
                ->with('error', 'ابتدا باید پرداخت کارمزد تأیید شود.');
        }

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        // Get current highest bid
        $highestBid = $auction->bids()
            ->where('status', BidStatus::HIGHEST)
            ->with('buyer')
            ->first();

        if (!$highestBid) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'پیشنهادی برای پذیرش وجود ندارد.');
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
            return redirect()->route('seller.dashboard');
        }

        // Check authorization using policy
        $highestBid = $auction->bids()
            ->where('status', BidStatus::HIGHEST)
            ->first();

        if (!$highestBid || !$user->can('accept', $highestBid)) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'شما مجاز به پذیرش این پیشنهاد نیستید.');
        }

        // Business rule: Only active auctions can accept bids
        if ($auction->status !== AuctionStatus::ACTIVE) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'این مزایده دیگر فعال نیست.');
        }

        DB::transaction(function () use ($auction, $highestBid, $sellerSale) {
            // Update bid status
            $highestBid->update(['status' => BidStatus::ACCEPTED]);

            // Lock auction - prevents new bids and other sellers from accepting
            $auction->update([
                'status' => AuctionStatus::LOCKED,
                'locked_at' => now(),
            ]);

            // Update seller sale
            $sellerSale->update([
                'selected_bid_id' => $highestBid->id,
                'status' => SaleStatus::AWAITING_BUYER_PAYMENT,
                'current_step' => 4,
            ]);

            // Create loan transfer record
            LoanTransfer::create([
                'auction_id' => $auction->id,
                'seller_id' => $sellerSale->seller_id,
                'buyer_id' => $highestBid->buyer_id,
                'national_id_of_buyer' => $highestBid->buyer->national_id ?? 'تعیین نشده',
                'transfer_receipt_path' => '', // Will be filled later when seller uploads transfer receipt
            ]);

            // Update buyer progress to step 6 (purchase payment)
            $buyerProgressService = app(\App\Services\BuyerProgressService::class);
            $buyerProgressService->updateProgress($auction, $highestBid->buyer, 'purchase-payment', 6);

            // Notify buyer that their bid was accepted
            $highestBid->buyer->notify(new \App\Notifications\BidAccepted($highestBid));
        });

        // Notify admin about bid acceptance
        $this->adminNotifier->notifySellerAction('bid_accepted', $user, [
            'auction_title' => $auction->title,
            'bid_amount' => $highestBid->amount,
            'buyer_name' => $highestBid->buyer->name
        ]);

        return redirect()->route('seller.auction.show', $auction)
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
            ->where('status', SaleStatus::OFFER_ACCEPTED)
            ->with('selectedBid')
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.sale.awaiting-buyer-payment', compact('auction', 'sellerSale'));
    }

    /**
     * Get buyer payment status for AJAX polling
     */
    public function getBuyerPaymentStatus(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale || !$sellerSale->selectedBid) {
            return response()->json(['status' => 'not_found']);
        }

        // Check buyer payment status
        $buyerPayment = PaymentReceipt::where('auction_id', $auction->id)
            ->where('user_id', $sellerSale->selectedBid->buyer_id)
            ->where('type', PaymentType::BUYER_PURCHASE_AMOUNT)
            ->first();

        if (!$buyerPayment) {
            return response()->json(['status' => 'pending']);
        }

        return response()->json([
            'status' => $buyerPayment->status->value,
            'label' => $buyerPayment->status->label(),
            'color' => $buyerPayment->status->color(),
        ]);
    }

    /**
     * Show loan transfer (Step 6)
     */
    public function showLoanTransfer(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->with('selectedBid')
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        // Check if buyer payment is approved
        $buyerPayment = PaymentReceipt::where('auction_id', $auction->id)
            ->where('user_id', $sellerSale->selectedBid->buyer_id)
            ->where('type', PaymentType::BUYER_PURCHASE_AMOUNT)
            ->where('status', PaymentStatus::APPROVED)
            ->first();

        if (!$buyerPayment) {
            return redirect()->route('seller.sale.awaiting-buyer-payment', $auction);
        }

        // Get loan transfer record
        $loanTransfer = LoanTransfer::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$loanTransfer) {
            return redirect()->route('seller.dashboard');
        }

        return redirect()->route('seller.auction.show', $auction);
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

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

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
            ->where('status', SaleStatus::LOAN_TRANSFERRED)
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
     * Get transfer confirmation status for AJAX polling
     */
    public function getTransferConfirmationStatus(Auction $auction)
    {
        $user = Auth::user();

        $loanTransfer = LoanTransfer::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$loanTransfer) {
            return response()->json(['status' => 'not_found']);
        }

        $status = 'pending';
        if ($loanTransfer->buyer_confirmed_at && $loanTransfer->admin_confirmed_at) {
            $status = 'confirmed';
        } elseif ($loanTransfer->buyer_confirmed_at) {
            $status = 'buyer_confirmed';
        }

        return response()->json([
            'status' => $status,
            'buyer_confirmed' => !is_null($loanTransfer->buyer_confirmed_at),
            'admin_confirmed' => !is_null($loanTransfer->admin_confirmed_at),
        ]);
    }

    /**
     * Show sale completion (Step 8)
     */
    public function showSaleCompletion(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->where('status', SaleStatus::TRANSFER_CONFIRMED)
            ->with('selectedBid')
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.sale.completion', compact('auction', 'sellerSale'));
    }

    /**
     * Complete sale (Step 8 -> Final)
     */
    public function completeSale(Auction $auction)
    {
        $user = Auth::user();

        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->where('status', SaleStatus::TRANSFER_CONFIRMED)
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard');
        }

        DB::transaction(function () use ($auction, $sellerSale) {
            // Update seller sale
            $sellerSale->update([
                'status' => SaleStatus::COMPLETED,
                'current_step' => 6,
            ]);

            // Complete auction
            $auction->update([
                'status' => AuctionStatus::COMPLETED,
                'completed_at' => now(),
            ]);
        });

        return redirect()->route('seller.dashboard')
            ->with('success', 'فرآیند فروش با موفقیت تکمیل شد.');
    }

    /**
     * Helper method to redirect to appropriate step
     */
    private function redirectToSaleStep(Auction $auction, SellerSale $sellerSale)
    {
        switch ($sellerSale->status) {
            case SaleStatus::INITIATED:
                return redirect()->route('seller.sale.details', $auction);
            case SaleStatus::CONTRACT_CONFIRMED:
                return redirect()->route('seller.sale.payment', $auction);
            case SaleStatus::FEE_APPROVED:
                return redirect()->route('seller.sale.bid-acceptance', $auction);
            case SaleStatus::OFFER_ACCEPTED:
                return redirect()->route('seller.sale.awaiting-buyer-payment', $auction);
            case SaleStatus::BUYER_PAYMENT_APPROVED:
                return redirect()->route('seller.auction.show', $auction);
            case SaleStatus::LOAN_TRANSFERRED:
                return redirect()->route('seller.sale.awaiting-transfer-confirmation', $auction);
            case SaleStatus::TRANSFER_CONFIRMED:
                return redirect()->route('seller.sale.completion', $auction);
            case SaleStatus::COMPLETED:
                return redirect()->route('seller.dashboard');
            default:
                return redirect()->route('seller.dashboard');
        }
    }

    /**
     * Upload loan transfer receipt (Step 6)
     */
    public function uploadLoanTransfer(Request $request, Auction $auction)
    {
        $user = Auth::user();

        // Check if user has access to this auction
        $sellerSale = SellerSale::where('auction_id', $auction->id)
            ->where('seller_id', $user->id)
            ->first();

        if (!$sellerSale) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'شما دسترسی به این مزایده ندارید');
        }

        // Check if user is on step 5 (loan transfer step)
        if ($sellerSale->current_step != 5) {
            return redirect()->route('seller.auction.show', $auction)
                ->with('error', 'شما در مرحله مناسب نیستید');
        }

        // Validate the request
        $request->validate([
            'transfer_receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB
            'iban' => [
                'required',
                'string',
                'regex:/^[0-9]{24}$/',
                'size:24'
            ]
        ], [
            'transfer_receipt.required' => 'فایل فیش انتقال الزامی است',
            'transfer_receipt.file' => 'فایل معتبر انتخاب کنید',
            'transfer_receipt.mimes' => 'فرمت فایل باید JPG، PNG یا PDF باشد',
            'transfer_receipt.max' => 'حجم فایل نباید از 10 مگابایت بیشتر باشد',
            'iban.required' => 'شماره شبا الزامی است',
            'iban.regex' => 'شماره شبا باید دقیقاً 24 رقم باشد',
            'iban.size' => 'شماره شبا باید دقیقاً 24 رقم باشد',
        ]);

        try {
            // Store the receipt image
            $fileUploadService = new \App\Services\FileUploadService();
            $filePath = $fileUploadService->storeReceiptImage($request->file('transfer_receipt'), $user->id);

            // Get the accepted bid to get the amount
            $acceptedBid = $auction->bids()->where('status', \App\Enums\BidStatus::ACCEPTED)->first();
            if (!$acceptedBid) {
                return redirect()->route('seller.auction.show', $auction)
                    ->with('error', 'پیشنهاد پذیرفته شده‌ای یافت نشد');
            }

            // Update user's IBAN (now required)
            $user->update(['iban' => $request->iban]);

            // Create PaymentReceipt for loan transfer
            \App\Models\PaymentReceipt::create([
                'user_id' => $user->id,
                'auction_id' => $auction->id,
                'type' => \App\Enums\PaymentType::LOAN_TRANSFER,
                'amount' => $acceptedBid->amount,
                'iban' => $request->iban,
                'status' => \App\Enums\PaymentStatus::PENDING_REVIEW,
                'image_path' => $filePath,
            ]);

            // Update or create LoanTransfer record for reference
            $loanTransfer = LoanTransfer::where('auction_id', $auction->id)->first();
            if ($loanTransfer) {
                $loanTransfer->update([
                    'transfer_receipt_path' => $filePath,
                    'updated_at' => now(),
                ]);
            } else {
                LoanTransfer::create([
                    'auction_id' => $auction->id,
                    'seller_id' => $user->id,
                    'buyer_id' => $acceptedBid->buyer_id,
                    'national_id_of_buyer' => $acceptedBid->buyer->national_id ?? 'تعیین نشده',
                    'transfer_receipt_path' => $filePath,
                ]);
            }

            return redirect()->route('seller.auction.show', $auction)
                ->with('success', 'فیش انتقال وام با موفقیت آپلود شد و در انتظار بررسی ادمین است');

        } catch (\Exception $e) {
            \Log::error('Error uploading loan transfer receipt: ' . $e->getMessage());
            return redirect()->route('seller.auction.show', $auction)
                ->with('error', 'خطا در آپلود فیش انتقال وام');
        }
    }

    /**
     * Update seller IBAN
     */
    public function updateIban(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'iban' => [
                'required',
                'string',
                'regex:/^IR[0-9]{24}$/',
                'size:26'
            ]
        ], [
            'iban.required' => 'شماره شبا الزامی است',
            'iban.regex' => 'شماره شبا باید با IR شروع شده و 24 رقم داشته باشد',
            'iban.size' => 'شماره شبا باید دقیقاً 26 کاراکتر باشد',
        ]);

        $user->update([
            'iban' => $request->iban
        ]);

        return redirect()->back()->with('success', 'شماره شبا با موفقیت ثبت شد.');
    }
}
