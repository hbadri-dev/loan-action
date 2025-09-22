<?php

namespace App\Enums;

enum PaymentType: string
{
    case BUYER_FEE = 'buyer_fee';
    case SELLER_FEE = 'seller_fee';
    case BUYER_PURCHASE_AMOUNT = 'buyer_purchase_amount';
    case LOAN_TRANSFER = 'loan_transfer';

    public function label(): string
    {
        return match($this) {
            self::BUYER_FEE => 'کارمزد خریدار',
            self::SELLER_FEE => 'کارمزد فروشنده',
            self::BUYER_PURCHASE_AMOUNT => 'مبلغ خرید خریدار',
            self::LOAN_TRANSFER => 'فیش انتقال وام',
        };
    }
}
