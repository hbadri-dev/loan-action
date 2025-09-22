<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\SellerSale;
use App\Models\ContractAgreement;
use App\Models\PaymentReceipt;
use App\Models\LoanTransfer;
use App\Models\User;
use App\Enums\AuctionStatus;
use App\Enums\BidStatus;
use App\Enums\SaleStatus;
use App\Enums\ContractStatus;
use App\Enums\PaymentType;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_auctions' => Auction::count(),
            'active_auctions' => Auction::where('status', AuctionStatus::ACTIVE)->count(),
            'locked_auctions' => Auction::where('status', AuctionStatus::LOCKED)->count(),
            'completed_auctions' => Auction::where('status', AuctionStatus::COMPLETED)->count(),
            'total_bids' => Bid::count(),
            'pending_payments' => PaymentReceipt::where('status', PaymentStatus::PENDING_REVIEW)->count(),
            'active_sales' => SellerSale::whereNotIn('status', [SaleStatus::COMPLETED, SaleStatus::CANCELLED])->count(),
            'pending_transfers' => LoanTransfer::whereNull('admin_confirmed_at')->count(),
        ];

        // Recent activities
        $recentAuctions = Auction::with('creator')->latest()->limit(5)->get();
        $recentBids = Bid::with(['auction', 'buyer'])->latest()->limit(5)->get();
        $recentPayments = PaymentReceipt::with(['user', 'auction'])->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentAuctions', 'recentBids', 'recentPayments'));
    }

    /**
     * Show auctions list
     */
    public function auctions(Request $request)
    {
        $query = Auction::with(['creator', 'bids' => function($q) {
            $q->where('status', BidStatus::HIGHEST);
        }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $auctions = $query->latest()->paginate(15);

        return view('admin.auctions.index', compact('auctions'));
    }

    /**
     * Show create auction form
     */
    public function createAuction()
    {
        return view('admin.auctions.create');
    }

    /**
     * Store new auction
     */
    public function storeAuction(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'loan_type' => 'required|in:personal,commercial',
            'principal_amount' => 'required|integer|min:1000000',
            'term_months' => 'required|integer|min:1|max:60',
            'interest_rate_percent' => 'required|numeric|min:0|max:100',
            'min_purchase_price' => 'required|integer|min:1000000',
        ]);

        Auction::create([
            'title' => $request->title,
            'description' => $request->description,
            'loan_type' => $request->loan_type,
            'principal_amount' => $request->principal_amount,
            'term_months' => $request->term_months,
            'interest_rate_percent' => $request->interest_rate_percent,
            'min_purchase_price' => $request->min_purchase_price,
            'status' => AuctionStatus::ACTIVE,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.auctions.index')
            ->with('success', 'مزایده با موفقیت ایجاد شد.');
    }

    /**
     * Show edit auction form
     */
    public function editAuction(Auction $auction)
    {
        return view('admin.auctions.edit', compact('auction'));
    }

    /**
     * Update auction
     */
    public function updateAuction(Request $request, Auction $auction)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'loan_type' => 'required|in:personal,commercial',
            'principal_amount' => 'required|integer|min:1000000',
            'term_months' => 'required|integer|min:1|max:60',
            'interest_rate_percent' => 'required|numeric|min:0|max:100',
            'min_purchase_price' => 'required|integer|min:1000000',
            'status' => 'required|in:active,locked,completed,cancelled',
        ]);

        $auction->update($request->all());

        return redirect()->route('admin.auctions.index')
            ->with('success', 'مزایده با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Global auction lock/unlock
     */
    public function toggleAuctionLock(Request $request, Auction $auction)
    {
        $newStatus = $auction->status === AuctionStatus::ACTIVE
            ? AuctionStatus::LOCKED
            : AuctionStatus::ACTIVE;

        $auction->update([
            'status' => $newStatus,
            'locked_at' => $newStatus === AuctionStatus::LOCKED ? now() : null,
        ]);

        $message = $newStatus === AuctionStatus::LOCKED
            ? 'مزایده قفل شد.'
            : 'مزایده باز شد.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Show payment receipts
     */
    public function paymentReceipts(Request $request)
    {
        $query = PaymentReceipt::with(['user', 'auction', 'reviewer']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $receipts = $query->latest()->paginate(15);

        return view('admin.payment-receipts.index', compact('receipts'));
    }

    /**
     * Show payment receipt details
     */
    public function showPaymentReceipt(PaymentReceipt $receipt)
    {
        $receipt->load(['user', 'auction', 'reviewer']);
        return view('admin.payment-receipts.show', compact('receipt'));
    }

    /**
     * Approve payment receipt
     */
    public function approvePaymentReceipt(Request $request, PaymentReceipt $receipt)
    {
        $receipt->update([
            'status' => PaymentStatus::APPROVED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Update related sale status if it's a buyer fee payment
        if ($receipt->type === PaymentType::BUYER_FEE) {
            $sellerSale = SellerSale::where('auction_id', $receipt->auction_id)->first();
            if ($sellerSale) {
                $sellerSale->update(['status' => SaleStatus::FEE_APPROVED]);
            }
        }

        // Update related sale status if it's a buyer purchase payment
        if ($receipt->type === PaymentType::BUYER_PURCHASE_AMOUNT) {
            $sellerSale = SellerSale::where('auction_id', $receipt->auction_id)->first();
            if ($sellerSale) {
                $sellerSale->update([
                    'status' => SaleStatus::BUYER_PAYMENT_APPROVED,
                    'current_step' => 6,
                ]);
            }
        }

        // Notify user of approval
        $receipt->user->notify(new \App\Notifications\PaymentReceiptApproved($receipt));

        return redirect()->back()->with('success', 'رسید پرداخت تأیید شد.');
    }

    /**
     * Reject payment receipt
     */
    public function rejectPaymentReceipt(Request $request, PaymentReceipt $receipt)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $receipt->update([
            'status' => PaymentStatus::REJECTED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'reject_reason' => $request->reject_reason,
        ]);

        // Notify user of rejection
        $receipt->user->notify(new \App\Notifications\PaymentReceiptRejected($receipt));

        return redirect()->back()->with('success', 'رسید پرداخت رد شد.');
    }

    /**
     * Show contracts
     */
    public function contracts(Request $request)
    {
        $query = ContractAgreement::with(['user', 'auction']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $contracts = $query->latest()->paginate(15);

        return view('admin.contracts.index', compact('contracts'));
    }

    /**
     * Show bids/offers
     */
    public function bids(Request $request)
    {
        $query = Bid::with(['buyer', 'auction']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('auction_id')) {
            $query->where('auction_id', $request->auction_id);
        }

        $bids = $query->latest()->paginate(15);

        // Get auctions for filter dropdown
        $auctions = Auction::where('status', AuctionStatus::ACTIVE)->get();

        return view('admin.bids.index', compact('bids', 'auctions'));
    }

    /**
     * Reject bid
     */
    public function rejectBid(Request $request, Bid $bid)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($bid, $request) {
            $bid->update([
                'status' => BidStatus::REJECTED,
                'reject_reason' => $request->reject_reason,
            ]);

            // If this was the highest bid, find the next highest
            if ($bid->status === BidStatus::HIGHEST) {
                $nextHighest = Bid::where('auction_id', $bid->auction_id)
                    ->where('id', '!=', $bid->id)
                    ->where('status', BidStatus::PENDING)
                    ->orderBy('amount', 'desc')
                    ->first();

                if ($nextHighest) {
                    $nextHighest->update(['status' => BidStatus::HIGHEST]);
                }
            }
        });

        return redirect()->back()->with('success', 'پیشنهاد رد شد.');
    }

    /**
     * Show sales
     */
    public function sales(Request $request)
    {
        $query = SellerSale::with(['seller', 'auction', 'selectedBid']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sales = $query->latest()->paginate(15);

        return view('admin.sales.index', compact('sales'));
    }

    /**
     * Show loan transfers
     */
    public function loanTransfers(Request $request)
    {
        $query = LoanTransfer::with(['seller', 'buyer', 'auction']);

        if ($request->filled('status')) {
            if ($request->status === 'pending_admin') {
                $query->whereNull('admin_confirmed_at');
            } elseif ($request->status === 'confirmed') {
                $query->whereNotNull('admin_confirmed_at');
            }
        }

        $transfers = $query->latest()->paginate(15);

        return view('admin.loan-transfers.index', compact('transfers'));
    }

    /**
     * Show loan transfer details
     */
    public function showLoanTransfer(LoanTransfer $transfer)
    {
        $transfer->load(['seller', 'buyer', 'auction']);
        return view('admin.loan-transfers.show', compact('transfer'));
    }

    /**
     * Approve loan transfer
     */
    public function approveLoanTransfer(LoanTransfer $transfer)
    {
        $transfer->update([
            'admin_confirmed_at' => now(),
        ]);

        // Update related sale status
        $sellerSale = SellerSale::where('auction_id', $transfer->auction_id)->first();
        if ($sellerSale) {
            $sellerSale->update(['status' => SaleStatus::TRANSFER_CONFIRMED]);
        }

        return redirect()->back()->with('success', 'انتقال وام تأیید شد.');
    }

    /**
     * Complete sale
     */
    public function completeSale(SellerSale $sale)
    {
        DB::transaction(function () use ($sale) {
            $sale->update(['status' => SaleStatus::COMPLETED]);
            $sale->auction->update([
                'status' => AuctionStatus::COMPLETED,
                'completed_at' => now(),
            ]);

            // Notify both buyer and seller
            $sale->seller->notify(new \App\Notifications\SaleCompleted($sale));
            $sale->selectedBid->buyer->notify(new \App\Notifications\SaleCompleted($sale));
        });

        return redirect()->back()->with('success', 'فروش تکمیل شد.');
    }
}
