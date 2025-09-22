<?php

namespace App\Notifications;

use App\Models\SellerSale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SaleCompleted extends Notification implements ShouldQueue
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
        return ['database', 'sms'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $isBuyer = $notifiable->id === $this->sale->selectedBid->buyer_id;
        $subject = $isBuyer ? 'فروش تکمیل شد' : 'فروش شما تکمیل شد';
        $message = $isBuyer
            ? 'فروش وام تکمیل شد. وام به نام شما منتقل شده است.'
            : 'فروش شما تکمیل شد. مبلغ به حساب شما واریز خواهد شد.';

        return (new MailMessage)
                    ->subject($subject)
                    ->line($message)
                    ->line('مزایده: ' . $this->sale->auction->title)
                    ->line('مبلغ فروش: ' . number_format($this->sale->selectedBid->amount) . ' تومان')
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
        $isBuyer = $notifiable->id === $this->sale->selectedBid->buyer_id;
        $message = $isBuyer
            ? 'فروش وام تکمیل شد. وام به نام شما منتقل شده است.'
            : 'فروش شما تکمیل شد. مبلغ به حساب شما واریز خواهد شد.';

        return [
            'type' => 'sale_completed',
            'sale_id' => $this->sale->id,
            'auction_id' => $this->sale->auction_id,
            'amount' => $this->sale->selectedBid->amount,
            'auction_title' => $this->sale->auction->title,
            'is_buyer' => $isBuyer,
            'message' => $message,
        ];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): array
    {
        $isBuyer = $notifiable->id === $this->sale->selectedBid->buyer_id;
        $message = $isBuyer
            ? "فروش وام تکمیل شد. وام به نام شما منتقل شده است."
            : "فروش شما تکمیل شد. مبلغ به حساب شما واریز خواهد شد.";

        return [
            'phone' => $notifiable->phone,
            'message' => $message,
        ];
    }
}

