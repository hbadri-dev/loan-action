<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\SellerSale;
use App\Models\Payment;
use App\Models\LoanTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get all auctions where user is involved (either as creator or has seller sales)
        $auctions = Auction::where(function($query) use ($user) {
                $query->where('created_by', $user->id)
                      ->orWhereHas('sellerSales', function($q) use ($user) {
                          $q->where('seller_id', $user->id);
                      });
            })
            ->with([
                'sellerSales' => function($query) use ($user) {
                    $query->where('seller_id', $user->id);
                },
                'bids' => function ($query) {
                    $query->where('status', \App\Enums\BidStatus::ACCEPTED);
                },
                'payments',
                'loanTransfers'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('seller.loan-history.index', compact('auctions'));
    }

    public function show(Auction $auction)
    {
        $user = Auth::user();

        // Check if user has access to this auction (either as creator or has seller sales)
        $hasAccess = $auction->created_by === $user->id ||
                     $auction->sellerSales()->where('seller_id', $user->id)->exists();

        if (!$hasAccess) {
            abort(403, 'شما دسترسی به این مزایده ندارید');
        }

        // Get seller sale for this auction
        $sellerSale = $auction->sellerSales()->where('seller_id', $user->id)->first();

        // Get accepted bid
        $acceptedBid = $auction->bids()->where('status', \App\Enums\BidStatus::ACCEPTED)->first();

        // Get all payments for this auction
        $payments = $auction->payments()->get();

        // Get loan transfer if exists
        $loanTransfer = $auction->loanTransfers()->first();

        return view('seller.loan-history.show', compact('auction', 'sellerSale', 'acceptedBid', 'payments', 'loanTransfer'));
    }
}
