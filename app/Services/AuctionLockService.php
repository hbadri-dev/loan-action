<?php

namespace App\Services;

use App\Enums\AuctionStatus;
use App\Enums\BidStatus;
use App\Enums\SaleStatus;
use App\Events\BidAccepted;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\LoanTransfer;
use App\Models\SellerSale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuctionLockService
{
    /**
     * Lock auction when seller accepts a bid
     *
     * @throws \Exception
     */
    public function lockOnAcceptance(Auction $auction, Bid $bid): array
    {
        return DB::transaction(function () use ($auction, $bid) {
            // Lock the auction for update
            $auction = Auction::lockForUpdate()->findOrFail($auction->id);
            $bid = Bid::lockForUpdate()->findOrFail($bid->id);

            // Validate auction and bid
            $this->validateAcceptance($auction, $bid);

            // Update auction status (this will automatically set is_locked = true)
            $auction->update([
                'status' => AuctionStatus::LOCKED,
                'locked_at' => now(),
            ]);

            // Update bid status to accepted
            $bid->update(['status' => BidStatus::ACCEPTED]);

            // Set all other bids to rejected
            Bid::where('auction_id', $auction->id)
                ->where('id', '!=', $bid->id)
                ->update(['status' => BidStatus::REJECTED]);

            // Update or create seller sale
            $sellerSale = SellerSale::updateOrCreate(
                [
                    'auction_id' => $auction->id,
                    'user_id' => $auction->user_id, // Seller
                ],
                [
                    'selected_bid_id' => $bid->id,
                    'status' => SaleStatus::OFFER_ACCEPTED,
                ]
            );

            // Create loan transfer record
            $loanTransfer = LoanTransfer::create([
                'auction_id' => $auction->id,
                'buyer_id' => $bid->user_id,
                'seller_id' => $auction->user_id,
                'amount' => $bid->amount,
            ]);

            // Fire bid accepted event
            event(new BidAccepted($bid, $auction));

            Log::info('Auction locked on bid acceptance', [
                'auction_id' => $auction->id,
                'bid_id' => $bid->id,
                'buyer_id' => $bid->user_id,
                'seller_id' => $auction->user_id,
                'amount' => $bid->amount,
            ]);

            return [
                'auction' => $auction->fresh(),
                'bid' => $bid->fresh(),
                'seller_sale' => $sellerSale,
                'loan_transfer' => $loanTransfer,
            ];
        });
    }

    /**
     * Validate that auction and bid can be accepted
     *
     * @throws \Exception
     */
    private function validateAcceptance(Auction $auction, Bid $bid): void
    {
        // Check if auction is active
        if ($auction->status !== AuctionStatus::ACTIVE) {
            throw new \Exception('مزایده در حال حاضر فعال نیست.');
        }

        // Check if auction is already locked
        if ($auction->is_locked) {
            throw new \Exception('مزایده قبلاً قفل شده است.');
        }

        // Check if bid belongs to this auction
        if ($bid->auction_id !== $auction->id) {
            throw new \Exception('پیشنهاد متعلق به این مزایده نیست.');
        }

        // Check if bid is the highest
        if ($bid->status !== BidStatus::HIGHEST) {
            throw new \Exception('فقط بالاترین پیشنهاد قابل قبول است.');
        }
    }

    /**
     * Unlock auction (admin function)
     *
     * @throws \Exception
     */
    public function unlock(Auction $auction, string $reason = null): Auction
    {
        return DB::transaction(function () use ($auction, $reason) {
            $auction = Auction::lockForUpdate()->findOrFail($auction->id);

            if ($auction->status !== AuctionStatus::LOCKED) {
                throw new \Exception('فقط مزایده‌های قفل شده قابل بازگشایی هستند.');
            }

            // Update auction status (this will automatically set is_locked = false)
            $auction->update([
                'status' => AuctionStatus::ACTIVE,
                'locked_at' => null,
            ]);

            // Reset accepted bid status
            $acceptedBid = $auction->bids()
                ->where('status', BidStatus::ACCEPTED)
                ->first();

            if ($acceptedBid) {
                $acceptedBid->update(['status' => BidStatus::HIGHEST]);
            }

            // Update seller sale status
            $sellerSale = SellerSale::where('auction_id', $auction->id)->first();
            if ($sellerSale) {
                $sellerSale->update(['status' => SaleStatus::INITIATED]);
            }

            Log::info('Auction unlocked', [
                'auction_id' => $auction->id,
                'reason' => $reason,
            ]);

            return $auction->fresh();
        });
    }

    /**
     * Force lock auction (admin function)
     *
     * @throws \Exception
     */
    public function forceLock(Auction $auction, string $reason = null): Auction
    {
        return DB::transaction(function () use ($auction, $reason) {
            $auction = Auction::lockForUpdate()->findOrFail($auction->id);

            if ($auction->status !== AuctionStatus::ACTIVE) {
                throw new \Exception('فقط مزایده‌های فعال قابل قفل اجباری هستند.');
            }

            // Update auction status (this will automatically set is_locked = true)
            $auction->update([
                'status' => AuctionStatus::LOCKED,
                'locked_at' => now(),
            ]);

            Log::info('Auction force locked', [
                'auction_id' => $auction->id,
                'reason' => $reason,
            ]);

            return $auction->fresh();
        });
    }

    /**
     * Check if auction is locked
     */
    public function isLocked(Auction $auction): bool
    {
        return $auction->isLocked();
    }

    /**
     * Get lock information for an auction
     */
    public function getLockInfo(Auction $auction): array
    {
        $acceptedBid = $auction->bids()
            ->where('status', BidStatus::ACCEPTED)
            ->with('user')
            ->first();

        $sellerSale = SellerSale::where('auction_id', $auction->id)->first();

        return [
            'is_locked' => $this->isLocked($auction),
            'locked_at' => $auction->locked_at,
            'accepted_bid' => $acceptedBid,
            'seller_sale' => $sellerSale,
        ];
    }
}
