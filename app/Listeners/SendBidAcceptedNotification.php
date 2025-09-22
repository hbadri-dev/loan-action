<?php

namespace App\Listeners;

use App\Events\BidAccepted;
use App\Notifications\BidAccepted as BidAcceptedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendBidAcceptedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BidAccepted $event): void
    {
        try {
            // Notify the buyer
            $buyer = $event->bid->user;
            $buyer->notify(new BidAcceptedNotification($event->bid));

            Log::info('Bid accepted notification sent', [
                'bid_id' => $event->bid->id,
                'buyer_id' => $buyer->id,
                'auction_id' => $event->auction->id,
                'seller_id' => $event->auction->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send bid accepted notification', [
                'bid_id' => $event->bid->id,
                'auction_id' => $event->auction->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(BidAccepted $event, \Throwable $exception): void
    {
        Log::error('Bid accepted notification failed', [
            'bid_id' => $event->bid->id,
            'auction_id' => $event->auction->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
