<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatus;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JibitService implements PaymentGatewayInterface
{
    private string $apiKey;
    private string $secretKey;
    private string $baseUrl;
    private bool $sandbox;
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->apiKey = (string) config('services.jibit.api_key');
        $this->secretKey = (string) config('services.jibit.secret_key');
        $this->sandbox = filter_var(Setting::get('jibit_sandbox', 'false'), FILTER_VALIDATE_BOOLEAN);
        // Jibit PPG v3 base URL
        $defaultBase = 'https://napi.jibit.ir/ppg/v3';
        $this->baseUrl = rtrim((string) config('services.jibit.base_url', $defaultBase), '/');
    }

    public function getName(): string
    {
        return 'jibit';
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

        $callbackUrl = (string) config('services.jibit.callback_url', route('payment.callback'));

        // Jibit PPG v3 purchase request - طبق سورس نمونه
        $requestBody = [
            'amount' => $this->formatAmount($data['amount']),
            'currency' => 'IRR',
            'callbackUrl' => $callbackUrl,
            'clientReferenceNumber' => (string) $payment->id,
            'userIdentifier' => $data['mobile'] ?? '',
            'description' => $data['description'] ?? null,
            'additionalData' => null,
        ];

        try {
            $headers = $this->authHeaders();

            Log::info('Jibit purchase create request', [
                'url' => $this->baseUrl . '/purchases',
                'headers' => array_keys($headers),
                'body' => $requestBody,
            ]);

            $response = Http::withHeaders($headers)
                ->post($this->baseUrl . '/purchases', $requestBody);

            $result = $response->json();

            Log::info('Jibit purchase create response', [
                'status' => $response->status(),
                'body' => $result,
                'raw_body' => $response->body(),
            ]);

            if ($response->successful() && isset($result['pspSwitchingUrl'])) {
                // استفاده از purchaseIdStr برای مقادیر bigint
                $purchaseId = (string) ($result['purchaseIdStr'] ?? $result['purchaseId']);

                PaymentTransaction::create([
                    'payment_id' => $payment->id,
                    'authority' => $purchaseId,
                    'status' => 'pending',
                    'gateway_response' => $result,
                ]);

                $payment->update([
                    'authority' => $purchaseId,
                    'gateway_url' => (string) $result['pspSwitchingUrl'],
                ]);

                Log::info('Jibit payment created successfully, redirecting', [
                    'payment_id' => $payment->id,
                    'purchase_id' => $purchaseId,
                    'redirect_url' => $result['pspSwitchingUrl'],
                ]);

                return [
                    'success' => true,
                    'authority' => $purchaseId,
                    'gateway_url' => (string) $result['pspSwitchingUrl'],
                    'payment_id' => $payment->id,
                ];
            }

            $payment->update(['status' => PaymentStatus::FAILED]);
            return [
                'success' => false,
                'error' => $result['errors'][0]['message'] ?? 'خطا در ایجاد درخواست پرداخت',
                'code' => $result['errors'][0]['code'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Jibit purchase create failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $payment->update(['status' => PaymentStatus::FAILED]);
            return [
                'success' => false,
                'error' => 'خطا در ارتباط با درگاه پرداخت: ' . $e->getMessage(),
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

        $purchaseId = $authority;

        try {
            // Jibit PPG v3 verify: GET /purchases/{purchaseId}/verify - طبق سورس نمونه
            $response = Http::withHeaders($this->authHeaders())
                ->get($this->baseUrl . '/purchases/' . $purchaseId . '/verify');
            $result = $response->json();

            Log::info('Jibit verify response', [
                'purchaseId' => $purchaseId,
                'status' => $response->status(),
                'body' => $result,
            ]);

            // طبق سورس نمونه: response مستقیماً شامل status است
            if ($response->successful() && ($result['status'] ?? '') === 'SUCCESSFUL') {
                $refId = (string) ($result['pspResult']['referenceNumber'] ?? $result['pspResult']['retrievalReferenceNumber'] ?? $purchaseId);

                $payment->update([
                    'status' => PaymentStatus::COMPLETED,
                    'ref_id' => $refId,
                    'paid_at' => now(),
                ]);

                $transaction = PaymentTransaction::where('payment_id', $payment->id)
                    ->where('authority', $authority)
                    ->first();
                if ($transaction) {
                    $transaction->update([
                        'status' => 'completed',
                        'ref_id' => $refId,
                        'gateway_response' => $result,
                        'completed_at' => now(),
                    ]);
                }

                return [
                    'success' => true,
                    'ref_id' => $refId,
                ];
            }

            $payment->update(['status' => PaymentStatus::FAILED]);
            return [
                'success' => false,
                'error' => $result['errors'][0]['message'] ?? 'پرداخت ناموفق',
                'code' => $result['errors'][0]['code'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Jibit verify failed', [
                'purchaseId' => $purchaseId,
                'error' => $e->getMessage(),
            ]);
            $payment->update(['status' => PaymentStatus::FAILED]);
            return [
                'success' => false,
                'error' => 'خطا در تأیید پرداخت',
            ];
        }
    }

    public function formatAmount(int $amountInToman): int
    {
        // Assuming Jibit expects rial; align with Zarinpal for consistency
        return $amountInToman * 10;
    }

    public function extractCallback(Request $request): array
    {
        // Jibit PPG v3 callback params با POST ارسال می‌شوند - طبق سورس نمونه
        $purchaseId = (string) $request->input('purchaseId', '');
        $status = (string) $request->input('status', '');
        return [$purchaseId, $status];
    }

    private function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            // Get access token from Jibit - طبق سورس نمونه
            $response = Http::post($this->baseUrl . '/tokens', [
                'apiKey' => $this->apiKey,
                'secretKey' => $this->secretKey,
            ]);

            $result = $response->json();

            Log::info('Jibit token request', [
                'status' => $response->status(),
                'body' => $result,
            ]);

            if ($response->successful() && isset($result['accessToken'])) {
                // ذخیره با Bearer prefix - طبق سورس نمونه
                $this->accessToken = 'Bearer ' . $result['accessToken'];
                return $this->accessToken;
            }

            throw new Exception($result['errors'][0]['message'] ?? 'Failed to get access token');
        } catch (Exception $e) {
            Log::error('Jibit token request failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function authHeaders(): array
    {
        $accessToken = $this->getAccessToken();
        return [
            'Authorization' => $accessToken, // قبلاً Bearer اضافه شده
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}
