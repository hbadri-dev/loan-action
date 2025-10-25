<?php

namespace App\Notifications;

use App\Models\SellerSale;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class BuyerPaymentCompletedNew extends Notification
{

    public function __construct(
        public SellerSale $sale
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', SmsChannel::class];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => 'پرداخت خریدار تکمیل شد',
            'message' => sprintf(
                'خریدار %s مبلغ خرید را پرداخت کرد. لطفاً نسبت به انتقال وام اقدام کنید.',
                $this->sale->selectedBid->buyer->name ?? 'خریدار'
            ),
            'type' => 'buyer_payment_completed',
            'data' => [
                'sale_id' => $this->sale->id,
                'auction_id' => $this->sale->auction_id,
                'buyer_name' => $this->sale->selectedBid->buyer->name,
                'buyer_national_id' => $this->sale->selectedBid->buyer->national_id,
            ],
        ]);
    }

    /**
     * Get the SMS representation of the notification.
     * Uses BuyerPaymentCompletedNew template from Kavenegar
     */
    public function toSms(object $notifiable): array
    {
        $sellerName = $this->cleanToken($notifiable->name ?? 'فروشنده');
        $buyerName = $this->cleanToken($this->sale->selectedBid->buyer->name ?? 'خریدار');
        $buyerNationalId = $this->cleanToken($this->sale->selectedBid->buyer->national_id ?? 'نامشخص');

        return [
            'phone' => $notifiable->phone,
            'template' => 'BuyerPaymentCompletedNew',
            'tokens' => [$sellerName, $buyerName, $buyerNationalId],
        ];
    }

    /**
     * Clean token for Kavenegar (remove spaces, newlines, special chars)
     */
    private function cleanToken(string $token): string
    {
        // Remove newlines, tabs, and all whitespace characters
        $token = str_replace(["\n", "\r", "\t", " "], '', $token);

        // Remove underscores and other separators
        $token = str_replace(['_', '-', '.', ',', '،', '؛', ':', ';'], '', $token);

        // Remove any remaining special characters except Persian/Arabic letters and numbers
        $token = preg_replace('/[^\p{L}\p{N}]/u', '', $token);

        // If token is empty, use dash
        if (empty($token)) {
            $token = '-';
        }

        return $token;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'buyer_payment_completed',
            'sale_id' => $this->sale->id,
            'auction_id' => $this->sale->auction_id,
            'buyer_name' => $this->sale->selectedBid->buyer->name,
            'buyer_national_id' => $this->sale->selectedBid->buyer->national_id,
            'message' => sprintf('خریدار %s مبلغ خرید را پرداخت کرد.', $this->sale->selectedBid->buyer->name ?? 'خریدار'),
        ];
    }
}
