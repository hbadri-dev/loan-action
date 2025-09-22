<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'phone_verified_at',
        'is_phone_verified',
        'national_id',
        'iban',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_phone_verified' => 'boolean',
        ];
    }

    public function createdAuctions(): HasMany
    {
        return $this->hasMany(Auction::class, 'created_by');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class, 'buyer_id');
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
        return $this->hasMany(SellerSale::class, 'seller_id');
    }

    public function loanTransfersAsSeller(): HasMany
    {
        return $this->hasMany(LoanTransfer::class, 'seller_id');
    }

    public function loanTransfersAsBuyer(): HasMany
    {
        return $this->hasMany(LoanTransfer::class, 'buyer_id');
    }

    public function reviewedPaymentReceipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class, 'reviewed_by');
    }

    public function buyerProgress(): HasMany
    {
        return $this->hasMany(BuyerProgress::class);
    }

    /**
     * Get IBAN validation rules
     */
    public static function getIbanValidationRules(): array
    {
        return [
            'iban' => [
                'required',
                'string',
                'regex:/^IR[0-9]{24}$/',
                'size:26'
            ]
        ];
    }

    /**
     * Get IBAN validation messages
     */
    public static function getIbanValidationMessages(): array
    {
        return [
            'iban.required' => 'شماره شبا الزامی است',
            'iban.regex' => 'شماره شبا باید با IR شروع شده و 24 رقم داشته باشد',
            'iban.size' => 'شماره شبا باید دقیقاً 26 کاراکتر باشد',
        ];
    }
}
