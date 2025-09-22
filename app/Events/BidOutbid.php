<?php

namespace App\Events;

use App\Models\Bid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidOutbid implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Bid $outbidBid;
    public Bid $newBid;

    /**
     * Create a new event instance.
     */
    public function __construct(Bid $outbidBid, Bid $newBid)
    {
        $this->outbidBid = $outbidBid;
        $this->newBid = $newBid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->outbidBid->user_id),
            new PrivateChannel('auction.' . $this->outbidBid->auction_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'outbid_bid' => [
                'id' => $this->outbidBid->id,
                'amount' => $this->outbidBid->amount,
                'status' => $this->outbidBid->status->value,
                'user_id' => $this->outbidBid->user_id,
            ],
            'new_bid' => [
                'id' => $this->newBid->id,
                'amount' => $this->newBid->amount,
                'status' => $this->newBid->status->value,
                'user_id' => $this->newBid->user_id,
            ],
            'auction' => [
                'id' => $this->outbidBid->auction_id,
                'title' => $this->outbidBid->auction->title,
            ],
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'bid.outbid';
    }
}

