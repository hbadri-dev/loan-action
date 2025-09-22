<?php

namespace App\Jobs;

use App\Services\SMS\KavenegarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $phone,
        public string $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(KavenegarService $kavenegarService): void
    {
        try {
            $result = $kavenegarService->sendMessage($this->phone, $this->message);

            Log::info('SMS sent successfully', [
                'phone' => $this->phone,
                'message' => $this->message,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send SMS', [
                'phone' => $this->phone,
                'message' => $this->message,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}

