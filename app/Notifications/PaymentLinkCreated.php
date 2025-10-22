<?php

namespace App\Notifications;

use App\Models\SellerSale;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class PaymentLinkCreated extends Notification
{
    use Queueable;

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
            'title' => 'لینک پرداخت آماده شد',
            'message' => sprintf(
                'لینک پرداخت مبلغ خرید برای مزایده "%s" آماده شد.',
                $this->sale->auction->title
            ),
            'type' => 'payment_link_created',
            'data' => [
                'sale_id' => $this->sale->id,
                'auction_id' => $this->sale->auction_id,
                'payment_link' => $this->sale->payment_link,
            ],
        ]);
    }

    /**
     * Get the SMS representation of the notification.
     * Uses PaymentLinkCreated template from Kavenegar
     */
    public function toSms(object $notifiable): array
    {
        $buyerName = $this->cleanToken($notifiable->name ?? 'کاربر');
        $auctionTitle = $this->cleanToken($this->sale->auction->title ?? 'نامشخص');

        return [
            'phone' => $notifiable->phone,
            'template' => 'PaymentLinkCreated',
            'token' => $buyerName,
            'token2' => $auctionTitle,
        ];
    }

    /**
     * Clean token for Kavenegar (remove spaces, newlines, special chars)
     */
    private function cleanToken(string $token): string
    {
        // Remove newlines, tabs, and extra spaces
        $token = str_replace(["\n", "\r", "\t"], '', $token);
        
        // Remove multiple spaces
        $token = preg_replace('/\s+/', '', $token);
        
        // If token is empty, use a default
        if (empty($token)) {
            $token = 'کاربر';
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
            'type' => 'payment_link_created',
            'sale_id' => $this->sale->id,
            'auction_id' => $this->sale->auction_id,
            'auction_title' => $this->sale->auction->title,
            'payment_link' => $this->sale->payment_link,
            'message' => sprintf('لینک پرداخت مبلغ خرید برای مزایده "%s" آماده شد.', $this->sale->auction->title),
        ];
    }
}


