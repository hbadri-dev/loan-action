<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\Payment;
use App\Models\LoanTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get all auctions where user has bids
        $auctions = Auction::whereHas('bids', function ($query) use ($user) {
            $query->where('buyer_id', $user->id);
        })
        ->with([
            'bids' => function ($query) use ($user) {
                $query->where('buyer_id', $user->id);
            },
            'payments' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            },
            'loanTransfers' => function ($query) use ($user) {
                $query->where('buyer_id', $user->id);
            }
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('buyer.loan-history.index', compact('auctions'));
    }

    public function show(Auction $auction)
    {
        $user = Auth::user();

        // Check if user has access to this auction
        $hasAccess = $auction->bids()->where('buyer_id', $user->id)->exists();

        if (!$hasAccess) {
            abort(403, 'شما دسترسی به این مزایده ندارید');
        }

        // Get user's bid for this auction
        $userBid = $auction->bids()->where('buyer_id', $user->id)->first();

        // Get user's payments for this auction
        $payments = $auction->payments()->where('user_id', $user->id)->get();

        // Get loan transfer if exists
        $loanTransfer = $auction->loanTransfers()->where('buyer_id', $user->id)->first();

        return view('buyer.loan-history.show', compact('auction', 'userBid', 'payments', 'loanTransfer'));
    }
}
