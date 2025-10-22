<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'user_id',
        'type',
        'amount',
        'card_last4',
        'iban',
        'image_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'reject_reason',
        'full_name',
        'national_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => PaymentType::class,
            'amount' => 'integer',
            'status' => PaymentStatus::class,
            'reviewed_at' => 'datetime',
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

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isApproved(): bool
    {
        return $this->status === PaymentStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === PaymentStatus::REJECTED;
    }

    public function isPendingReview(): bool
    {
        return $this->status === PaymentStatus::PENDING_REVIEW;
    }
}
