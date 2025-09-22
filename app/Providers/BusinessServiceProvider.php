<?php

namespace App\Providers;

use App\Services\AuctionLockService;
use App\Services\BiddingService;
use App\Services\ContractService;
use App\Services\FileUploadService;
use App\Services\LocalizationService;
use App\Services\ReceiptService;
use App\Services\SMS\KavenegarService;
use App\Services\TransferReceiptService;
use Illuminate\Support\ServiceProvider;

class BusinessServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register SMS service
        $this->app->singleton(KavenegarService::class, function ($app) {
            return new KavenegarService();
        });

        // Register file upload service
        $this->app->singleton(FileUploadService::class, function ($app) {
            return new FileUploadService();
        });

        // Register business services
        $this->app->singleton(BiddingService::class, function ($app) {
            return new BiddingService();
        });

        $this->app->singleton(AuctionLockService::class, function ($app) {
            return new AuctionLockService();
        });

        $this->app->singleton(ReceiptService::class, function ($app) {
            return new ReceiptService($app->make(FileUploadService::class));
        });

        $this->app->singleton(TransferReceiptService::class, function ($app) {
            return new TransferReceiptService($app->make(FileUploadService::class));
        });

        $this->app->singleton(ContractService::class, function ($app) {
            return new ContractService($app->make(KavenegarService::class));
        });

        $this->app->singleton(LocalizationService::class, function ($app) {
            return new LocalizationService();
        });

        // Register aliases
        $this->app->alias(KavenegarService::class, 'sms.kavenegar');
        $this->app->alias(FileUploadService::class, 'service.file_upload');
        $this->app->alias(BiddingService::class, 'service.bidding');
        $this->app->alias(AuctionLockService::class, 'service.auction_lock');
        $this->app->alias(ReceiptService::class, 'service.receipt');
        $this->app->alias(TransferReceiptService::class, 'service.transfer_receipt');
        $this->app->alias(LocalizationService::class, 'service.localization');
        $this->app->alias(ContractService::class, 'service.contract');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
