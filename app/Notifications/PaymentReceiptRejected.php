<?php

namespace App\Notifications;

use App\Models\PaymentReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceiptRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PaymentReceipt $receipt
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
                    ->subject('رد رسید پرداخت')
                    ->line('متأسفانه رسید پرداخت شما رد شد.')
                    ->line('مبلغ: ' . number_format($this->receipt->amount) . ' تومان')
                    ->line('دلیل: ' . $this->receipt->reject_reason)
                    ->action('آپلود مجدد رسید', url('/dashboard'))
                    ->line('لطفاً رسید جدیدی آپلود کنید.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_rejected',
            'receipt_id' => $this->receipt->id,
            'amount' => $this->receipt->amount,
            'payment_type' => $this->receipt->type->value,
            'reject_reason' => $this->receipt->reject_reason,
            'message' => 'رسید پرداخت شما رد شد. دلیل: ' . $this->receipt->reject_reason,
        ];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): array
    {
        $message = "رسید پرداخت شما رد شد. لطفاً رسید جدیدی آپلود کنید. پنل کاربری: " . url('/dashboard');

        return [
            'phone' => $notifiable->phone,
            'message' => $message,
        ];
    }
}

