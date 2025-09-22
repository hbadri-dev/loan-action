<?php

namespace App\Listeners;

use App\Events\BidPlaced;
use App\Notifications\BidPlaced as BidPlacedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendBidPlacedNotification implements ShouldQueue
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
    public function handle(BidPlaced $event): void
    {
        try {
            // Notify seller about new bid
            $seller = $event->bid->auction->user;
            $seller->notify(new BidPlacedNotification($event->bid));

            Log::info('Bid placed notification sent', [
                'bid_id' => $event->bid->id,
                'seller_id' => $seller->id,
                'auction_id' => $event->bid->auction_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send bid placed notification', [
                'bid_id' => $event->bid->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(BidPlaced $event, \Throwable $exception): void
    {
        Log::error('Bid placed notification failed', [
            'bid_id' => $event->bid->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}

