# راهنمای رفع مشکل لاگین و Session

## مشکلات حل شده:

1. Session expire سریع
2. بلاک شدن شماره تلفن
3. کد OTP منقضی شدن سریع

## تغییرات انجام شده:

### 1. تنظیمات Session

- Session lifetime: 120 دقیقه → 1440 دقیقه (24 ساعت)
- Session driver: file → database
- Migration جدید برای sessions table اضافه شد

### 2. تنظیمات OTP

- OTP expiry: 2 دقیقه → 5 دقیقه
- Rate limiting: 5 درخواست/ساعت → 10 درخواست/ساعت
- فاصله بین درخواست‌ها: 60 ثانیه → 30 ثانیه

## مراحل Deploy:

### 1. آپلود فایل‌های تغییر یافته:

```bash
# فایل‌های تغییر یافته:
- config/session.php
- app/Http/Controllers/Auth/OtpRequestController.php
- app/Http/Controllers/Auth/UnifiedOTPController.php
- database/migrations/2024_01_01_000000_create_sessions_table.php
```

### 2. تنظیم .env روی سرور:

```env
SESSION_DRIVER=database
SESSION_LIFETIME=1440
SMS_OTP_EXPIRY=5
SMS_RATE_LIMIT_ATTEMPTS=10
SMS_RATE_LIMIT_DECAY=30
```

### 3. اجرای Migration:

```bash
php artisan migrate
```

### 4. Clear Cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 5. Restart Services:

```bash
# اگر از supervisor استفاده می‌کنید:
sudo supervisorctl restart all

# یا restart web server
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
```

## تست:

1. لاگین با شماره تلفن
2. بررسی session که 24 ساعت باقی می‌ماند
3. تست ارسال OTP (باید 30 ثانیه بین درخواست‌ها کافی باشد)
4. تست اعتبار OTP (باید 5 دقیقه معتبر باشد)

## نکات مهم:

- حتماً sessions table در database وجود داشته باشد
- اگر از Redis استفاده می‌کنید، می‌توانید SESSION_DRIVER=redis تنظیم کنید
- Rate limiting در cache ذخیره می‌شود، پس cache را clear کنید

