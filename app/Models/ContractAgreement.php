<?php

namespace App\Models;

use App\Enums\ContractStatus;
use App\Enums\ContractRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'user_id',
        'role',
        'status',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'role' => ContractRole::class,
            'status' => ContractStatus::class,
            'confirmed_at' => 'datetime',
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

    public function isConfirmed(): bool
    {
        return $this->status === ContractStatus::CONFIRMED;
    }

    public function isOtpSent(): bool
    {
        return $this->status === ContractStatus::OTP_SENT;
    }

    public function isPending(): bool
    {
        return $this->status === ContractStatus::PENDING;
    }
}
