<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING_REVIEW = 'pending_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING_REVIEW => 'در انتظار بررسی',
            self::APPROVED => 'تأیید شده',
            self::REJECTED => 'رد شده',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING_REVIEW => 'yellow',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
        };
    }
}
