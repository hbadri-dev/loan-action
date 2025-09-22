<?php

namespace App\Enums;

enum BidStatus: string
{
    case PENDING = 'pending';
    case OUTBID = 'outbid';
    case HIGHEST = 'highest';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'در انتظار',
            self::OUTBID => 'پیشنهاد بالاتر',
            self::HIGHEST => 'بالاترین پیشنهاد',
            self::ACCEPTED => 'پذیرفته شده',
            self::REJECTED => 'رد شده',
            self::CANCELLED => 'لغو شده',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::OUTBID => 'orange',
            self::HIGHEST => 'green',
            self::ACCEPTED => 'blue',
            self::REJECTED => 'red',
            self::CANCELLED => 'gray',
        };
    }
}
