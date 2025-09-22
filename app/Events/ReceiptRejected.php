<?php

namespace App\Events;

use App\Models\PaymentReceipt;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReceiptRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PaymentReceipt $receipt;
    public User $reviewer;
    public string $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(PaymentReceipt $receipt, User $reviewer, string $reason)
    {
        $this->receipt = $receipt;
        $this->reviewer = $reviewer;
        $this->reason = $reason;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->receipt->user_id),
            new PrivateChannel('auction.' . $this->receipt->auction_id),
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
            'receipt' => [
                'id' => $this->receipt->id,
                'type' => $this->receipt->type->value,
                'amount' => $this->receipt->amount,
                'status' => $this->receipt->status->value,
                'reject_reason' => $this->receipt->reject_reason,
                'reviewed_at' => $this->receipt->reviewed_at?->toISOString(),
            ],
            'auction' => [
                'id' => $this->receipt->auction_id,
                'title' => $this->receipt->auction->title,
            ],
            'reviewer' => [
                'id' => $this->reviewer->id,
                'name' => $this->reviewer->name,
            ],
            'reason' => $this->reason,
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'receipt.rejected';
    }
}

