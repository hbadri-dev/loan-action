<?php

namespace App\Services;

use App\Enums\ContractRole;
use App\Enums\ContractStatus;
use App\Models\Auction;
use App\Models\ContractAgreement;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\SMS\KavenegarService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContractService
{
    protected KavenegarService $smsService;

    public function __construct(KavenegarService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send OTP for contract confirmation
     *
     * @throws \Exception
     */
    public function sendContractOTP(User $user, Auction $auction, ContractRole $role): array
    {
        return DB::transaction(function () use ($user, $auction, $role) {
            // Check if user already has a confirmed contract for this auction and role
            $existingContract = ContractAgreement::where('user_id', $user->id)
                ->where('auction_id', $auction->id)
                ->where('role', $role)
                ->where('status', ContractStatus::CONFIRMED)
                ->first();

            if ($existingContract) {
                throw new \Exception('قرارداد قبلاً تأیید شده است.');
            }

            // Check if user has a pending OTP
            $pendingOtp = OtpCode::where('phone', $user->phone)
                ->where('purpose', 'contract-confirmation')
                ->where('used_at', null)
                ->where('expires_at', '>', now())
                ->first();

            if ($pendingOtp) {
                $remainingTime = $pendingOtp->expires_at->diffInSeconds(now());
                throw new \Exception(
                    'کد تأیید قبلاً ارسال شده است. ' .
                    ceil($remainingTime / 60) . ' دقیقه دیگر مجدداً تلاش کنید.'
                );
            }

            // Generate OTP code
            $code = $this->smsService->generateOTP();
            $expiresAt = now()->addMinutes($this->smsService->getOTPExpiryMinutes());

            // Create OTP record
            $otpCode = OtpCode::create([
                'phone' => $user->phone,
                'code' => $code,
                'purpose' => 'contract-confirmation',
                'expires_at' => $expiresAt,
            ]);

            // Send OTP via SMS
            $smsSent = $this->smsService->sendContractOTP($user->phone, $code);

            if (!$smsSent) {
                throw new \Exception('خطا در ارسال کد تأیید. لطفاً مجدداً تلاش کنید.');
            }

            Log::info('Contract OTP sent', [
                'user_id' => $user->id,
                'auction_id' => $auction->id,
                'role' => $role->value,
                'phone' => $user->phone,
                'otp_id' => $otpCode->id,
            ]);

            return [
                'otp_id' => $otpCode->id,
                'expires_at' => $expiresAt,
                'message' => 'کد تأیید ارسال شد.',
            ];
        });
    }

    /**
     * Verify OTP and create contract agreement
     *
     * @throws \Exception
     */
    public function verifyContractOTP(
        User $user,
        Auction $auction,
        ContractRole $role,
        string $code
    ): ContractAgreement {
        return DB::transaction(function () use ($user, $auction, $role, $code) {
            // Find valid OTP
            $otpCode = OtpCode::where('phone', $user->phone)
                ->where('code', $code)
                ->where('purpose', 'contract-confirmation')
                ->where('used_at', null)
                ->where('expires_at', '>', now())
                ->first();

            if (!$otpCode) {
                throw new \Exception('کد تأیید نامعتبر یا منقضی شده است.');
            }

            // Mark OTP as used
            $otpCode->update(['used_at' => now()]);

            // Check if contract already exists
            $existingContract = ContractAgreement::where('user_id', $user->id)
                ->where('auction_id', $auction->id)
                ->where('role', $role)
                ->first();

            if ($existingContract) {
                // Update existing contract
                $existingContract->update([
                    'status' => ContractStatus::CONFIRMED,
                    'confirmed_at' => now(),
                ]);

                Log::info('Contract agreement updated', [
                    'contract_id' => $existingContract->id,
                    'user_id' => $user->id,
                    'auction_id' => $auction->id,
                    'role' => $role->value,
                ]);

                return $existingContract->fresh();
            } else {
                // Create new contract agreement
                $contract = ContractAgreement::create([
                    'user_id' => $user->id,
                    'auction_id' => $auction->id,
                    'role' => $role,
                    'status' => ContractStatus::CONFIRMED,
                    'confirmed_at' => now(),
                ]);

                Log::info('Contract agreement created', [
                    'contract_id' => $contract->id,
                    'user_id' => $user->id,
                    'auction_id' => $auction->id,
                    'role' => $role->value,
                ]);

                return $contract;
            }
        });
    }

    /**
     * Check if user has confirmed contract for auction and role
     */
    public function hasConfirmedContract(User $user, Auction $auction, ContractRole $role): bool
    {
        return ContractAgreement::where('user_id', $user->id)
            ->where('auction_id', $auction->id)
            ->where('role', $role)
            ->where('status', ContractStatus::CONFIRMED)
            ->exists();
    }

    /**
     * Get user's contract agreements for an auction
     */
    public function getUserContracts(User $user, Auction $auction): \Illuminate\Database\Eloquent\Collection
    {
        return ContractAgreement::where('user_id', $user->id)
            ->where('auction_id', $auction->id)
            ->get();
    }

    /**
     * Get contract text from config
     */
    public function getContractText(): string
    {
        $contractText = config('contract.text');

        if (!$contractText) {
            return 'متن قرارداد در حال حاضر در دسترس نیست.';
        }

        return $contractText;
    }

    /**
     * Get contract status for user and auction
     */
    public function getContractStatus(User $user, Auction $auction, ContractRole $role): array
    {
        $contract = ContractAgreement::where('user_id', $user->id)
            ->where('auction_id', $auction->id)
            ->where('role', $role)
            ->first();

        if (!$contract) {
            return [
                'status' => 'not_created',
                'message' => 'قرارداد هنوز ایجاد نشده است.',
                'can_verify' => true,
            ];
        }

        switch ($contract->status) {
            case ContractStatus::PENDING:
                return [
                    'status' => 'pending',
                    'message' => 'قرارداد در انتظار تأیید است.',
                    'can_verify' => true,
                    'contract' => $contract,
                ];
            case ContractStatus::CONFIRMED:
                return [
                    'status' => 'confirmed',
                    'message' => 'قرارداد تأیید شده است.',
                    'can_verify' => false,
                    'contract' => $contract,
                ];
            default:
                return [
                    'status' => 'unknown',
                    'message' => 'وضعیت قرارداد نامشخص است.',
                    'can_verify' => false,
                    'contract' => $contract,
                ];
        }
    }

    /**
     * Clean up expired OTP codes
     */
    public function cleanupExpiredOTPs(): int
    {
        $deleted = OtpCode::where('expires_at', '<', now())
            ->orWhere('created_at', '<', now()->subHours(24)) // Clean up codes older than 24 hours
            ->delete();

        Log::info('Expired OTP codes cleaned up', ['count' => $deleted]);

        return $deleted;
    }

    /**
     * Get OTP statistics
     */
    public function getOTPStats(): array
    {
        return [
            'pending' => OtpCode::where('used_at', null)
                ->where('expires_at', '>', now())
                ->count(),
            'used' => OtpCode::where('used_at', '!=', null)->count(),
            'expired' => OtpCode::where('expires_at', '<', now())
                ->where('used_at', null)
                ->count(),
            'total' => OtpCode::count(),
        ];
    }
}

