# Loan Verification Result Notification

## Overview

وقتی ادمین احراز هویت وام فروشنده را تایید یا رد می‌کند، یک پیام SMS به فروشنده ارسال می‌شود.

## Kavenegar Template Configuration

### Template Name

`LoanVerificationResult`

### Template Content

```
کاربر %token گرامی
احراز هویت وام شما %token2 شد.

nationalkind.ir
وام یار
```

### Tokens

- `%token`: نام فروشنده
- `%token2`: "تایید" یا "رد"

## Implementation Details

### New Files Created

#### 1. `app/Notifications/LoanVerificationResult.php`

یک Notification جدید که:

- از `SmsChannel` و `database` استفاده می‌کند
- از template `LoanVerificationResult` در کاوه‌نگار استفاده می‌کند
- بسته به وضعیت `PaymentReceipt`، متن "تایید" یا "رد" را ارسال می‌کند

**Key Code:**

```php
public function toSms(object $notifiable): array
{
    $isApproved = $this->receipt->status === PaymentStatus::APPROVED;
    $statusText = $isApproved ? 'تایید' : 'رد';
    $sellerName = $notifiable->name ?? 'کاربر';

    return [
        'phone' => $notifiable->phone,
        'template' => 'LoanVerificationResult',
        'token' => $sellerName,
        'token2' => $statusText,
    ];
}
```

### Modified Files

#### 2. `app/Notifications/Channels/SmsChannel.php`

**Changes:**

- اضافه شدن پشتیبانی از `token2` برای template های دو توکنی
- استفاده از `sendTemplateSMSWithTokens` برای ارسال SMS با چند token

**Key Code:**

```php
// Check if token2 exists for two-token templates
if (isset($data['token2'])) {
    $this->kavenegarService->sendTemplateSMSWithTokens($data['phone'], [$data['token'], $data['token2']], $data['template']);
}
```

#### 3. `app/Http/Controllers/Admin/ReceiptReviewController.php`

**Changes in `approve()` method (Line 107-108):**

```php
// Send loan verification result notification to seller
$receipt->user->notify(new \App\Notifications\LoanVerificationResult($receipt));
```

**Changes in `reject()` method (Line 182-188):**

```php
// Send specific notification for loan verification rejection
if ($receipt->type === PaymentType::LOAN_VERIFICATION) {
    $receipt->user->notify(new \App\Notifications\LoanVerificationResult($receipt));
} else {
    // Notify user of rejection for other payment types
    $receipt->user->notify(new \App\Notifications\PaymentReceiptRejected($receipt));
}
```

## Flow

### Approval Flow

1. ادمین احراز هویت وام را تایید می‌کند
2. وضعیت `PaymentReceipt` به `APPROVED` تغییر می‌کند
3. `SellerSale` به مرحله 3 منتقل می‌شود
4. `LoanVerificationResult` notification برای فروشنده ارسال می‌شود
5. SMS با متن "تایید" به فروشنده می‌رسد

### Rejection Flow

1. ادمین احراز هویت وام را رد می‌کند
2. وضعیت `PaymentReceipt` به `REJECTED` تغییر می‌کند
3. `LoanVerificationResult` notification برای فروشنده ارسال می‌شود
4. SMS با متن "رد" به فروشنده می‌رسد

## Kavenegar Setup

1. وارد پنل کاوه‌نگار شوید
2. بخش Templates
3. ساخت template جدید با نام: `LoanVerificationResult`
4. متن فارسی با `%token` و `%token2` را وارد کنید
5. ارسال برای تایید

## Testing

### Test Approval

1. به عنوان فروشنده احراز هویت وام را آپلود کنید
2. به عنوان ادمین لاگین کنید
3. احراز هویت را تایید کنید
4. فروشنده باید SMS با متن "تایید" دریافت کند

### Test Rejection

1. به عنوان فروشنده احراز هویت وام را آپلود کنید
2. به عنوان ادمین لاگین کنید
3. احراز هویت را رد کنید (با دلیل)
4. فروشنده باید SMS با متن "رد" دریافت کند

## Logs

بررسی لاگ:

```bash
tail -f storage/logs/laravel.log | grep "LoanVerificationResult"
```

## Notes

- این notification فقط برای `LOAN_VERIFICATION` type استفاده می‌شود
- برای سایر انواع payment receipts، از `PaymentReceiptApproved` و `PaymentReceiptRejected` استفاده می‌شود
- SMS به صورت queued ارسال می‌شود (ShouldQueue)
- علاوه بر SMS، یک notification در database هم ذخیره می‌شود
