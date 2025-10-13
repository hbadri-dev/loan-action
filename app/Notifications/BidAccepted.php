<?php

namespace App\Notifications;

use App\Models\Bid;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BidAccepted extends Notification
{
    use Queueable;

    public function __construct(
        public Bid $bid
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'sms'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('پیشنهاد شما پذیرفته شد')
                    ->line('تبریک! پیشنهاد شما پذیرفته شد.')
                    ->line('مزایده: ' . $this->bid->auction->title)
                    ->line('مبلغ پیشنهادی: ' . number_format($this->bid->amount) . ' تومان')
                    ->action('ادامه فرآیند', url('/buyer/auction/' . $this->bid->auction->id . '/purchase-payment'))
                    ->line('لطفاً مبلغ خرید را پرداخت کنید.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'bid_accepted',
            'bid_id' => $this->bid->id,
            'auction_id' => $this->bid->auction_id,
            'amount' => $this->bid->amount,
            'auction_title' => $this->bid->auction->title,
            'message' => 'پیشنهاد شما برای مزایده "' . $this->bid->auction->title . '" پذیرفته شد.',
        ];
    }

    /**
     * Get the SMS representation of the notification.
     * Uses SellerConfirmationNotice template from Kavenegar
     */
    public function toSms(object $notifiable): array
    {
        // Use buyer's phone number as token (fallback to name if phone is not available)
        $token = $notifiable->phone ?? $notifiable->name ?? 'کاربر';

        return [
            'phone' => $notifiable->phone,
            'template' => 'SellerConfirmationNotice',
            'token' => $token,
        ];
    }
}
