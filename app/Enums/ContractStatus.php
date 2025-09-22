<?php

namespace App\Enums;

enum ContractStatus: string
{
    case PENDING = 'pending';
    case OTP_SENT = 'otp_sent';
    case CONFIRMED = 'confirmed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'در انتظار',
            self::OTP_SENT => 'کد ارسال شده',
            self::CONFIRMED => 'تأیید شده',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::OTP_SENT => 'orange',
            self::CONFIRMED => 'green',
        };
    }
}
