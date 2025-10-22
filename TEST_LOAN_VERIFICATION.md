# تست نوتیفیکیشن احراز هویت وام

## مشکل قبلی

توکن‌ها نباید فاصله یا خط جدید داشته باشند.

## راه‌حل

اضافه شدن متد `cleanToken()` در `LoanVerificationResult` که:

- تمام فاصله‌ها رو حذف می‌کنه
- خطوط جدید و tab ها رو حذف می‌کنه
- اگه توکن خالی شد، از "کاربر" استفاده می‌کنه

## چک کردن لاگ‌ها

### 1. چک کردن ارسال Notification

```bash
tail -f storage/logs/laravel.log | grep "LoanVerificationResult"
```

### 2. چک کردن ارسال SMS

```bash
tail -f storage/logs/laravel.log | grep "Template SMS"
```

### 3. چک کردن خطاها

```bash
tail -f storage/logs/laravel.log | grep "Failed to send SMS"
```

### 4. چک کردن Kavenegar API

```bash
tail -f storage/logs/laravel.log | grep "Kavenegar"
```

## تست دستی

### مرحله 1: رد کردن احراز هویت

1. به عنوان ادمین لاگین کنید
2. به بخش Payment Receipts بروید
3. یک احراز هویت وام را رد کنید
4. چک کنید لاگ چی می‌گه

### مرحله 2: بررسی داده‌های ارسالی

در لاگ باید ببینید:

```
Template SMS with tokens would be sent
template: LoanVerificationResult
tokens: ["نامفروشنده", "رد"]
```

### مرحله 3: چک کردن شماره تلفن

مطمئن شوید فروشنده شماره تلفن داره:

```bash
php artisan tinker
>>> $receipt = \App\Models\PaymentReceipt::find(RECEIPT_ID);
>>> $receipt->user->phone;
```

## مشکلات احتمالی

### 1. شماره تلفن خالی است

```php
if (!$notifiable->phone) {
    // SMS ارسال نمی‌شه
}
```

### 2. Sandbox Mode فعال است

در `KavenegarService.php` چک کنید:

```php
if ($this->sandbox) {
    // فقط لاگ می‌شه، SMS ارسال نمی‌شه
}
```

### 3. Template در Kavenegar تایید نشده

- وارد پنل Kavenegar شوید
- بخش Templates
- مطمئن شوید `LoanVerificationResult` تایید شده

### 4. توکن‌ها درست ارسال نمی‌شن

چک کنید که:

- `token` = نام فروشنده (بدون فاصله)
- `token2` = "تایید" یا "رد"

## دستور تست سریع

```bash
# چک کردن آخرین 50 خط لاگ
tail -n 50 storage/logs/laravel.log

# فیلتر کردن فقط SMS ها
tail -n 100 storage/logs/laravel.log | grep -i sms

# چک کردن خطاهای امروز
grep "$(date +%Y-%m-%d)" storage/logs/laravel.log | grep -i error
```

