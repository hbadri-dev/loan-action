<?php

namespace App\Enums;

enum AuctionStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case LOCKED = 'locked';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'پیش‌نویس',
            self::ACTIVE => 'فعال',
            self::LOCKED => 'قفل شده',
            self::COMPLETED => 'تکمیل شده',
            self::CANCELLED => 'لغو شده',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::ACTIVE => 'green',
            self::LOCKED => 'orange',
            self::COMPLETED => 'blue',
            self::CANCELLED => 'red',
        };
    }
}
