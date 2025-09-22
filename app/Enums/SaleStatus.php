<?php

namespace App\Enums;

enum SaleStatus: string
{
    case INITIATED = 'initiated';
    case CONTRACT_CONFIRMED = 'contract_confirmed';
    case FEE_APPROVED = 'fee_approved';
    case OFFER_ACCEPTED = 'offer_accepted';
    case AWAITING_BUYER_PAYMENT = 'awaiting_buyer_payment';
    case BUYER_PAYMENT_APPROVED = 'buyer_payment_approved';
    case LOAN_TRANSFERRED = 'loan_transferred';
    case TRANSFER_CONFIRMED = 'transfer_confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::INITIATED => 'شروع شده',
            self::CONTRACT_CONFIRMED => 'قرارداد تأیید شده',
            self::FEE_APPROVED => 'کارمزد تأیید شده',
            self::OFFER_ACCEPTED => 'پیشنهاد پذیرفته شده',
            self::AWAITING_BUYER_PAYMENT => 'در انتظار پرداخت خریدار',
            self::BUYER_PAYMENT_APPROVED => 'پرداخت خریدار تأیید شده',
            self::LOAN_TRANSFERRED => 'وام انتقال یافته',
            self::TRANSFER_CONFIRMED => 'انتقال تأیید شده',
            self::COMPLETED => 'تکمیل شده',
            self::CANCELLED => 'لغو شده',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::INITIATED => 'blue',
            self::CONTRACT_CONFIRMED => 'green',
            self::FEE_APPROVED => 'green',
            self::OFFER_ACCEPTED => 'green',
            self::AWAITING_BUYER_PAYMENT => 'yellow',
            self::BUYER_PAYMENT_APPROVED => 'green',
            self::LOAN_TRANSFERRED => 'green',
            self::TRANSFER_CONFIRMED => 'green',
            self::COMPLETED => 'blue',
            self::CANCELLED => 'red',
        };
    }
}
