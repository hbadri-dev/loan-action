<?php

namespace App\Events;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidAccepted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Bid $bid;
    public Auction $auction;

    /**
     * Create a new event instance.
     */
    public function __construct(Bid $bid, Auction $auction)
    {
        $this->bid = $bid;
        $this->auction = $auction;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->bid->user_id), // Notify buyer
            new PrivateChannel('user.' . $this->auction->user_id), // Notify seller
            new PrivateChannel('auction.' . $this->auction->id), // Notify auction participants
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
            'bid' => [
                'id' => $this->bid->id,
                'amount' => $this->bid->amount,
                'status' => $this->bid->status->value,
                'user_id' => $this->bid->user_id,
                'accepted_at' => now()->toISOString(),
            ],
            'auction' => [
                'id' => $this->auction->id,
                'title' => $this->auction->title,
                'status' => $this->auction->status->value,
                'locked_at' => $this->auction->locked_at?->toISOString(),
            ],
            'seller' => [
                'id' => $this->auction->user_id,
                'name' => $this->auction->user->name,
            ],
            'buyer' => [
                'id' => $this->bid->user_id,
                'name' => $this->bid->user->name,
            ],
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'bid.accepted';
    }
}

