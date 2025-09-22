<?php

namespace App\Console\Commands;

use App\Services\SMS\KavenegarService;
use Illuminate\Console\Command;

class TestSmsService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {phone} {--message=Test message} {--otp}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test SMS service by sending a message or OTP';

    /**
     * Execute the console command.
     */
    public function handle(KavenegarService $smsService): int
    {
        $phone = $this->argument('phone');
        $message = $this->option('message');
        $sendOtp = $this->option('otp');

        $this->info("Testing SMS service...");
        $this->info("Phone: {$phone}");
        $this->info("Sandbox mode: " . (config('sms.sandbox') ? 'Enabled' : 'Disabled'));

        try {
            if ($sendOtp) {
                $code = $smsService->generateOTP();
                $this->info("Generated OTP: {$code}");

                $result = $smsService->sendLoginOTP($phone, $code);

                if ($result) {
                    $this->info("✅ Login OTP sent successfully!");
                } else {
                    $this->error("❌ Failed to send login OTP");
                    return 1;
                }
            } else {
                $result = $smsService->sendMessage($phone, $message);

                if ($result) {
                    $this->info("✅ SMS sent successfully!");
                } else {
                    $this->error("❌ Failed to send SMS");
                    return 1;
                }
            }

            if (config('sms.sandbox')) {
                $this->warn("⚠️  Sandbox mode is enabled - no actual SMS was sent");
                $this->info("Check the logs for the sandbox message details");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}

