<?php

namespace App\Notifications\Channels;

use App\Services\SMS\KavenegarService;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    protected KavenegarService $kavenegarService;

    public function __construct(KavenegarService $kavenegarService)
    {
        $this->kavenegarService = $kavenegarService;
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        $data = $notification->toSms($notifiable);

        if (!isset($data['phone']) || !isset($data['message'])) {
            return;
        }

        // Dispatch SMS to queue
        \App\Jobs\SendSmsJob::dispatch($data['phone'], $data['message']);
    }
}

