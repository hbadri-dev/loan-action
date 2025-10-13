<?php

namespace App\Jobs;

use App\Services\SMS\KavenegarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTemplateSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $phone,
        public string $token,
        public string $template
    ) {}

    /**
     * Execute the job.
     */
    public function handle(KavenegarService $kavenegarService): void
    {
        try {
            $result = $kavenegarService->sendTemplateSMS($this->phone, $this->token, $this->template);

            Log::info('Template SMS sent successfully via job', [
                'phone' => $this->phone,
                'template' => $this->template,
                'token' => $this->token,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send template SMS via job', [
                'phone' => $this->phone,
                'template' => $this->template,
                'token' => $this->token,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
