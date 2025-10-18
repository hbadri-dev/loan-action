<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomRedirectService implements PaymentGatewayInterface
{
    private string $redirectUrl;

    public function __construct()
    {
        $this->redirectUrl = 'https://ppng.ir/d/gw8d';
    }

    public function getName(): string
    {
        return 'custom_redirect';
    }

    public function createPaymentRequest(array $data): array
    {
        // Create payment record with pending status
        $payment = Payment::create([
            'user_id' => $data['user_id'],
            'auction_id' => $data['auction_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'status' => PaymentStatus::PENDING,
            'authority' => 'custom_' . time() . '_temp',
        ]);

        // Update with actual payment ID
        $payment->update([
            'authority' => 'custom_' . time() . '_' . $payment->id,
        ]);

        // Create transaction record
        PaymentTransaction::create([
            'payment_id' => $payment->id,
            'authority' => $payment->authority,
            'status' => 'pending',
            'gateway_response' => ['redirect_url' => $this->redirectUrl],
        ]);

        Log::info('Custom redirect payment created', [
            'payment_id' => $payment->id,
            'redirect_url' => $this->redirectUrl,
            'user_id' => $data['user_id'],
            'auction_id' => $data['auction_id'],
        ]);

        return [
            'success' => true,
            'gateway_url' => $this->redirectUrl,
            'payment_id' => $payment->id,
            'authority' => $payment->authority,
        ];
    }

    public function verifyPayment(string $authority, int $amount): array
    {
        // For custom redirect, we don't verify with external gateway
        // The callback will handle the verification
        return [
            'success' => true,
            'ref_id' => 'custom_' . time(),
        ];
    }

    public function formatAmount(int $amountInToman): int
    {
        // Return amount as-is for custom redirect
        return $amountInToman;
    }

    public function extractCallback(Request $request): array
    {
        // Extract payment parameter from callback
        $payment = $request->get('payment', '');
        $status = $payment === 'success' ? 'success' : 'failed';

        return [$payment, $status];
    }
}
