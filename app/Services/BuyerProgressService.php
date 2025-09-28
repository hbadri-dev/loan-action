<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\BuyerProgress;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BuyerProgressService
{
    /**
     * Create or update buyer progress
     */
    public function updateProgress(Auction $auction, User $user, string $stepName, int $stepNumber, array $stepData = []): BuyerProgress
    {
        return BuyerProgress::updateOrCreate(
            [
                'auction_id' => $auction->id,
                'user_id' => $user->id,
            ],
            [
                'current_step' => $stepNumber,
                'step_name' => $stepName,
                'step_data' => $stepData,
                'last_activity_at' => now(),
            ]
        );
    }

    /**
     * Initialize step 1 (details) for first-time auction participation
     */
    public function initializeStep1(Auction $auction, User $user): BuyerProgress
    {
        return $this->updateProgress($auction, $user, 'details', 1, [
            'joined_at' => now(),
            'participation_initiated' => true,
        ]);
    }

    /**
     * Mark progress as completed
     */
    public function markCompleted(Auction $auction, User $user): BuyerProgress
    {
        $progress = BuyerProgress::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->first();

        if ($progress) {
            $progress->update([
                'step_name' => 'complete',
                'current_step' => 8,  // Updated to reflect new step count
                'is_completed' => true,
                'last_activity_at' => now(),
            ]);
        }

        return $progress;
    }

    /**
     * Get user's progress for an auction
     */
    public function getProgress(Auction $auction, User $user): ?BuyerProgress
    {
        return BuyerProgress::where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * Get all user's auction progress
     */
    public function getUserProgress(User $user = null): \Illuminate\Database\Eloquent\Collection
    {
        $user = $user ?? Auth::user();

        return BuyerProgress::where('user_id', $user->id)
            ->with(['auction', 'auction.creator'])
            ->orderBy('last_activity_at', 'desc')
            ->get();
    }

    /**
     * Check if user can access a specific step
     */
    public function canAccessStep(Auction $auction, User $user, string $targetStepName): bool
    {
        $progress = $this->getProgress($auction, $user);

        if (!$progress) {
            return $targetStepName === 'details';
        }

        $stepOrder = [
            'details' => 1,
            'payment' => 2,  // Contract step removed, payment is now step 2
            'bid' => 3,      // Bid is now step 3
            'waiting-seller' => 4,
            'purchase-payment' => 5,
            'awaiting-seller-transfer' => 6,
            'confirm-transfer' => 7,
            'complete' => 8,
        ];

        $currentStepNumber = $stepOrder[$progress->step_name] ?? 1;
        $targetStepNumber = $stepOrder[$targetStepName] ?? 1;

        // User can access current step and previous steps
        return $targetStepNumber <= $currentStepNumber + 1;
    }

    /**
     * Get the appropriate redirect route based on progress
     */
    public function getRedirectRoute(Auction $auction, User $user): string
    {
        $progress = $this->getProgress($auction, $user);

        if (!$progress || $progress->is_completed) {
            return route('buyer.auction.details', $auction);
        }

        return match($progress->step_name) {
            'details' => route('buyer.auction.details', $auction),
            'contract' => route('buyer.auction.contract', $auction),
            'payment' => route('buyer.auction.payment', $auction),
            'bid' => route('buyer.auction.bid', $auction),
            'waiting-seller' => route('buyer.auction.waiting-seller', $auction),
            'purchase-payment' => route('buyer.auction.purchase-payment', $auction),
            'awaiting-seller-transfer' => route('buyer.auction.awaiting-seller-transfer', $auction),
            'confirm-transfer' => route('buyer.auction.confirm-transfer', $auction),
            'complete' => route('buyer.auction.complete', $auction),
            default => route('buyer.auction.details', $auction),
        };
    }
}
