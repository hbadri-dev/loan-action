# راهنمای تنظیمات زرین‌پال

## تنظیمات Sandbox و Production

برای کنترل محیط sandbox و production زرین‌پال، از فایل `config/services.php` استفاده کنید:

### 1. تنظیمات در `config/services.php`:

```php
'zarinpal' => [
    'merchant_id' => env('ZARINPAL_MERCHANT_ID', '3163ddfe-bd9a-46d2-830e-d2587c67ee46'),
    'sandbox' => env('ZARINPAL_SANDBOX', true), // true برای sandbox، false برای production
    'callback_url' => env('ZARINPAL_CALLBACK_URL', 'localhost:8080/payment/callback'),
    'test_merchant_id' => '00000000-0000-0000-0000-000000000000', // Merchant ID تست برای sandbox
],
```

### 2. تنظیمات در `.env`:

```env
# Zarinpal Payment Gateway
ZARINPAL_MERCHANT_ID=3163ddfe-bd9a-46d2-830e-d2587c67ee46
ZARINPAL_SANDBOX=true
ZARINPAL_CALLBACK_URL=localhost:8080/payment/callback
```

## نحوه تغییر محیط:

### برای Sandbox (محیط تست):

```env
ZARINPAL_SANDBOX=true
```

### برای Production (محیط واقعی):

```env
ZARINPAL_SANDBOX=false
```

## تنظیمات خودکار:

- **Sandbox**: استفاده از merchant ID تست و URL های sandbox
- **Production**: استفاده از merchant ID واقعی و URL های production
- **Callback URL**: به صورت خودکار http/https تنظیم می‌شود

## پاک کردن Cache:

بعد از تغییر تنظیمات، cache را پاک کنید:

```bash
php artisan config:clear
php artisan cache:clear
```

## تست:

برای تست sandbox، مبلغ ۲۰۰ هزار تومن (۲ میلیون ریال) ارسال می‌شود.
