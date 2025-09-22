<?php

namespace App\Policies;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuctionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view active auctions
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Auction $auction): bool
    {
        // Users can view active auctions, or their own auctions
        return $auction->status->value === 'active' || $auction->created_by === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('seller') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Auction $auction): bool
    {
        return $auction->created_by === $user->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Auction $auction): bool
    {
        return $auction->created_by === $user->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can bid on the auction.
     */
    public function bid(User $user, Auction $auction): bool
    {
        // Must be buyer, auction must be active, user can't bid on their own auction
        return $user->hasRole('buyer') &&
               $auction->status->value === 'active' &&
               $auction->created_by !== $user->id;
    }

    /**
     * Determine whether the user can start sale process.
     */
    public function startSale(User $user, Auction $auction): bool
    {
        // Sellers can start sale on any active auction with bids
        return $user->hasRole('seller') &&
               $auction->status->value === 'active' &&
               $auction->bids()->count() > 0;
    }
}

