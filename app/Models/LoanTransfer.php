<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'seller_id',
        'buyer_id',
        'national_id_of_buyer',
        'transfer_receipt_path',
        'buyer_confirmed_at',
        'admin_confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'buyer_confirmed_at' => 'datetime',
            'admin_confirmed_at' => 'datetime',
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

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function isBuyerConfirmed(): bool
    {
        return !is_null($this->buyer_confirmed_at);
    }

    public function isAdminConfirmed(): bool
    {
        return !is_null($this->admin_confirmed_at);
    }

    public function isFullyConfirmed(): bool
    {
        return $this->isBuyerConfirmed() && $this->isAdminConfirmed();
    }
}
