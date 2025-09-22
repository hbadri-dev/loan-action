<?php

namespace App\Models;

use App\Enums\SaleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SellerSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'seller_id',
        'status',
        'current_step',
        'selected_bid_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => SaleStatus::class,
        ];
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function selectedBid(): BelongsTo
    {
        return $this->belongsTo(Bid::class, 'selected_bid_id');
    }

    public function loanTransfer(): HasOne
    {
        return $this->hasOne(LoanTransfer::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === SaleStatus::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === SaleStatus::CANCELLED;
    }

    public function getDisplayStep(): int
    {
        return match($this->status) {
            SaleStatus::INITIATED => 1,
            SaleStatus::CONTRACT_CONFIRMED => 2,
            SaleStatus::FEE_APPROVED => 3,
            SaleStatus::OFFER_ACCEPTED => 4,
            SaleStatus::AWAITING_BUYER_PAYMENT => 5,
            SaleStatus::BUYER_PAYMENT_APPROVED => 6,
            SaleStatus::LOAN_TRANSFERRED => 7,
            SaleStatus::TRANSFER_CONFIRMED => 8,
            SaleStatus::COMPLETED => 6, // نمایش مرحله 6 برای وضعیت تکمیل شده
            default => 1,
        };
    }
}
