<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZarinpalService;

class CheckZarinpalConfig extends Command
{
    protected $signature = 'zarinpal:check';
    protected $description = 'Check Zarinpal configuration and sandbox status';

    public function handle()
    {
        $this->info('=== Zarinpal Configuration Check ===');

        // Environment variables
        $this->line('Environment Variables:');
        $this->line('ZARINPAL_SANDBOX: ' . env('ZARINPAL_SANDBOX'));
        $this->line('ZARINPAL_MERCHANT_ID: ' . env('ZARINPAL_MERCHANT_ID'));
        $this->line('ZARINPAL_CALLBACK_URL: ' . env('ZARINPAL_CALLBACK_URL'));
        $this->line('APP_ENV: ' . env('APP_ENV'));
        $this->line('APP_DEBUG: ' . env('APP_DEBUG'));

        $this->newLine();

        // Config values
        $this->line('Config Values:');
        $this->line('services.zarinpal.sandbox: ' . (config('services.zarinpal.sandbox') ? 'true' : 'false'));
        $this->line('services.zarinpal.merchant_id: ' . config('services.zarinpal.merchant_id'));
        $this->line('services.zarinpal.test_merchant_id: ' . config('services.zarinpal.test_merchant_id'));
        $this->line('services.zarinpal.callback_url: ' . config('services.zarinpal.callback_url'));

        $this->newLine();

        // ZarinpalService instance
        $this->line('ZarinpalService Instance:');
        try {
            $zarinpalService = app(ZarinpalService::class);
            $this->info('Service initialized successfully');

            // Use reflection to get private properties
            $reflection = new \ReflectionClass($zarinpalService);

            $sandboxProperty = $reflection->getProperty('sandbox');
            $sandboxProperty->setAccessible(true);
            $sandboxValue = $sandboxProperty->getValue($zarinpalService);
            $this->line('Service sandbox: ' . ($sandboxValue ? 'true' : 'false'));

            $merchantIdProperty = $reflection->getProperty('merchantId');
            $merchantIdProperty->setAccessible(true);
            $merchantIdValue = $merchantIdProperty->getValue($zarinpalService);
            $this->line('Service merchant_id: ' . $merchantIdValue);

            $baseUrlProperty = $reflection->getProperty('baseUrl');
            $baseUrlProperty->setAccessible(true);
            $baseUrlValue = $baseUrlProperty->getValue($zarinpalService);
            $this->line('Service base_url: ' . $baseUrlValue);

        } catch (\Exception $e) {
            $this->error('Error initializing ZarinpalService: ' . $e->getMessage());
        }

        $this->newLine();

        // Recommendations
        $this->line('Recommendations:');
        if (config('services.zarinpal.sandbox')) {
            $this->info('✓ Sandbox mode is ACTIVE');
            $this->line('  - Using test merchant ID: ' . config('services.zarinpal.test_merchant_id'));
            $this->line('  - Using sandbox URL: https://sandbox.zarinpal.com/pg/v4/payment/');
        } else {
            $this->warn('⚠ Production mode is ACTIVE');
            $this->line('  - Using real merchant ID: ' . config('services.zarinpal.merchant_id'));
            $this->line('  - Using production URL: https://api.zarinpal.com/pg/v4/payment/');
        }

        $this->newLine();
        $this->info('Check completed. Look for logs in storage/logs/laravel.log');
    }
}

