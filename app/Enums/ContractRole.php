<?php

namespace App\Enums;

enum ContractRole: string
{
    case BUYER = 'buyer';
    case SELLER = 'seller';

    public function label(): string
    {
        return match($this) {
            self::BUYER => 'خریدار',
            self::SELLER => 'فروشنده',
        };
    }
}

