<?php

namespace App\Listeners;

use App\Events\BidOutbid;
use App\Notifications\BidOutbid as BidOutbidNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendBidOutbidNotification implements ShouldQueue
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
    public function handle(BidOutbid $event): void
    {
        try {
            // Notify the outbid user
            $outbidUser = $event->outbidBid->user;
            $outbidUser->notify(new BidOutbidNotification($event->outbidBid, $event->newBid));

            Log::info('Bid outbid notification sent', [
                'outbid_bid_id' => $event->outbidBid->id,
                'new_bid_id' => $event->newBid->id,
                'outbid_user_id' => $outbidUser->id,
                'auction_id' => $event->outbidBid->auction_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send bid outbid notification', [
                'outbid_bid_id' => $event->outbidBid->id,
                'new_bid_id' => $event->newBid->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(BidOutbid $event, \Throwable $exception): void
    {
        Log::error('Bid outbid notification failed', [
            'outbid_bid_id' => $event->outbidBid->id,
            'new_bid_id' => $event->newBid->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}

