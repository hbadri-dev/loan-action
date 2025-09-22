<?php

namespace App\Notifications;

use App\Models\Bid;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class BidPlaced extends Notification implements ShouldQueue
{
    use Queueable;

    protected Bid $bid;

    /**
     * Create a new notification instance.
     */
    public function __construct(Bid $bid)
    {
        $this->bid = $bid;
    }

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
            'title' => 'پیشنهاد جدید در مزایده',
            'message' => sprintf(
                'یک پیشنهاد جدید به مبلغ %s تومان در مزایده "%s" ثبت شد.',
                number_format($this->bid->amount),
                $this->bid->auction->title
            ),
            'type' => 'bid_placed',
            'data' => [
                'bid_id' => $this->bid->id,
                'auction_id' => $this->bid->auction_id,
                'amount' => $this->bid->amount,
                'auction_title' => $this->bid->auction->title,
            ],
        ]);
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        return sprintf(
            'پیشنهاد جدید به مبلغ %s تومان در مزایده "%s" ثبت شد. %s',
            number_format($this->bid->amount),
            $this->bid->auction->title,
            config('app.name')
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'پیشنهاد جدید در مزایده',
            'message' => sprintf(
                'یک پیشنهاد جدید به مبلغ %s تومان در مزایده "%s" ثبت شد.',
                number_format($this->bid->amount),
                $this->bid->auction->title
            ),
            'type' => 'bid_placed',
            'bid_id' => $this->bid->id,
            'auction_id' => $this->bid->auction_id,
            'amount' => $this->bid->amount,
            'auction_title' => $this->bid->auction->title,
        ];
    }
}

