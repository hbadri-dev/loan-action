# Admin Loan Verification Notification

## Overview

وقتی فروشنده اسکرین‌شات احراز هویت وام را آپلود می‌کند، بلافاصله یک پیام SMS به تمام ادمین‌ها ارسال می‌شود.

## Kavenegar Template Configuration

### Template Name

`AdminLoanVerification`

### Template Content

```
ادمین گرامی
احراز هویت وام جدید برای مزایده "%token" توسط فروشنده "%token2" انجام شد.

nationalkind.ir
وام یار
```

### Tokens

- `%token`: عنوان مزایده
- `%token2`: نام فروشنده

## Implementation Details

### Modified Files

#### 1. `app/Services/AdminNotifier.php`

**Changes:**

- متد `notifySellerAction()` فعال شد
- فقط برای `loan_verification_uploaded` پیام ارسال می‌شود
- از template `AdminLoanVerification` استفاده می‌کند

**Key Code:**

```php
public function notifySellerAction(string $action, User $seller, array $context = []): void
{
    // Only send notification for loan verification upload
    if ($action === 'loan_verification_uploaded') {
        $auctionTitle = $this->cleanToken($context['auction_title'] ?? 'نامشخص');
        $sellerName = $this->cleanToken($seller->name ?? 'فروشنده');

        // Use AdminLoanVerification template with specific tokens
        $this->notifyAdmin($action, $auctionTitle, $sellerName, 'AdminLoanVerification');
    }

    // All other seller actions are disabled
}
```

#### 2. `app/Http/Controllers/Seller/SaleFlowController.php`

**Added Code (Line 165-168):**

```php
// Notify admin about loan verification upload
$this->adminNotifier->notifySellerAction('loan_verification_uploaded', $user, [
    'auction_title' => $auction->title
]);
```

## Flow

1. فروشنده اسکرین‌شات وام را آپلود می‌کند (`uploadLoanVerification`)
2. فایل ذخیره می‌شود و رکورد `PaymentReceipt` با نوع `LOAN_VERIFICATION` ساخته می‌شود
3. متد `notifySellerAction` فراخوانی می‌شود
4. پیام SMS با template `AdminLoanVerification` به تمام ادمین‌ها ارسال می‌شود

## Kavenegar Setup

1. وارد پنل کاوه‌نگار شوید
2. بخش Templates
3. ساخت template جدید با نام: `AdminLoanVerification`
4. متن فارسی با `%token` و `%token2` را وارد کنید
5. ارسال برای تایید

## Testing

بعد از تایید template در کاوه‌نگار:

1. به عنوان فروشنده لاگین کنید
2. در یک مزایده، اسکرین‌شات وام را آپلود کنید
3. بررسی لاگ: `tail -f storage/logs/laravel.log | grep AdminNotifier`
4. تایید دریافت SMS توسط ادمین‌ها

## Active Notifications

فقط این دو نوتیفیکیشن فعال هستند:

1. **AdminBidPlaced**: وقتی خریدار پیشنهاد ثبت می‌کند
2. **AdminLoanVerification**: وقتی فروشنده احراز هویت وام را آپلود می‌کند

بقیه نوتیفیکیشن‌ها غیرفعال هستند.

