<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanTransfer;
use App\Models\SellerSale;
use App\Models\Auction;
use App\Enums\AuctionStatus;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    /**
     * Display a listing of loan transfers
     */
    public function index(Request $request)
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
     * Display the specified loan transfer
     */
    public function show(LoanTransfer $transfer)
    {
        $transfer->load(['seller', 'buyer', 'auction']);
        return view('admin.loan-transfers.show', compact('transfer'));
    }

    /**
     * Approve loan transfer
     */
    public function approve(LoanTransfer $transfer)
    {
        $transfer->update([
            'admin_confirmed_at' => now(),
        ]);

        // Update related sale status
        $sellerSale = SellerSale::where('auction_id', $transfer->auction_id)->first();
        if ($sellerSale) {
            $sellerSale->update(['status' => \App\Enums\SaleStatus::TRANSFER_CONFIRMED]);
        }

        return redirect()->back()->with('success', 'انتقال وام تأیید شد.');
    }

    /**
     * Complete sale after transfer confirmation
     */
    public function completeSale(SellerSale $sale)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($sale) {
            $sale->update(['status' => \App\Enums\SaleStatus::COMPLETED]);
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

