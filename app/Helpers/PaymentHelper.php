<?php

namespace App\Helpers;

class PaymentHelper
{
    /**
     * Get display name for payment gateway
     */
    public static function getGatewayDisplayName(string $gateway): string
    {
        return match($gateway) {
            'zarinpal' => 'زرین‌پال',
            'jibit' => 'جیبیت',
            'payping' => 'پی‌پینگ',
            default => 'درگاه پرداخت',
        };
    }

    /**
     * Get active payment gateway
     */
    public static function getActiveGateway(): string
    {
        return \App\Models\Setting::get('payment_gateway', config('services.payments.active', 'zarinpal'));
    }

    /**
     * Get active payment gateway display name
     */
    public static function getActiveGatewayDisplayName(): string
    {
        return self::getGatewayDisplayName(self::getActiveGateway());
    }

    /**
     * Get actual amount for payment (considering sandbox mode)
     */
    public static function getActualPaymentAmount(int $requestedAmount): int
    {
        $activeGateway = self::getActiveGateway();
        
        if ($activeGateway === 'payping') {
            $paypingService = app(\App\Services\Payments\PaypingService::class);
            return $paypingService->getActualAmount($requestedAmount);
        }
        
        return $requestedAmount;
    }

    /**
     * Check if current gateway is in sandbox mode
     */
    public static function isSandboxMode(): bool
    {
        $activeGateway = self::getActiveGateway();
        
        if ($activeGateway === 'payping') {
            $paypingService = app(\App\Services\Payments\PaypingService::class);
            return $paypingService->isSandboxMode();
        }
        
        return false;
    }
}
