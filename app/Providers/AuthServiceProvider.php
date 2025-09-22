<?php

namespace App\Providers;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\PaymentReceipt;
use App\Models\SellerSale;
use App\Policies\AuctionPolicy;
use App\Policies\BidPolicy;
use App\Policies\FileAccessPolicy;
use App\Policies\PaymentReceiptPolicy;
use App\Policies\SellerSalePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
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
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Register file access policy
        Gate::define('view', [FileAccessPolicy::class, 'view']);
        Gate::define('download', [FileAccessPolicy::class, 'download']);
        Gate::define('delete', [FileAccessPolicy::class, 'delete']);
        Gate::define('viewReceipt', [FileAccessPolicy::class, 'viewReceipt']);
        Gate::define('viewTransferReceipt', [FileAccessPolicy::class, 'viewTransferReceipt']);
        Gate::define('upload', [FileAccessPolicy::class, 'upload']);
        Gate::define('manage', [FileAccessPolicy::class, 'manage']);
    }
}
