<?php

namespace Tests\Feature;

use App\Services\SMS\KavenegarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SmsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KavenegarService $smsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->smsService = new KavenegarService();
    }

    /** @test */
    public function it_can_generate_otp_code()
    {
        $code = $this->smsService->generateOTP();

        $this->assertIsString($code);
        $this->assertEquals(6, strlen($code));
        $this->assertTrue(is_numeric($code));
    }

    /** @test */
    public function it_can_validate_mobile_number()
    {
        $validMobile = '09123456789';
        $invalidMobile = '1234567890';

        $this->assertTrue($this->smsService->validateMobile($validMobile));
        $this->assertFalse($this->smsService->validateMobile($invalidMobile));
    }

    /** @test */
    public function it_can_format_mobile_number()
    {
        $mobile = '09123456789';
        $formatted = $this->smsService->formatMobile($mobile);

        $this->assertEquals('989123456789', $formatted);
    }

    /** @test */
    public function it_uses_sandbox_mode_when_enabled()
    {
        // Enable sandbox mode
        config(['sms.sandbox' => true]);

        // Mock the log facade
        Log::shouldReceive('info')
            ->once()
            ->with('SMS Sandbox Mode - OTP would be sent', \Mockery::type('array'));

        $result = $this->smsService->sendLoginOTP('09123456789', '123456');

        $this->assertTrue($result);
    }

    /** @test */
    public function it_uses_sandbox_mode_for_regular_sms()
    {
        // Enable sandbox mode
        config(['sms.sandbox' => true]);

        // Mock the log facade
        Log::shouldReceive('info')
            ->once()
            ->with('SMS Sandbox Mode - Message would be sent', \Mockery::type('array'));

        $result = $this->smsService->sendMessage('09123456789', 'Test message');

        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_get_otp_expiry_minutes()
    {
        $expiry = $this->smsService->getOTPExpiryMinutes();

        $this->assertIsInt($expiry);
        $this->assertGreaterThan(0, $expiry);
    }

    /** @test */
    public function it_throws_exception_when_api_key_is_missing()
    {
        // Set empty API key
        config(['sms.services.kavenegar.api_key' => '']);

        $this->expectException(\Exception::class);

        $this->smsService->sendLoginOTP('09123456789', '123456');
    }
}

