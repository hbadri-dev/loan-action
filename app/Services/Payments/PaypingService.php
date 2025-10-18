<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaypingService implements PaymentGatewayInterface
{
    private string $token;
    private string $baseUrl;
    private bool $sandbox;

    public function __construct()
    {
        $this->token = (string) config('services.payping.token');
        $this->sandbox = filter_var(Setting::get('payping_sandbox', 'true'), FILTER_VALIDATE_BOOLEAN);

        if ($this->sandbox) {
            $this->baseUrl = 'https://api.payping.ir/v1';
        } else {
            $this->baseUrl = 'https://api.payping.ir/v1';
        }
    }

    public function getName(): string
    {
        return 'payping';
    }

    public function createPaymentRequest(array $data): array
    {
        $payment = Payment::create([
            'user_id' => $data['user_id'],
            'auction_id' => $data['auction_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'status' => PaymentStatus::PENDING,
        ]);

        $callbackUrl = (string) config('services.payping.callback_url', route('payment.callback'));

        // Payping payment request
        $requestBody = [
            'amount' => $this->formatAmount($data['amount']),
            'returnUrl' => $callbackUrl,
            'description' => $data['description'] ?? null,
            'clientRefId' => (string) $payment->id,
        ];

        try {
            $headers = $this->getHeaders();

            Log::info('Payping Payment Request', [
                'request_body' => $requestBody,
                'headers' => $headers,
                'base_url' => $this->baseUrl,
                'sandbox' => $this->sandbox,
            ]);

            $response = Http::withHeaders($headers)
                ->post($this->baseUrl . '/pay', $requestBody);

            $result = $response->json();

            Log::info('Payping API Response', [
                'response' => $result,
                'status_code' => $response->status(),
                'request_body' => $requestBody
            ]);

            // Check if response is valid
            if (!$result) {
                $payment->update(['status' => PaymentStatus::FAILED]);
                return [
                    'success' => false,
                    'error' => 'پاسخ نامعتبر از درگاه پرداخت',
                    'code' => null,
                ];
            }

            // Check for errors in response
            if (isset($result['error']) && !empty($result['error'])) {
                $payment->update(['status' => PaymentStatus::FAILED]);
                return [
                    'success' => false,
                    'error' => $result['error'] ?? 'خطا در ایجاد درخواست پرداخت',
                    'code' => $result['code'] ?? null,
                ];
            }

            // Check if we have valid data with code
            if (isset($result['code']) && !empty($result['code'])) {
                $code = $result['code'];

                PaymentTransaction::create([
                    'payment_id' => $payment->id,
                    'authority' => $code,
                    'status' => 'pending',
                    'gateway_response' => $result,
                ]);

                $payment->update([
                    'authority' => $code,
                    'gateway_url' => $this->getGatewayUrl($code),
                ]);

                return [
                    'success' => true,
                    'authority' => $code,
                    'gateway_url' => $payment->gateway_url,
                    'payment_id' => $payment->id,
                ];
            } else {
                $payment->update(['status' => PaymentStatus::FAILED]);

                return [
                    'success' => false,
                    'error' => $result['error'] ?? 'خطا در ایجاد درخواست پرداخت',
                    'code' => $result['code'] ?? null,
                ];
            }
        } catch (Exception $e) {
            Log::error('Payping payment request failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            $payment->update(['status' => PaymentStatus::FAILED]);

            return [
                'success' => false,
                'error' => 'خطا در ارتباط با درگاه پرداخت',
            ];
        }
    }

    public function verifyPayment(string $authority, int $amount): array
    {
        $payment = Payment::where('authority', $authority)->first();

        if (!$payment) {
            return [
                'success' => false,
                'error' => 'پرداخت یافت نشد',
            ];
        }

        $requestBody = [
            'amount' => $amount,
            'refId' => $authority,
        ];

        try {
            $headers = $this->getHeaders();

            Log::info('Payping Payment Verification', [
                'request_body' => $requestBody,
                'headers' => $headers,
            ]);

            $response = Http::withHeaders($headers)
                ->post($this->baseUrl . '/pay/verify', $requestBody);

            $result = $response->json();

            Log::info('Payping Verification Response', [
                'response' => $result,
                'status_code' => $response->status(),
                'request_body' => $requestBody
            ]);

            $transaction = PaymentTransaction::where('payment_id', $payment->id)
                ->where('authority', $authority)
                ->first();

            if (isset($result['success']) && $result['success'] === true) {
                // Payment successful
                $payment->update([
                    'status' => PaymentStatus::COMPLETED,
                    'ref_id' => $result['refId'] ?? $authority,
                    'paid_at' => now(),
                ]);

                if ($transaction) {
                    $transaction->update([
                        'status' => 'completed',
                        'ref_id' => $result['refId'] ?? $authority,
                        'gateway_response' => $result,
                        'completed_at' => now(),
                    ]);
                }

                return [
                    'success' => true,
                    'ref_id' => $result['refId'] ?? $authority,
                    'payment' => $payment,
                ];
            } else {
                // Payment failed
                $payment->update(['status' => PaymentStatus::FAILED]);

                if ($transaction) {
                    $transaction->update([
                        'status' => 'failed',
                        'gateway_response' => $result,
                    ]);
                }

                return [
                    'success' => false,
                    'error' => $result['error'] ?? 'پرداخت ناموفق',
                    'code' => $result['code'] ?? null,
                ];
            }
        } catch (Exception $e) {
            Log::error('Payping payment verification failed', [
                'error' => $e->getMessage(),
                'authority' => $authority,
            ]);

            $payment->update(['status' => PaymentStatus::FAILED]);

            return [
                'success' => false,
                'error' => 'خطا در تأیید پرداخت',
            ];
        }
    }

    public function getPaymentStatus(string $authority): ?Payment
    {
        return Payment::where('authority', $authority)->first();
    }

    private function getGatewayUrl(string $code): string
    {
        return $this->baseUrl . '/pay/gotoipg/' . $code;
    }

    public function formatAmount(int $amountInToman): int
    {
        // In sandbox mode, always use 1000 Rial for testing (not Toman)
        if ($this->sandbox) {
            return 1000; // 1000 Rial for sandbox testing
        }

        return $amountInToman * 10; // Convert to Rial
    }

    public function convertToToman(int $amountInRial): int
    {
        return $amountInRial / 10;
    }

    /**
     * Get the actual amount that will be charged (considering sandbox mode)
     */
    public function getActualAmount(int $requestedAmountInToman): int
    {
        if ($this->sandbox) {
            return 100; // Always 100 Toman in sandbox (1000 Rial = 100 Toman)
        }

        return $requestedAmountInToman;
    }

    /**
     * Check if sandbox mode is active
     */
    public function isSandboxMode(): bool
    {
        return $this->sandbox;
    }

    public function extractCallback(Request $request): array
    {
        // Payping may send refId with different casing; normalize it
        $authority = (string) ($request->get('refId') ?? $request->get('refid') ?? $request->get('refID') ?? '');
        // Status may be missing entirely; also consider different casing
        $status = (string) ($request->get('status') ?? $request->get('Status') ?? '');

        // In Payping sandbox/production, callback may only include refid without a status.
        // If we have a refid, treat it as a successful redirect and let verify() decide final status.
        if ($authority && $status === '') {
            $status = 'OK';
        }

        return [$authority, $status];
    }

    private function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
