<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PENDING_REVIEW = 'pending_review';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'در انتظار پرداخت',
            self::PENDING_REVIEW => 'در انتظار بررسی',
            self::COMPLETED => 'تکمیل شده',
            self::FAILED => 'ناموفق',
            self::CANCELLED => 'لغو شده',
            self::EXPIRED => 'منقضی شده',
            self::APPROVED => 'تأیید شده',
            self::REJECTED => 'رد شده',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'blue',
            self::PENDING_REVIEW => 'yellow',
            self::COMPLETED => 'green',
            self::FAILED => 'red',
            self::CANCELLED => 'gray',
            self::EXPIRED => 'orange',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
        };
    }
}
