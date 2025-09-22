<?php

namespace Tests\Feature;

use App\Models\OtpCode;
use App\Models\User;
use App\Services\SMS\KavenegarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OtpAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_request_otp_for_existing_user()
    {
        // Create a user with buyer role
        $user = User::factory()->create([
            'phone' => '09123456789'
        ]);
        $user->assignRole('buyer');

        // Mock KavenegarService
        $this->mock(KavenegarService::class, function ($mock) {
            $mock->shouldReceive('generateOTP')
                ->once()
                ->andReturn('123456');
            $mock->shouldReceive('sendLoginOTP')
                ->once()
                ->with('09123456789', '123456')
                ->andReturn(true);
        });

        $response = $this->postJson('/auth/otp/request', [
            'phone' => '09123456789',
            'purpose' => 'login-otp'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'کد تأیید ارسال شد.',
            'expires_in' => 120
        ]);

        // Check OTP was stored in database
        $this->assertDatabaseHas('otp_codes', [
            'phone' => '09123456789',
            'code' => '123456',
            'purpose' => 'login-otp'
        ]);
    }

    public function test_can_verify_otp_and_authenticate_user()
    {
        // Create a user with buyer role
        $user = User::factory()->create([
            'phone' => '09123456789',
            'is_phone_verified' => false
        ]);
        $user->assignRole('buyer');

        // Create OTP code
        $otpCode = OtpCode::create([
            'phone' => '09123456789',
            'code' => '123456',
            'purpose' => 'login-otp',
            'expires_at' => now()->addMinutes(2)
        ]);

        $response = $this->postJson('/auth/otp/verify', [
            'phone' => '09123456789',
            'code' => '123456'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'token',
            'user' => ['id', 'name', 'phone', 'email'],
            'roles',
            'redirect_url'
        ]);

        // Check user is now phone verified
        $user->refresh();
        $this->assertTrue($user->is_phone_verified);

        // Check OTP is marked as used
        $otpCode->refresh();
        $this->assertNotNull($otpCode->used_at);
    }

    public function test_cannot_verify_expired_otp()
    {
        // Create OTP code that's expired
        OtpCode::create([
            'phone' => '09123456789',
            'code' => '123456',
            'purpose' => 'login-otp',
            'expires_at' => now()->subMinutes(1)
        ]);

        $response = $this->postJson('/auth/otp/verify', [
            'phone' => '09123456789',
            'code' => '123456'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code']);
    }

    public function test_cannot_request_otp_for_non_existing_user()
    {
        $response = $this->postJson('/auth/otp/request', [
            'phone' => '09999999999',
            'purpose' => 'login-otp'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['phone']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

