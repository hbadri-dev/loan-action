<?php

namespace App\Notifications;

use App\Models\PaymentReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceiptApproved extends Notification implements ShouldQueue
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
                    ->subject('تأیید رسید پرداخت')
                    ->line('رسید پرداخت شما تأیید شد.')
                    ->line('مبلغ: ' . number_format($this->receipt->amount) . ' تومان')
                    ->line('نوع: ' . $this->receipt->type->label())
                    ->action('مشاهده جزئیات', url('/dashboard'))
                    ->line('با تشکر از شما!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_approved',
            'receipt_id' => $this->receipt->id,
            'amount' => $this->receipt->amount,
            'payment_type' => $this->receipt->type->value,
            'message' => 'رسید پرداخت شما به مبلغ ' . number_format($this->receipt->amount) . ' تومان تأیید شد.',
        ];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): array
    {
        $message = "رسید پرداخت شما به مبلغ " . number_format($this->receipt->amount) . " تومان تأیید شد. لطفاً به پنل کاربری مراجعه کنید.";

        return [
            'phone' => $notifiable->phone,
            'message' => $message,
        ];
    }
}

