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

        if (!isset($data['phone'])) {
            return;
        }

        try {
            // Check if it's a template-based SMS or simple message
            if (isset($data['template']) && isset($data['token'])) {
                // Send template-based SMS immediately
                $this->kavenegarService->sendTemplateSMS($data['phone'], $data['token'], $data['template']);
            } elseif (isset($data['message'])) {
                // Send simple message SMS immediately
                $this->kavenegarService->sendMessage($data['phone'], $data['message']);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send SMS notification', [
                'phone' => $data['phone'],
                'error' => $e->getMessage(),
                'notification' => get_class($notification)
            ]);
        }
    }
}
