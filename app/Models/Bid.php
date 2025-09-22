<?php

namespace App\Models;

use App\Enums\BidStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'buyer_id',
        'amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'status' => BidStatus::class,
        ];
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function isHighest(): bool
    {
        return $this->status === BidStatus::HIGHEST;
    }

    public function isOutbid(): bool
    {
        return $this->status === BidStatus::OUTBID;
    }

    public function isAccepted(): bool
    {
        return $this->status === BidStatus::ACCEPTED;
    }
}
