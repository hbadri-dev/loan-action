# Bid Acceptance Notifications

## Overview

وقتی فروشنده یک پیشنهاد را قبول می‌کند، دو پیام SMS به صورت آنی ارسال می‌شود:

1. به ادمین‌ها
2. به خریدار

## Kavenegar Templates

### 1. AdminBidAccepted (برای ادمین)

**Template Name:** `AdminBidAccepted`

**Template Content:**

```
ادمین گرامی
فروشنده "%token" پیشنهاد "%token2" تومان را در مزایده "%token3" پذیرفت.

nationalkind.ir
وام یار
```

**Tokens:**

- `%token`: نام فروشنده (بدون فاصله)
- `%token2`: مبلغ پیشنهاد (با فرمت: 1,000,000)
- `%token3`: عنوان مزایده (بدون فاصله)

### 2. SellerConfirmationNoticeNew (برای خریدار)

**Template Name:** `SellerConfirmationNoticeNew`

**Template Content:**

```
کاربر %token گرامی
یک فروشنده قیمت پیشنهادی شما را تایید کرده است. لطفا در اسرع وقت نسبت به واریز کل مبلغ وام در پلتفرم اقدام فرمایید.

nationalkind.ir
وام یار
```

**Tokens:**

- `%token`: نام خریدار (بدون فاصله)

## Implementation Details

### Modified Files

#### 1. `app/Notifications/BidAccepted.php`

**Changes:**

- تغییر template از `SellerConfirmationNotice` به `SellerConfirmationNoticeNew`
- اضافه شدن `cleanToken()` برای حذف فاصله‌ها
- استفاده از `SmsChannel::class` به جای `'sms'`
- ارسال آنی (بدون queue)

**Key Code:**

```php
public function toSms(object $notifiable): array
{
    $buyerName = $this->cleanToken($notifiable->name ?? 'کاربر');

    return [
        'phone' => $notifiable->phone,
        'template' => 'SellerConfirmationNoticeNew',
        'token' => $buyerName,
    ];
}
```

#### 2. `app/Services/AdminNotifier.php`

**Changes:**

- فعال شدن `bid_accepted` در `notifySellerAction()`
- اضافه شدن `notifyAdminWithThreeTokens()` برای سه توکن
- اضافه شدن `sendLookupSMSWithThreeTokens()` برای ارسال با سه توکن

**Key Code:**

```php
// Send notification for bid acceptance
if ($action === 'bid_accepted') {
    $sellerName = $this->cleanToken($seller->name ?? 'فروشنده');
    $bidAmount = number_format($context['bid_amount'] ?? 0);
    $auctionTitle = $this->cleanToken($context['auction_title'] ?? 'نامشخص');

    // Use AdminBidAccepted template with three tokens
    $this->notifyAdminWithThreeTokens($action, $sellerName, $bidAmount, $auctionTitle, 'AdminBidAccepted');
}
```

#### 3. `app/Http/Controllers/Seller/SaleFlowController.php`

**Existing Code (Line 439-444):**

```php
// Notify admin about bid acceptance
$this->adminNotifier->notifySellerAction('bid_accepted', $user, [
    'auction_title' => $auction->title,
    'bid_amount' => $highestBid->amount,
    'buyer_name' => $highestBid->buyer->name
]);
```

این کد قبلاً وجود داشت و الان فعال شده.

## Flow

1. فروشنده پیشنهاد را قبول می‌کند (`SaleFlowController::acceptBid`)
2. **به خریدار:** `BidAccepted` notification ارسال می‌شود
   - از template `SellerConfirmationNoticeNew` استفاده می‌کند
   - آنی ارسال می‌شود (بدون queue)
3. **به ادمین:** `AdminNotifier::notifySellerAction` فراخوانی می‌شود
   - از template `AdminBidAccepted` استفاده می‌کند
   - به تمام ادمین‌ها ارسال می‌شود
   - آنی ارسال می‌شود

## Kavenegar Setup

### Template 1: AdminBidAccepted

1. وارد پنل کاوه‌نگار شوید
2. بخش Templates
3. ساخت template با نام: `AdminBidAccepted`
4. سه توکن: `%token`, `%token2`, `%token3`
5. ارسال برای تایید

### Template 2: SellerConfirmationNoticeNew

1. وارد پنل کاوه‌نگار شوید
2. بخش Templates
3. ساخت template با نام: `SellerConfirmationNoticeNew`
4. یک توکن: `%token`
5. ارسال برای تایید

## Testing

1. به عنوان فروشنده لاگین کنید
2. یک پیشنهاد را قبول کنید
3. چک کنید:
   - خریدار باید SMS دریافت کند
   - تمام ادمین‌ها باید SMS دریافت کنند

### Check Logs

```bash
tail -f storage/logs/laravel.log | grep "BidAccepted\|AdminBidAccepted"
```

## Active Notifications Summary

فقط این سه نوتیفیکیشن فعال هستند:

1. **AdminBidPlaced**: وقتی خریدار پیشنهاد ثبت می‌کند → به ادمین
2. **AdminLoanVerification**: وقتی فروشنده احراز هویت وام آپلود می‌کند → به ادمین
3. **AdminBidAccepted**: وقتی فروشنده پیشنهاد قبول می‌کند → به ادمین
4. **SellerConfirmationNoticeNew**: وقتی فروشنده پیشنهاد قبول می‌کند → به خریدار
5. **LoanVerificationResult**: وقتی ادمین احراز هویت تایید/رد می‌کند → به فروشنده

همه آنی ارسال می‌شوند (بدون queue)! 🎉

