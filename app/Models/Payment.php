<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'auction_id',
        'type',
        'amount',
        'description',
        'status',
        'authority',
        'ref_id',
        'gateway_url',
        'paid_at',
        'metadata',
        'first_name',
        'last_name',
        'national_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => PaymentType::class,
            'amount' => 'integer',
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === PaymentStatus::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::FAILED;
    }

    public function isCancelled(): bool
    {
        return $this->status === PaymentStatus::CANCELLED;
    }

    /**
     * Get formatted amount in Toman
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount) . ' تومان';
    }

    /**
     * Get payment type label
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }
}
