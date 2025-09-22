# SMS Service Documentation

## Overview

The SMS service provides a unified interface for sending SMS messages and OTP codes using the Kavenegar SMS service. It includes sandbox mode for development and testing.

## Configuration

### Environment Variables

Add the following to your `.env` file:

```env
# Kavenegar SMS Service
KAVENEGAR_API_KEY=your_api_key_here
KAVENEGAR_SENDER=10008663
KAVENEGAR_BASE_URL=https://api.kavenegar.com/v1

# SMS Configuration
SMS_SANDBOX=true
SMS_DRIVER=kavenegar
SMS_RATE_LIMIT_ATTEMPTS=5
SMS_RATE_LIMIT_DECAY=60
SMS_OTP_LENGTH=6
SMS_OTP_EXPIRY=2
SMS_MAX_RETRIES=3
SMS_TIMEOUT=30
SMS_CONNECT_TIMEOUT=10
```

### Configuration File

The SMS service configuration is stored in `config/sms.php` and includes:

- Service provider settings
- Sandbox mode toggle
- Rate limiting configuration
- SMS templates
- General settings

## Usage

### Basic SMS Sending

```php
use App\Services\SMS\KavenegarService;

$smsService = new KavenegarService();

// Send a regular SMS
$result = $smsService->sendMessage('09123456789', 'Hello World!');

if ($result) {
    echo "SMS sent successfully!";
} else {
    echo "Failed to send SMS";
}
```

### OTP Sending

```php
// Generate OTP code
$code = $smsService->generateOTP();

// Send login OTP
$result = $smsService->sendLoginOTP('09123456789', $code);

// Send contract confirmation OTP
$result = $smsService->sendContractOTP('09123456789', $code);
```

### Mobile Number Validation

```php
// Validate mobile number format
$isValid = $smsService->validateMobile('09123456789');

// Format mobile number for API
$formatted = $smsService->formatMobile('09123456789');
// Returns: 989123456789
```

## Sandbox Mode

When `SMS_SANDBOX=true`, the service will log messages instead of sending them:

```php
// This will log the message instead of sending it
$smsService->sendMessage('09123456789', 'Test message');
```

Check your logs for sandbox messages:

```
[INFO] SMS Sandbox Mode - Message would be sent: {"mobile":"09123456789","message":"Test message",...}
```

## Testing

### Artisan Command

Test the SMS service using the artisan command:

```bash
# Send a test SMS
php artisan sms:test 09123456789 --message="Hello World"

# Send a test OTP
php artisan sms:test 09123456789 --otp
```

### Unit Tests

Run the SMS service tests:

```bash
php artisan test tests/Feature/SmsServiceTest.php
```

## Error Handling

The service throws exceptions for various error conditions:

```php
try {
    $smsService->sendLoginOTP('09123456789', '123456');
} catch (\Exception $e) {
    // Handle error
    echo "Error: " . $e->getMessage();
}
```

### Common Error Scenarios

1. **Invalid API Key**: Missing or invalid Kavenegar API key
2. **Network Errors**: Connection timeout or HTTP errors
3. **API Errors**: Kavenegar API returns non-200 status
4. **Invalid Mobile**: Mobile number format validation fails

## Rate Limiting

The service includes built-in rate limiting:

- Maximum 5 attempts per hour per phone number
- 1 attempt per minute per phone number
- Configurable via environment variables

## Templates

The service supports the following SMS templates:

- `login-otp`: For user login OTP
- `contract-confirmation`: For contract confirmation OTP
- `payment-approved`: For payment approval notifications
- `payment-rejected`: For payment rejection notifications
- `bid-accepted`: For bid acceptance notifications
- `sale-completed`: For sale completion notifications

## Logging

All SMS operations are logged with detailed information:

- Success logs include response data
- Error logs include error messages and context
- Sandbox mode logs include all parameters

## Security Considerations

1. **API Key Protection**: Never commit API keys to version control
2. **Rate Limiting**: Implement proper rate limiting to prevent abuse
3. **Input Validation**: Always validate mobile numbers before sending
4. **Error Handling**: Don't expose sensitive information in error messages

## Troubleshooting

### Common Issues

1. **SMS not sending**: Check API key and sandbox mode
2. **Invalid mobile format**: Ensure mobile numbers start with 09
3. **Rate limiting**: Check if you've exceeded rate limits
4. **Network errors**: Check internet connection and API endpoint

### Debug Mode

Enable detailed logging by setting log level to debug in your logging configuration.

## API Reference

### KavenegarService Methods

- `sendMessage(string $mobile, string $message, string $sender = null): bool`
- `sendLoginOTP(string $phone, string $code): bool`
- `sendContractOTP(string $phone, string $code): bool`
- `generateOTP(int $length = null): string`
- `validateMobile(string $mobile): bool`
- `formatMobile(string $mobile): string`
- `getOTPExpiryMinutes(): int`

### Configuration Options

- `sms.sandbox`: Enable/disable sandbox mode
- `sms.services.kavenegar.api_key`: Kavenegar API key
- `sms.services.kavenegar.base_url`: API base URL
- `sms.services.kavenegar.sender`: Default sender number
- `sms.settings.otp_length`: OTP code length
- `sms.settings.otp_expiry_minutes`: OTP expiry time
- `sms.settings.timeout`: HTTP timeout
- `sms.settings.connect_timeout`: Connection timeout

