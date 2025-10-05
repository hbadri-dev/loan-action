<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerProgress extends Model
{
    protected $fillable = [
        'auction_id',
        'user_id',
        'current_step',
        'step_name',
        'is_completed',
        'step_data',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'current_step' => 'integer',
            'is_completed' => 'boolean',
            'step_data' => 'array',
            'last_activity_at' => 'datetime',
        ];
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get step display name in Persian
     */
    public function getStepDisplayNameAttribute(): string
    {
        return match($this->step_name) {
            'details' => 'جزئیات وام',
            'payment' => 'پرداخت کارمزد',
            'bid' => 'ثبت پیشنهاد',
            'waiting-seller' => 'انتظار تأیید فروشنده',
            'purchase-payment' => 'پرداخت مبلغ خرید',
            'awaiting-seller-transfer' => 'انتظار انتقال فروشنده',
            'confirm-transfer' => 'تأیید انتقال وام',
            'complete' => 'تکمیل شده',
            default => $this->step_name,
        };
    }

    /**
     * Check if progress is at a specific step
     */
    public function isAtStep(string $stepName): bool
    {
        return $this->step_name === $stepName;
    }

    /**
     * Check if progress is completed
     */
    public function isCompleted(): bool
    {
        return $this->is_completed;
    }

    /**
     * Get the next step route
     */
    public function getNextStepRouteAttribute(): ?string
    {
        if ($this->is_completed) {
            return null;
        }

        return match($this->step_name) {
            'details' => route('buyer.auction.contract', $this->auction),
            'contract' => route('buyer.auction.payment', $this->auction),
            'payment' => route('buyer.auction.bid', $this->auction),
            'bid' => route('buyer.auction.waiting-seller', $this->auction),
            'waiting-seller' => route('buyer.auction.purchase-payment', $this->auction),
            'purchase-payment' => route('buyer.auction.awaiting-seller-transfer', $this->auction),
            'awaiting-seller-transfer' => route('buyer.auction.confirm-transfer', $this->auction),
            'confirm-transfer' => route('buyer.auction.complete', $this->auction),
            default => null,
        };
    }

    /**
     * Get the current step route
     */
    public function getCurrentStepRouteAttribute(): ?string
    {
        if ($this->is_completed) {
            return route('buyer.auction.complete', $this->auction);
        }

        return match($this->step_name) {
            'details' => route('buyer.auction.show', $this->auction),
            'contract' => route('buyer.auction.show', $this->auction),
            'payment' => route('buyer.auction.show', $this->auction),
            'bid' => route('buyer.auction.show', $this->auction),
            'waiting-seller' => route('buyer.auction.show', $this->auction),
            'purchase-payment' => route('buyer.auction.show', $this->auction),
            'awaiting-seller-transfer' => route('buyer.auction.show', $this->auction),
            'confirm-transfer' => route('buyer.auction.show', $this->auction),
            'complete' => route('buyer.auction.show', $this->auction),
            default => route('buyer.auction.show', $this->auction),
        };
    }
}
