<?php

namespace App\Services\Payments;

use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Return machine name of gateway (e.g., 'zarinpal', 'jibit').
     */
    public function getName(): string;

    /**
     * Create a payment request and return data including gateway_url and payment_id.
     *
     * Expected return shape:
     * [
     *   'success' => bool,
     *   'gateway_url' => string, // if success
     *   'payment_id' => int,     // if success
     *   'authority' => string|null,
     *   'error' => string|null,
     *   'code' => int|string|null,
     * ]
     */
    public function createPaymentRequest(array $data): array;

    /**
     * Verify payment using authority/token and amount (in Rial if gateway requires).
     *
     * Expected return shape:
     * [
     *   'success' => bool,
     *   'ref_id' => string|int|null,
     *   'error' => string|null,
     *   'code' => int|string|null,
     * ]
     */
    public function verifyPayment(string $authority, int $amount): array;

    /**
     * Convert Toman to gateway-expected unit (Rial for Zarinpal).
     */
    public function formatAmount(int $amountInToman): int;

    /**
     * Extract callback parameters (authority/token and status) from request.
     * Must return [authority, statusString].
     */
    public function extractCallback(Request $request): array;
}










