<?php

namespace App\Listeners;

use App\Events\ReceiptApproved;
use App\Notifications\PaymentReceiptApproved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendReceiptApprovedNotification implements ShouldQueue
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
    public function handle(ReceiptApproved $event): void
    {
        try {
            // Notify the user whose receipt was approved
            $user = $event->receipt->user;
            $user->notify(new PaymentReceiptApproved($event->receipt));

            Log::info('Receipt approved notification sent', [
                'receipt_id' => $event->receipt->id,
                'user_id' => $user->id,
                'reviewer_id' => $event->reviewer->id,
                'type' => $event->receipt->type->value,
                'amount' => $event->receipt->amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send receipt approved notification', [
                'receipt_id' => $event->receipt->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(ReceiptApproved $event, \Throwable $exception): void
    {
        Log::error('Receipt approved notification failed', [
            'receipt_id' => $event->receipt->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
