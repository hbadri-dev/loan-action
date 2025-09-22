<?php

namespace App\Policies;

use App\Models\SellerSale;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SellerSalePolicy
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
    public function view(User $user, SellerSale $sellerSale): bool
    {
        return $sellerSale->seller_id === $user->id ||
               $sellerSale->selectedBid?->buyer_id === $user->id ||
               $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('seller');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SellerSale $sellerSale): bool
    {
        return $sellerSale->seller_id === $user->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SellerSale $sellerSale): bool
    {
        return $user->hasRole('admin');
    }
}

