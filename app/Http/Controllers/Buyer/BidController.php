<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBidRequest;
use App\Models\Auction;
use App\Models\Bid;
use App\Enums\AuctionStatus;
use App\Enums\BidStatus;
use App\Services\BiddingService;
use App\Services\BuyerProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    protected BiddingService $biddingService;
    protected BuyerProgressService $progressService;

    public function __construct(BiddingService $biddingService, BuyerProgressService $progressService)
    {
        $this->biddingService = $biddingService;
        $this->progressService = $progressService;
    }
    /**
     * Show bid submission form (Step 4)
     */
    public function showBidForm(Auction $auction)
    {
        $user = Auth::user();

        // Check authorization using policy
        if (!$user->can('create', [Bid::class, $auction])) {
            abort(403, 'شما مجاز به ثبت پیشنهاد در این مزایده نیستید.');
        }

        // Redirect to the main auction details page which now handles step 4
        return redirect()->route('buyer.auction.show', $auction);
    }

    /**
     * Submit bid (Step 4 -> Step 5)
     */
    public function submitBid(StoreBidRequest $request, Auction $auction)
    {
        $user = Auth::user();

        // Check authorization using policy
        if (!$user->can('create', [Bid::class, $auction])) {
            abort(403, 'شما مجاز به ثبت پیشنهاد در این مزایده نیستید.');
        }

        // Server-side enforcement: Check if auction is locked
        if ($auction->isLocked()) {
            abort(422, 'مزایده قفل است.');
        }

        try {
            $bid = $this->biddingService->placeBid($auction, $user, $request->amount);

            // Update buyer progress to step 4 (waiting for seller)
            $this->progressService->updateProgress($auction, $user, 'waiting-seller', 4);

            $message = $auction->bids()->where('buyer_id', $user->id)->count() > 1
                ? 'پیشنهاد شما بروزرسانی شد.'
                : 'پیشنهاد شما ثبت شد.';

            return redirect()->route('buyer.auction.details', $auction)
                ->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Bid submission failed', [
                'user_id' => $user->id,
                'auction_id' => $auction->id,
                'amount' => $request->amount,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'amount' => $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Get bid status for polling (Step 5)
     */
    public function getBidStatus(Auction $auction)
    {
        $user = Auth::user();

        $userBid = $auction->bids()
            ->where('buyer_id', $user->id)
            ->latest()
            ->first();

        if (!$userBid) {
            return response()->json(['status' => 'not_found']);
        }

        $response = [
            'status' => $userBid->status->value,
            'amount' => $userBid->amount,
        ];

        // If bid is accepted, redirect to next step
        if ($userBid->status === BidStatus::ACCEPTED) {
            $response['redirect'] = route('buyer.auction.purchase-payment', $auction);
        }

        return response()->json($response);
    }
}
