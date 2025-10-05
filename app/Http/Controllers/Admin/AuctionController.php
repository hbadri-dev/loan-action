<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Enums\AuctionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuctionController extends Controller
{
    /**
     * Display a listing of auctions
     */
    public function index(Request $request)
    {
        $query = Auction::with(['creator', 'bids' => function($q) {
            $q->where('status', 'highest');
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
     * Show the form for creating a new auction
     */
    public function create()
    {
        return view('admin.auctions.create');
    }

    /**
     * Store a newly created auction
     */
    public function store(Request $request)
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
     * Display the specified auction
     */
    public function show(Auction $auction)
    {
        $auction->load(['creator', 'bids.buyer', 'sellerSales.seller']);
        return view('admin.auctions.show', compact('auction'));
    }

    /**
     * Show the form for editing the auction
     */
    public function edit(Auction $auction)
    {
        return view('admin.auctions.edit', compact('auction'));
    }

    /**
     * Update the specified auction
     */
    public function update(Request $request, Auction $auction)
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
     * Remove the specified auction
     */
    public function destroy(Auction $auction)
    {
        if ($auction->bids()->count() > 0) {
            return redirect()->back()
                ->with('error', 'نمی‌توان مزایداتی که پیشنهاد دارند را حذف کرد.');
        }

        $auction->delete();

        return redirect()->route('admin.auctions.index')
            ->with('success', 'مزایده با موفقیت حذف شد.');
    }

    /**
     * Force delete auction completely (admin only)
     */
    public function forceDelete(Auction $auction)
    {
        try {
            // Delete all related data first
            $auction->bids()->delete();
            $auction->buyerProgress()->delete();
            $auction->sellerSales()->delete();
            $auction->contractAgreements()->delete();
            $auction->paymentReceipts()->delete();
            $auction->loanTransfers()->delete();

            // Delete payments first, then payment transactions will be deleted automatically via cascade
            $auction->payments()->delete();

            // Force delete the auction
            $auction->forceDelete();

            return redirect()->route('admin.auctions.index')
                ->with('success', 'مزایده و تمامی اطلاعات مرتبط با آن با موفقیت حذف شد.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'خطا در حذف مزایده: ' . $e->getMessage());
        }
    }

    /**
     * Toggle auction lock status
     */
    public function toggleLock(Auction $auction)
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
}
