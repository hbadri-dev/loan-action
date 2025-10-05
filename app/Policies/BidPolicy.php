<?php

namespace App\Policies;

use App\Models\Bid;
use App\Models\User;
use App\Models\Auction;
use Illuminate\Auth\Access\HandlesAuthorization;

class BidPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bid $bid): bool
    {
        // Users can view their own bids, auction owner can view bids on their auction, admin can view all
        return $bid->buyer_id === $user->id ||
               $bid->auction->created_by === $user->id ||
               $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Auction $auction): bool
    {
        // Must be buyer, auction must be active, user can't bid on their own auction
        if (!$user->hasRole('buyer') ||
            $auction->status->value !== 'active' ||
            $auction->created_by === $user->id) {
            return false;
        }

        // Contract step removed - no need to check contract confirmation
        // Payment step removed - no need to check buyer fee payment

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bid $bid): bool
    {
        return $bid->buyer_id === $user->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bid $bid): bool
    {
        return $bid->buyer_id === $user->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can accept the bid (seller only).
     */
    public function accept(User $user, Bid $bid): bool
    {
        // Any seller can accept bids on active auctions
        return $user->hasRole('seller') &&
               $bid->auction->status->value === 'active';
    }
}
