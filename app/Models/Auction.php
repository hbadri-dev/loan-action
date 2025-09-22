<?php

namespace App\Models;

use App\Enums\AuctionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'loan_type',
        'principal_amount',
        'term_months',
        'interest_rate_percent',
        'min_purchase_price',
        'status',
        'is_locked',
        'locked_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AuctionStatus::class,
            'principal_amount' => 'integer',
            'term_months' => 'integer',
            'interest_rate_percent' => 'decimal:2',
            'min_purchase_price' => 'integer',
            'is_locked' => 'boolean',
            'locked_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function paymentReceipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function contractAgreements(): HasMany
    {
        return $this->hasMany(ContractAgreement::class);
    }

    public function sellerSales(): HasMany
    {
        return $this->hasMany(SellerSale::class);
    }

    public function highestBid(): HasMany
    {
        return $this->hasMany(Bid::class)->where('status', \App\Enums\BidStatus::HIGHEST);
    }

    public function buyerProgress(): HasMany
    {
        return $this->hasMany(BuyerProgress::class);
    }

    /**
     * Set the status attribute and sync is_locked
     */
    public function setStatusAttribute($value): void
    {
        $this->attributes['status'] = $value;

        // Sync is_locked based on status
        if ($value === AuctionStatus::LOCKED) {
            $this->attributes['is_locked'] = true;
        } else {
            $this->attributes['is_locked'] = false;
        }
    }

    /**
     * Check if auction is locked (quick database-level check)
     */
    public function isLocked(): bool
    {
        return $this->is_locked || $this->status === AuctionStatus::LOCKED;
    }
}
