# SMS Notification for Bid Acceptance - Implementation Guide

## Overview

When a seller accepts a bid in the auction platform, an SMS notification is automatically sent to the buyer using Kavenegar's template-based SMS service.

## Implementation Details

### 1. Template Information

- **Template Name**: `SellerConfirmationNotice`
- **Template Content**:

  ```
  کاربر %token گرامی
  یک فروشنده قیمت پیشنهادی شما را تایید کرده است. لطفا در اسرع وقت نسبت به واریز کل مبلغ وام در پلتفرم اقدام فرمایید.

  nationalkind.ir
  وام یار
  ```

- **Token**: Buyer's phone number (fallback to name if phone is unavailable)

### 2. Files Modified

#### A. KavenegarService.php

**Location**: `app/Services/SMS/KavenegarService.php`

Added a new method `sendTemplateSMS()` to support template-based SMS:

```php
public function sendTemplateSMS(string $mobile, string $token, string $template): bool
```

This method:

- Uses Kavenegar's `verify/lookup` API endpoint
- Supports sandbox mode for testing
- Includes comprehensive logging and error handling
- Automatically formats mobile numbers

#### B. SendTemplateSmsJob.php

**Location**: `app/Jobs/SendTemplateSmsJob.php` (NEW FILE)

Created a new queue job to handle template-based SMS sending:

```php
class SendTemplateSmsJob implements ShouldQueue
{
    public function __construct(
        public string $phone,
        public string $token,
        public string $template
    ) {}
}
```

Benefits:

- Queued processing for better performance
- Automatic retry on failure
- Comprehensive logging

#### C. SmsChannel.php

**Location**: `app/Notifications/Channels/SmsChannel.php`

Enhanced to support both simple messages and template-based SMS:

```php
public function send($notifiable, Notification $notification)
{
    // Check if template-based or simple message
    if (isset($data['template']) && isset($data['token'])) {
        \App\Jobs\SendTemplateSmsJob::dispatch(...);
    } elseif (isset($data['message'])) {
        \App\Jobs\SendSmsJob::dispatch(...);
    }
}
```

#### D. BidAccepted.php

**Location**: `app/Notifications/BidAccepted.php`

Updated the `toSms()` method to use the template:

```php
public function toSms(object $notifiable): array
{
    $token = $notifiable->phone ?? $notifiable->name ?? 'کاربر';

    return [
        'phone' => $notifiable->phone,
        'template' => 'SellerConfirmationNotice',
        'token' => $token,
    ];
}
```

### 3. Flow Diagram

```
Seller accepts bid
    ↓
SellerController::acceptBid() [Line 654]
    ↓
$buyer->notify(new BidAccepted($bid))
    ↓
BidAccepted::toSms() returns template data
    ↓
SmsChannel::send() dispatches SendTemplateSmsJob
    ↓
SendTemplateSmsJob::handle() calls KavenegarService
    ↓
KavenegarService::sendTemplateSMS() sends to Kavenegar API
    ↓
Buyer receives SMS
```

### 4. Configuration Requirements

#### Kavenegar Setup

1. Log in to your Kavenegar account
2. Navigate to Templates section
3. Create/verify the template with name: `SellerConfirmationNotice`
4. Template must have exactly **one token** named `%token`

#### Environment Variables

Ensure these are set in `.env`:

```env
KAVENEGAR_API_KEY=your_api_key_here
SMS_SANDBOX=true  # Set to false in production
```

### 5. Testing

#### Sandbox Mode (Development)

When `SMS_SANDBOX=true`, SMS won't be sent but will be logged:

```bash
# Check logs
tail -f storage/logs/laravel.log | grep "Template SMS"
```

You should see:

```
SMS Sandbox Mode - Template SMS would be sent
Template: SellerConfirmationNotice
Token: 09123456789 (or buyer's name)
```

#### Production Mode

1. Set `SMS_SANDBOX=false` in production `.env`
2. Ensure Kavenegar template is approved
3. Verify API key is correct
4. Test with a real bid acceptance

### 6. Monitoring & Troubleshooting

#### Check Logs

```bash
# View all SMS-related logs
grep -i "template sms" storage/logs/laravel.log

# View errors only
grep -i "Failed to send template SMS" storage/logs/laravel.log
```

#### Common Issues

**Issue**: SMS not sent

- Check Kavenegar API key
- Verify template name matches exactly
- Ensure buyer has a valid phone number
- Check queue is running: `php artisan queue:work`

**Issue**: Template not found

- Verify template exists in Kavenegar dashboard
- Check template name spelling (case-sensitive)
- Ensure template is approved

**Issue**: Invalid token

- Check buyer's phone number format
- Verify token is not empty

### 7. Queue Management

The SMS job is queued for better performance:

```bash
# Start queue worker
php artisan queue:work

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### 8. Backward Compatibility

The implementation maintains backward compatibility:

- Existing simple message notifications still work
- Other SMS notifications (OTP, etc.) unaffected
- Can switch between template and simple messages per notification

## Summary

✅ Template-based SMS system implemented
✅ Automatic notification on bid acceptance
✅ Queue-based processing for performance
✅ Comprehensive logging and error handling
✅ Sandbox mode for safe testing
✅ Backward compatible with existing SMS system

The buyer will now receive a professional, template-based SMS notification when their bid is accepted by a seller.
