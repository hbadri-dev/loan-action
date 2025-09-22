<?php

namespace App\Policies;

use App\Models\PaymentReceipt;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentReceiptPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PaymentReceipt $paymentReceipt): bool
    {
        return $paymentReceipt->user_id === $user->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('buyer') || $user->hasRole('seller');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PaymentReceipt $paymentReceipt): bool
    {
        // Users can update their own receipts if not approved, admin can update all
        return ($paymentReceipt->user_id === $user->id &&
                $paymentReceipt->status->value === 'rejected') ||
               $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PaymentReceipt $paymentReceipt): bool
    {
        return $user->hasRole('admin');
    }
}

