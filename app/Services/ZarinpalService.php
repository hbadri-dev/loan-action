<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ZarinpalService
{
    private string $merchantId;
    private string $baseUrl;
    private bool $sandbox;

    public function __construct()
    {
        $this->sandbox = config('services.zarinpal.sandbox', true);

        if ($this->sandbox) {
            // Sandbox environment - use test merchant ID and sandbox URLs
            $this->merchantId = config('services.zarinpal.test_merchant_id');
            $this->baseUrl = 'https://sandbox.zarinpal.com/pg/v4/payment/';
        } else {
            // Production environment
            $this->merchantId = config('services.zarinpal.merchant_id');
            $this->baseUrl = 'https://api.zarinpal.com/pg/v4/payment/';
        }

        Log::info('ZarinpalService initialized', [
            'sandbox' => $this->sandbox,
            'merchant_id' => $this->merchantId,
            'base_url' => $this->baseUrl,
            'env_sandbox' => env('ZARINPAL_SANDBOX'),
            'config_sandbox' => config('services.zarinpal.sandbox')
        ]);
    }

    /**
     * Create payment request
     */
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

        $callbackUrl = config('services.zarinpal.callback_url');

        $requestData = [
            'merchant_id' => $this->merchantId,
            'amount' => $this->formatAmount($data['amount']), // Convert Toman to Rial
            'description' => $data['description'],
            'callback_url' => $callbackUrl,
            'mobile' => $data['mobile'] ?? null,
            'email' => $data['email'] ?? null,
        ];

        Log::info('Zarinpal Payment Request', [
            'request_data' => $requestData,
            'base_url' => $this->baseUrl,
            'merchant_id' => $this->merchantId,
            'sandbox' => $this->sandbox,
            'env_sandbox' => env('ZARINPAL_SANDBOX'),
            'config_sandbox' => config('services.zarinpal.sandbox')
        ]);

        try {
            $response = Http::post($this->baseUrl . 'request.json', $requestData);
            $result = $response->json();

            Log::info('Zarinpal API Response', [
                'response' => $result,
                'status_code' => $response->status(),
                'request_data' => $requestData
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
            if (isset($result['errors']) && !empty($result['errors'])) {
                $payment->update(['status' => PaymentStatus::FAILED]);
                return [
                    'success' => false,
                    'error' => $result['errors']['message'] ?? 'خطا در ایجاد درخواست پرداخت',
                    'code' => $result['errors']['code'] ?? null,
                ];
            }

            // Check if we have valid data with success code
            if (isset($result['data']) && is_array($result['data']) && isset($result['data']['code']) && $result['data']['code'] == 100) {
                $authority = $result['data']['authority'];

                PaymentTransaction::create([
                    'payment_id' => $payment->id,
                    'authority' => $authority,
                    'status' => 'pending',
                    'gateway_response' => $result,
                ]);

                $payment->update([
                    'authority' => $authority,
                    'gateway_url' => $this->getGatewayUrl($authority),
                ]);

                return [
                    'success' => true,
                    'authority' => $authority,
                    'gateway_url' => $payment->gateway_url,
                    'payment_id' => $payment->id,
                ];
            } else {
                $payment->update(['status' => PaymentStatus::FAILED]);

                return [
                    'success' => false,
                    'error' => $result['errors']['message'] ?? 'خطا در ایجاد درخواست پرداخت',
                    'code' => $result['errors']['code'] ?? $result['data']['code'] ?? null,
                ];
            }
        } catch (Exception $e) {
            Log::error('Zarinpal payment request failed', [
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

    /**
     * Verify payment
     */
    public function verifyPayment(string $authority, int $amount): array
    {
        $payment = Payment::where('authority', $authority)->first();

        if (!$payment) {
            return [
                'success' => false,
                'error' => 'پرداخت یافت نشد',
            ];
        }

        $requestData = [
            'merchant_id' => $this->merchantId,
            'amount' => $amount,
            'authority' => $authority,
        ];

        try {
            $response = Http::post($this->baseUrl . 'verify.json', $requestData);
            $result = $response->json();

            $transaction = PaymentTransaction::where('payment_id', $payment->id)
                ->where('authority', $authority)
                ->first();

            if ($result['data']['code'] == 100) {
                // Payment successful
                $payment->update([
                    'status' => PaymentStatus::COMPLETED,
                    'ref_id' => $result['data']['ref_id'],
                    'paid_at' => now(),
                ]);

                if ($transaction) {
                    $transaction->update([
                        'status' => 'completed',
                        'ref_id' => $result['data']['ref_id'],
                        'gateway_response' => $result,
                        'completed_at' => now(),
                    ]);
                }

                return [
                    'success' => true,
                    'ref_id' => $result['data']['ref_id'],
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
                    'error' => $result['errors']['message'] ?? 'پرداخت ناموفق',
                    'code' => $result['data']['code'] ?? null,
                ];
            }
        } catch (Exception $e) {
            Log::error('Zarinpal payment verification failed', [
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

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $authority): ?Payment
    {
        return Payment::where('authority', $authority)->first();
    }

    /**
     * Get gateway URL
     */
    private function getGatewayUrl(string $authority): string
    {
        Log::info('Generating Gateway URL', [
            'authority' => $authority,
            'sandbox' => $this->sandbox,
            'sandbox_type' => gettype($this->sandbox),
            'sandbox_value' => $this->sandbox ? 'true' : 'false'
        ]);

        if ($this->sandbox) {
            // Sandbox gateway URL
            $url = 'https://sandbox.zarinpal.com/pg/StartPay/' . $authority;
            Log::info('Using Sandbox URL', ['url' => $url]);
            return $url;
        } else {
            // Production gateway URL
            $url = 'https://www.zarinpal.com/pg/StartPay/' . $authority;
            Log::info('Using Production URL', ['url' => $url]);
            return $url;
        }
    }

    /**
     * Format amount for Zarinpal (convert Toman to Rial)
     */
    public function formatAmount(int $amountInToman): int
    {
        return $amountInToman * 10; // Convert to Rial
    }

    /**
     * Convert Rial back to Toman
     */
    public function convertToToman(int $amountInRial): int
    {
        return $amountInRial / 10;
    }
}
