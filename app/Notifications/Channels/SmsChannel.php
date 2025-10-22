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
            // Check if it's a template-based SMS with multiple tokens
            if (isset($data['template']) && isset($data['tokens']) && is_array($data['tokens'])) {
                // Send template-based SMS with multiple tokens
                $this->kavenegarService->sendTemplateSMSWithTokens($data['phone'], $data['tokens'], $data['template']);
            } elseif (isset($data['template']) && isset($data['token'])) {
                // Check for three tokens first, then two, then single
                if (isset($data['token2']) && isset($data['token3'])) {
                    // Three tokens: token, token2, token3
                    $this->kavenegarService->sendTemplateSMSWithTokens($data['phone'], [$data['token'], $data['token2'], $data['token3']], $data['template']);
                } elseif (isset($data['token2'])) {
                    // Two tokens: token, token2
                    $this->kavenegarService->sendTemplateSMSWithTokens($data['phone'], [$data['token'], $data['token2']], $data['template']);
                } else {
                    // Single token
                    $this->kavenegarService->sendTemplateSMS($data['phone'], $data['token'], $data['template']);
                }
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
