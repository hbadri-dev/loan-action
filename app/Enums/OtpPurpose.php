<?php

namespace App\Enums;

enum OtpPurpose: string
{
    case LOGIN_OTP = 'login-otp';
    case CONTRACT_CONFIRMATION = 'contract-confirmation';

    public function label(): string
    {
        return match($this) {
            self::LOGIN_OTP => 'ورود به سیستم',
            self::CONTRACT_CONFIRMATION => 'تأیید قرارداد',
        };
    }
}

