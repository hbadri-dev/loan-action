<?php

namespace App\Services\SMS;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class KavenegarService
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl;
    private bool $sandbox;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('KAVENEGAR_API_KEY');
        $this->baseUrl = config('sms.services.kavenegar.base_url', 'https://api.kavenegar.com/v1');
        $this->sandbox = config('sms.sandbox', true);
    }

    /**
     * Send SMS using Kavenegar API (alias for sendMessage)
     */
    public function sendSMS(string $mobile, string $message, string $sender = null): bool
    {
        return $this->sendMessage($mobile, $message, $sender);
    }

    /**
     * Send SMS using Kavenegar API
     */
    public function sendMessage(string $mobile, string $message, string $sender = null): bool
    {
        try {
            $sender = $sender ?? config('sms.services.kavenegar.sender', '10008663');
            $formattedMobile = $this->formatMobile($mobile);

            // Sandbox mode - just log instead of calling API
            if ($this->sandbox) {
                Log::info('SMS Sandbox Mode - Message would be sent', [
                    'mobile' => $mobile,
                    'formatted_mobile' => $formattedMobile,
                    'message' => $message,
                    'sender' => $sender,
                    'api_key' => substr($this->apiKey, 0, 8) . '...'
                ]);
                return true;
            }

            $response = $this->client->post("{$this->baseUrl}/{$this->apiKey}/sms/send.json", [
                'form_params' => [
                    'receptor' => $formattedMobile,
                    'message' => $message,
                    'sender' => $sender,
                ],
                'timeout' => config('sms.settings.timeout', 30),
                'connect_timeout' => config('sms.settings.connect_timeout', 10),
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            // Check if the response is valid
            if (!isset($result['return']['status'])) {
                throw new \Exception('Invalid response from Kavenegar API: ' . json_encode($result));
            }

            // Check if status is 200 (success)
            if ($result['return']['status'] != 200) {
                $errorMessage = $result['return']['message'] ?? 'Unknown error';
                throw new \Exception("Kavenegar API error: {$errorMessage} (Status: {$result['return']['status']})");
            }

            Log::info('SMS sent successfully', [
                'mobile' => $mobile,
                'formatted_mobile' => $formattedMobile,
                'message' => $message,
                'response' => $result
            ]);

            return true;

        } catch (GuzzleException $e) {
            Log::error('SMS service HTTP error', [
                'mobile' => $mobile,
                'message' => $message,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw new \Exception('Failed to send SMS: Network error - ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('SMS service error', [
                'mobile' => $mobile,
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send login OTP code using Kavenegar verify/lookup API
     * Uses "login-otp" template
     */
    public function sendLoginOTP(string $phone, string $code): bool
    {
        return $this->sendOTP($phone, $code, 'login-otp');
    }

    /**
     * Send contract confirmation OTP code using Kavenegar verify/lookup API
     * Uses "contract-confirmation" template
     */
    public function sendContractOTP(string $phone, string $code): bool
    {
        return $this->sendOTP($phone, $code, 'contract-confirmation');
    }

    /**
     * Send OTP code using Kavenegar verify/lookup API
     * Documentation: https://kavenegar.com/rest.html#verify-lookup
     */
    public function sendOTP(string $mobile, string $code, string $template = 'login-otp'): bool
    {
        try {
            $formattedMobile = $this->formatMobile($mobile);

            // Sandbox mode - just log instead of calling API
            if ($this->sandbox) {
                Log::info('SMS Sandbox Mode - OTP would be sent', [
                    'mobile' => $mobile,
                    'formatted_mobile' => $formattedMobile,
                    'template' => $template,
                    'code' => $code,
                    'api_key' => substr($this->apiKey, 0, 8) . '...'
                ]);
                return true;
            }

            $response = $this->client->post("{$this->baseUrl}/{$this->apiKey}/verify/lookup.json", [
                'form_params' => [
                    'receptor' => $formattedMobile,
                    'token' => $code,
                    'template' => $template,
                ],
                'timeout' => config('sms.settings.timeout', 30),
                'connect_timeout' => config('sms.settings.connect_timeout', 10),
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            // Check if the response is valid
            if (!isset($result['return']['status'])) {
                throw new \Exception('Invalid response from Kavenegar API: ' . json_encode($result));
            }

            // Check if status is 200 (success)
            if ($result['return']['status'] != 200) {
                $errorMessage = $result['return']['message'] ?? 'Unknown error';
                throw new \Exception("Kavenegar API error: {$errorMessage} (Status: {$result['return']['status']})");
            }

            Log::info('OTP sent successfully', [
                'mobile' => $mobile,
                'formatted_mobile' => $formattedMobile,
                'template' => $template,
                'response' => $result
            ]);

            return true;

        } catch (GuzzleException $e) {
            Log::error('OTP service HTTP error', [
                'mobile' => $mobile,
                'template' => $template,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw new \Exception('Failed to send OTP: Network error - ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('OTP service error', [
                'mobile' => $mobile,
                'template' => $template,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send template-based SMS using Kavenegar verify/lookup API
     * This method is used for sending predefined templates with token substitution
     *
     * @param string $mobile The recipient's mobile number
     * @param string $token The token value to replace in the template (e.g., name, phone number)
     * @param string $template The template name registered in Kavenegar
     * @return bool
     */
    public function sendTemplateSMS(string $mobile, string $token, string $template): bool
    {
        try {
            $formattedMobile = $this->formatMobile($mobile);

            // Sandbox mode - just log instead of calling API
            if ($this->sandbox) {
                Log::info('SMS Sandbox Mode - Template SMS would be sent', [
                    'mobile' => $mobile,
                    'formatted_mobile' => $formattedMobile,
                    'template' => $template,
                    'token' => $token,
                    'api_key' => substr($this->apiKey, 0, 8) . '...'
                ]);
                return true;
            }

            $response = $this->client->post("{$this->baseUrl}/{$this->apiKey}/verify/lookup.json", [
                'form_params' => [
                    'receptor' => $formattedMobile,
                    'token' => $token,
                    'template' => $template,
                ],
                'timeout' => config('sms.settings.timeout', 30),
                'connect_timeout' => config('sms.settings.connect_timeout', 10),
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            // Check if the response is valid
            if (!isset($result['return']['status'])) {
                throw new \Exception('Invalid response from Kavenegar API: ' . json_encode($result));
            }

            // Check if status is 200 (success)
            if ($result['return']['status'] != 200) {
                $errorMessage = $result['return']['message'] ?? 'Unknown error';
                throw new \Exception("Kavenegar API error: {$errorMessage} (Status: {$result['return']['status']})");
            }

            Log::info('Template SMS sent successfully', [
                'mobile' => $mobile,
                'formatted_mobile' => $formattedMobile,
                'template' => $template,
                'token' => $token,
                'response' => $result
            ]);

            return true;

        } catch (GuzzleException $e) {
            Log::error('Template SMS service HTTP error', [
                'mobile' => $mobile,
                'template' => $template,
                'token' => $token,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw new \Exception('Failed to send template SMS: Network error - ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Template SMS service error', [
                'mobile' => $mobile,
                'template' => $template,
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate random OTP code (6 digits by default)
     */
    public function generateOTP(int $length = null): string
    {
        $length = $length ?? config('sms.settings.otp_length', 6);
        return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Get OTP expiry time in minutes
     */
    public function getOTPExpiryMinutes(): int
    {
        return config('sms.settings.otp_expiry_minutes', 2);
    }

    /**
     * Validate mobile number format
     */
    public function validateMobile(string $mobile): bool
    {
        // Remove any non-digit characters
        $mobile = preg_replace('/\D/', '', $mobile);

        // Check if it's a valid Iranian mobile number
        return preg_match('/^09[0-9]{9}$/', $mobile) === 1;
    }

    /**
     * Format mobile number for Kavenegar
     */
    public function formatMobile(string $mobile): string
    {
        $mobile = preg_replace('/\D/', '', $mobile);

        // Add country code if not present
        if (str_starts_with($mobile, '09')) {
            $mobile = '98' . substr($mobile, 1);
        }

        return $mobile;
    }
}
