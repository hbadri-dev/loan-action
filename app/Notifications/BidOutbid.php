<?php

namespace App\Notifications;

use App\Models\Bid;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class BidOutbid extends Notification implements ShouldQueue
{
    use Queueable;

    protected Bid $outbidBid;
    protected Bid $newBid;

    /**
     * Create a new notification instance.
     */
    public function __construct(Bid $outbidBid, Bid $newBid)
    {
        $this->outbidBid = $outbidBid;
        $this->newBid = $newBid;
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
            'title' => 'پیشنهاد شما رد شد',
            'message' => sprintf(
                'پیشنهاد شما به مبلغ %s تومان در مزایده "%s" توسط پیشنهاد بالاتری (%s تومان) رد شد.',
                number_format($this->outbidBid->amount),
                $this->outbidBid->auction->title,
                number_format($this->newBid->amount)
            ),
            'type' => 'bid_outbid',
            'data' => [
                'outbid_bid_id' => $this->outbidBid->id,
                'new_bid_id' => $this->newBid->id,
                'auction_id' => $this->outbidBid->auction_id,
                'outbid_amount' => $this->outbidBid->amount,
                'new_amount' => $this->newBid->amount,
                'auction_title' => $this->outbidBid->auction->title,
            ],
        ]);
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        return sprintf(
            'پیشنهاد شما به مبلغ %s تومان در مزایده "%s" توسط پیشنهاد بالاتری رد شد. %s',
            number_format($this->outbidBid->amount),
            $this->outbidBid->auction->title,
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
            'title' => 'پیشنهاد شما رد شد',
            'message' => sprintf(
                'پیشنهاد شما به مبلغ %s تومان در مزایده "%s" توسط پیشنهاد بالاتری (%s تومان) رد شد.',
                number_format($this->outbidBid->amount),
                $this->outbidBid->auction->title,
                number_format($this->newBid->amount)
            ),
            'type' => 'bid_outbid',
            'outbid_bid_id' => $this->outbidBid->id,
            'new_bid_id' => $this->newBid->id,
            'auction_id' => $this->outbidBid->auction_id,
            'outbid_amount' => $this->outbidBid->amount,
            'new_amount' => $this->newBid->amount,
            'auction_title' => $this->outbidBid->auction->title,
        ];
    }
}

