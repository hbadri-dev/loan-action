<?php

namespace App\Services;

use App\Enums\AuctionStatus;
use App\Enums\BidStatus;
use App\Enums\ContractStatus;
use App\Enums\PaymentStatus;
use App\Events\BidPlaced;
use App\Events\BidOutbid;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\ContractAgreement;
use App\Models\PaymentReceipt;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BiddingService
{
    /**
     * Get the highest bid for an auction
     */
    public function getHighestBid(Auction $auction): ?Bid
    {
        return $auction->bids()
            ->where('status', BidStatus::HIGHEST)
            ->first();
    }

    /**
     * Place a bid on an auction with atomic transaction
     *
     * @throws \Exception
     */
    public function placeBid(Auction $auction, User $buyer, int $amount): Bid
    {
        return DB::transaction(function () use ($auction, $buyer, $amount) {
            // Lock the auction for update to prevent race conditions
            $auction = Auction::lockForUpdate()->findOrFail($auction->id);

            // Check auction prerequisites
            $this->validateAuctionForBidding($auction);

            // Check buyer prerequisites
            $this->validateBuyerPrerequisites($buyer, $auction);

            // Validate bid amount
            $this->validateBidAmount($auction, $amount);

            // Get current highest bid
            $currentHighest = $this->getHighestBid($auction);

            // Check if user already has a bid
            $existingBid = Bid::where('buyer_id', $buyer->id)
                ->where('auction_id', $auction->id)
                ->first();

            if ($existingBid) {
                // Update existing bid
                $existingBid->update(['amount' => $amount]);
                $newBid = $existingBid;
            } else {
                // Create new bid
                $newBid = Bid::create([
                    'auction_id' => $auction->id,
                    'buyer_id' => $buyer->id,
                    'amount' => $amount,
                    'status' => BidStatus::PENDING,
                ]);
            }

            // Update bid statuses atomically
            if ($currentHighest && $currentHighest->id !== $newBid->id) {
                // Demote previous highest bid
                $currentHighest->update(['status' => BidStatus::OUTBID]);

                // Fire outbid event
                event(new BidOutbid($currentHighest, $newBid));
            }

            // Set new bid as highest
            $newBid->update(['status' => BidStatus::HIGHEST]);

            // Fire bid placed event
            event(new BidPlaced($newBid));

            Log::info('Bid placed successfully', [
                'auction_id' => $auction->id,
                'buyer_id' => $buyer->id,
                'amount' => $amount,
                'previous_highest' => $currentHighest?->amount,
            ]);

            return $newBid->fresh();
        });
    }

    /**
     * Validate auction is available for bidding
     *
     * @throws \Exception
     */
    private function validateAuctionForBidding(Auction $auction): void
    {
        if ($auction->status !== AuctionStatus::ACTIVE) {
            throw new \Exception('مزایده در حال حاضر فعال نیست.');
        }

        if ($auction->isLocked()) {
            throw new \Exception('مزایده قفل است.');
        }
    }

    /**
     * Validate buyer prerequisites for bidding
     *
     * @throws \Exception
     */
    private function validateBuyerPrerequisites(User $buyer, Auction $auction): void
    {
        // Note: Buyers can update their bids, so we don't restrict multiple bids

        // Check if buyer has confirmed contract for this auction
        $contract = ContractAgreement::where('user_id', $buyer->id)
            ->where('auction_id', $auction->id)
            ->where('role', \App\Enums\ContractRole::BUYER)
            ->where('status', ContractStatus::CONFIRMED)
            ->first();

        if (!$contract) {
            throw new \Exception('برای شرکت در مزایده باید قرارداد را تأیید کنید.');
        }

        // Check if buyer has approved fee payment
        $feePayment = PaymentReceipt::where('user_id', $buyer->id)
            ->where('auction_id', $auction->id)
            ->where('type', \App\Enums\PaymentType::BUYER_FEE)
            ->where('status', PaymentStatus::APPROVED)
            ->first();

        if (!$feePayment) {
            throw new \Exception('برای شرکت در مزایده باید کارمزد خریدار را پرداخت کرده باشید.');
        }
    }

    /**
     * Validate bid amount against auction requirements
     *
     * @throws \Exception
     */
    private function validateBidAmount(Auction $auction, int $amount): void
    {
        if ($amount <= $auction->min_purchase_price) {
            throw new \Exception(
                'مبلغ پیشنهادی باید بیشتر از حداقل قیمت خرید (' .
                number_format($auction->min_purchase_price) . ' تومان) باشد.'
            );
        }

        $currentHighest = $this->getHighestBid($auction);
        if ($currentHighest && $amount <= $currentHighest->amount) {
            throw new \Exception(
                'مبلغ پیشنهادی باید بیشتر از بالاترین پیشنهاد فعلی (' .
                number_format($currentHighest->amount) . ' تومان) باشد.'
            );
        }
    }

    /**
     * Get buyer's bids for an auction
     */
    public function getBuyerBids(Auction $auction, User $buyer): \Illuminate\Database\Eloquent\Collection
    {
        return $auction->bids()
            ->where('buyer_id', $buyer->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if buyer can place a bid
     */
    public function canPlaceBid(Auction $auction, User $buyer): array
    {
        $canBid = true;
        $reasons = [];

        try {
            $this->validateAuctionForBidding($auction);
        } catch (\Exception $e) {
            $canBid = false;
            $reasons[] = $e->getMessage();
        }

        try {
            $this->validateBuyerPrerequisites($buyer, $auction);
        } catch (\Exception $e) {
            $canBid = false;
            $reasons[] = $e->getMessage();
        }

        return [
            'can_bid' => $canBid,
            'reasons' => $reasons,
        ];
    }
}
