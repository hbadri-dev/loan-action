<?php

namespace App\Notifications;

use App\Models\PaymentReceipt;
use App\Enums\PaymentStatus;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class LoanVerificationResult extends Notification
{
    use Queueable;

    public function __construct(
        public PaymentReceipt $receipt
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', SmsChannel::class];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): DatabaseMessage
    {
        $isApproved = $this->receipt->status === PaymentStatus::APPROVED;
        $statusText = $isApproved ? 'تایید' : 'رد';

        return new DatabaseMessage([
            'title' => 'نتیجه احراز هویت وام',
            'message' => sprintf(
                'احراز هویت وام شما %s شد.',
                $statusText
            ),
            'type' => 'loan_verification_result',
            'data' => [
                'receipt_id' => $this->receipt->id,
                'auction_id' => $this->receipt->auction_id,
                'status' => $this->receipt->status->value,
                'is_approved' => $isApproved,
            ],
        ]);
    }

    /**
     * Get the SMS representation of the notification.
     * Uses LoanVerificationResult template from Kavenegar
     */
    public function toSms(object $notifiable): array
    {
        $isApproved = $this->receipt->status === PaymentStatus::APPROVED;
        $statusText = $isApproved ? 'تایید' : 'رد';
        $sellerName = $this->cleanToken($notifiable->name ?? 'کاربر');

        return [
            'phone' => $notifiable->phone,
            'template' => 'LoanVerificationResult',
            'token' => $sellerName,
            'token2' => $statusText,
        ];
    }

    /**
     * Clean token for Kavenegar (remove spaces, newlines, special chars)
     */
    private function cleanToken(string $token): string
    {
        // Remove newlines, tabs, and extra spaces
        $token = str_replace(["\n", "\r", "\t"], '', $token);

        // Remove multiple spaces
        $token = preg_replace('/\s+/', '', $token);

        // If token is empty, use a default
        if (empty($token)) {
            $token = 'کاربر';
        }

        return $token;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isApproved = $this->receipt->status === PaymentStatus::APPROVED;
        $statusText = $isApproved ? 'تایید' : 'رد';

        return [
            'type' => 'loan_verification_result',
            'receipt_id' => $this->receipt->id,
            'auction_id' => $this->receipt->auction_id,
            'status' => $this->receipt->status->value,
            'is_approved' => $isApproved,
            'message' => sprintf('احراز هویت وام شما %s شد.', $statusText),
        ];
    }
}
