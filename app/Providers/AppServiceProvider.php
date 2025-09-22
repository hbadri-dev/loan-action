<?php

namespace App\Providers;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\PaymentReceipt;
use App\Models\SellerSale;
use App\Policies\AuctionPolicy;
use App\Policies\BidPolicy;
use App\Policies\PaymentReceiptPolicy;
use App\Policies\SellerSalePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Auction::class => AuctionPolicy::class,
        Bid::class => BidPolicy::class,
        PaymentReceipt::class => PaymentReceiptPolicy::class,
        SellerSale::class => SellerSalePolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
