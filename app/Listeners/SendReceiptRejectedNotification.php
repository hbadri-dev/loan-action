<?php

namespace App\Listeners;

use App\Events\ReceiptRejected;
use App\Notifications\PaymentReceiptRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendReceiptRejectedNotification implements ShouldQueue
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
    public function handle(ReceiptRejected $event): void
    {
        try {
            // Notify the user whose receipt was rejected
            $user = $event->receipt->user;
            $user->notify(new PaymentReceiptRejected($event->receipt));

            Log::info('Receipt rejected notification sent', [
                'receipt_id' => $event->receipt->id,
                'user_id' => $user->id,
                'reviewer_id' => $event->reviewer->id,
                'type' => $event->receipt->type->value,
                'amount' => $event->receipt->amount,
                'reason' => $event->reason,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send receipt rejected notification', [
                'receipt_id' => $event->receipt->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(ReceiptRejected $event, \Throwable $exception): void
    {
        Log::error('Receipt rejected notification failed', [
            'receipt_id' => $event->receipt->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
