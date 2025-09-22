<?php

namespace App\Http\Controllers\Example;

use App\Http\Controllers\Controller;
use App\Services\SMS\KavenegarService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SmsExampleController extends Controller
{
    protected KavenegarService $smsService;

    public function __construct(KavenegarService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send a test SMS message
     */
    public function sendTestSms(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|regex:/^09[0-9]{9}$/',
            'message' => 'required|string|max:160',
        ]);

        try {
            $result = $this->smsService->sendMessage(
                $request->phone,
                $request->message
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'sandbox_mode' => config('sms.sandbox'),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send SMS',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending SMS: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a test OTP
     */
    public function sendTestOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|regex:/^09[0-9]{9}$/',
            'type' => 'required|in:login,contract',
        ]);

        try {
            $code = $this->smsService->generateOTP();

            if ($request->type === 'login') {
                $result = $this->smsService->sendLoginOTP($request->phone, $code);
            } else {
                $result = $this->smsService->sendContractOTP($request->phone, $code);
            }

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully',
                    'code' => config('sms.sandbox') ? $code : null, // Only show code in sandbox mode
                    'sandbox_mode' => config('sms.sandbox'),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending OTP: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate mobile number
     */
    public function validateMobile(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $isValid = $this->smsService->validateMobile($request->phone);
        $formatted = $isValid ? $this->smsService->formatMobile($request->phone) : null;

        return response()->json([
            'valid' => $isValid,
            'formatted' => $formatted,
        ]);
    }

    /**
     * Get SMS service status
     */
    public function getStatus(): JsonResponse
    {
        return response()->json([
            'sandbox_mode' => config('sms.sandbox'),
            'driver' => config('sms.default'),
            'otp_length' => config('sms.settings.otp_length'),
            'otp_expiry_minutes' => config('sms.settings.otp_expiry_minutes'),
            'rate_limit_attempts' => config('sms.rate_limit.max_attempts'),
            'rate_limit_decay' => config('sms.rate_limit.decay_minutes'),
        ]);
    }
}

