# سیستم مزایده وام

سیستم جامع مزایده وام با پنل‌های جداگانه برای خریداران، فروشندگان و ادمین‌ها.

## پیش‌نیازها

- Docker
- Docker Compose
- Git

## راه‌اندازی

### 1. کلون کردن پروژه

```bash
git clone <repository-url>
cd loan-auction
```

### 2. راه‌اندازی با Docker

```bash
# راه‌اندازی کانتینرها
make up

# کپی کردن فایل محیط
docker exec -it app cp .env.example .env

# تولید کلید اپلیکیشن
docker exec -it app php artisan key:generate

# نصب وابستگی‌های PHP
docker exec -it app composer install

# اجرای مایگریشن‌ها و سیدرها
docker exec -it app php artisan migrate --seed

# نصب وابستگی‌های Node.js
docker exec -it app npm install

# کامپایل فایل‌های CSS و JS
docker exec -it app npm run dev
```

### 3. دسترسی به سیستم

- **وب‌سایت:** http://localhost:8080
- **پنل ادمین:** http://localhost:8080/admin

## ورود به سیستم

### ورود ادمین
- **ایمیل:** admin@example.com
- **رمز عبور:** admin123

### ثبت‌نام کاربران جدید

#### خریدار
1. مراجعه به `/register/buyer`
2. وارد کردن نام و شماره موبایل
3. دریافت کد OTP
4. ورود با کد تأیید

#### فروشنده
1. مراجعه به `/register/seller`
2. وارد کردن نام و شماره موبایل
3. دریافت کد OTP
4. ورود با کد تأیید

## تنظیمات محیط

### متغیرهای محیطی اصلی

```env
# تنظیمات پایه
APP_NAME="سیستم مزایده وام"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

# تنظیمات پایگاه داده
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=loan_auction
DB_USERNAME=root
DB_PASSWORD=root

# تنظیمات Redis (غیرفعال)
# REDIS_HOST=redis
# REDIS_PASSWORD=null
# REDIS_PORT=6379
```

### تنظیمات Kavenegar (SMS)

```env
# کلید API کاوه‌نگار
KAVENEGAR_API_KEY=your_api_key_here

# شماره فرستنده (اختیاری)
KAVENEGAR_SENDER=10008663

# URL پایه API (اختیاری)
KAVENEGAR_BASE_URL=https://api.kavenegar.com/v1

# تنظیمات SMS
SMS_SANDBOX=true  # برای تست: true، برای تولید: false
SMS_DRIVER=kavenegar
SMS_RATE_LIMIT_ATTEMPTS=5
SMS_RATE_LIMIT_DECAY=60
SMS_OTP_LENGTH=6
SMS_OTP_EXPIRY=2
SMS_MAX_RETRIES=3
SMS_TIMEOUT=30
SMS_CONNECT_TIMEOUT=10
```

**نکته:** برای ارسال SMS واقعی، مقدار `SMS_SANDBOX=false` را در فایل `.env` قرار دهید.

### تنظیمات ذخیره‌سازی

```env
# دیسک ذخیره‌سازی
FILESYSTEM_DISK=public

# تنظیمات فایل
UPLOAD_MAX_FILESIZE=5M
POST_MAX_SIZE=10M

# لینک ذخیره‌سازی عمومی
STORAGE_LINK_CREATED=true
```

## ساختار سیستم

### پنل‌های کاربری

#### 1. پنل ادمین (`/admin`)
- مدیریت مزایده‌ها
- بررسی و تأیید رسیدهای پرداخت
- مدیریت قراردادها و پیشنهادها
- نظارت بر فرایندهای فروش
- مدیریت انتقال وام

#### 2. پنل خریدار (`/buyer`)
- مشاهده مزایده‌های فعال
- شرکت در مزایده (7 مرحله)
- ثبت پیشنهاد قیمت
- آپلود رسیدهای پرداخت
- پیگیری وضعیت سفارشات

#### 3. پنل فروشنده (`/seller`)
- مشاهده مزایده‌های فعال
- شروع فرایند فروش (8 مرحله)
- پذیرش پیشنهادات
- آپلود رسید انتقال وام
- پیگیری وضعیت فروش

### فرایند مزایده

#### فرایند خریدار (7 مرحله)
1. **جزئیات وام** - مشاهده اطلاعات مزایده
2. **متن قرارداد** - تأیید قرارداد با OTP
3. **پرداخت کارمزد** - واریز ۳ میلیون تومان
4. **ثبت پیشنهاد قیمت** - ثبت مبلغ پیشنهادی
5. **انتظار تأیید فروشنده** - پیگیری وضعیت پیشنهاد
6. **پرداخت مبلغ خرید** - واریز مبلغ نهایی
7. **انتظار انتقال وام** - تأیید انتقال وام

#### فرایند فروشنده (8 مرحله)
1. **جزئیات وام** - مشاهده اطلاعات مزایده
2. **قرارداد فروش** - تأیید قرارداد با OTP
3. **پرداخت کارمزد** - واریز ۳ میلیون تومان
4. **پذیرش آخرین پیشنهاد** - انتخاب خریدار
5. **انتظار واریز وجه** - پیگیری پرداخت خریدار
6. **انتقال وام** - انجام انتقال و آپلود رسید
7. **انتظار تأیید انتقال** - تأیید نهایی
8. **تکمیل فروش** - پایان فرایند

## ویژگی‌های فنی

### امنیت
- احراز هویت OTP برای کاربران
- احراز هویت ایمیل/رمز عبور برای ادمین
- کنترل دسترسی مبتنی بر نقش
- اعتبارسنجی فایل‌های آپلود شده
- حفاظت از CSRF

### ذخیره‌سازی فایل
- آپلود ایمن با اعتبارسنجی
- سازماندهی فایل‌ها بر اساس کاربر
- نام‌گذاری UUID برای امنیت
- محدودیت حجم (5MB) و نوع فایل
- کنترل دسترسی با Policy

### پیامک (SMS)
- یکپارچه‌سازی با Kavenegar
- ارسال OTP ورود و تأیید قرارداد
- محدودیت نرخ درخواست
- حالت Sandbox برای تست
- مدیریت خطاها و بازگشت

### رابط کاربری
- طراحی RTL برای فارسی
- فونت Vazirmatn
- کامپوننت‌های قابل استفاده مجدد
- فرمت‌دهی اعداد فارسی
- اعتبارسنجی real-time

## دستورات مفید

### Docker
```bash
# راه‌اندازی
make up

# توقف
make down

# مشاهده لاگ‌ها
make logs

# دسترسی به کانتینر
make shell
```

### Laravel
```bash
# مایگریشن
docker exec -it app php artisan migrate

# سیدر
docker exec -it app php artisan db:seed

# پاک کردن کش
docker exec -it app php artisan cache:clear
docker exec -it app php artisan config:clear
docker exec -it app php artisan route:clear
docker exec -it app php artisan view:clear

# لینک ذخیره‌سازی
docker exec -it app php artisan storage:link

# تست
docker exec -it app php artisan test
```

### NPM
```bash
# نصب وابستگی‌ها
docker exec -it app npm install

# کامپایل برای توسعه
docker exec -it app npm run dev

# کامپایل برای تولید
docker exec -it app npm run build

# تماشای تغییرات
docker exec -it app npm run watch
```

## تست سیستم

### تست SMS
```bash
# تست ارسال SMS
docker exec -it app php artisan sms:test

# مشاهده لاگ‌های SMS
docker exec -it app tail -f storage/logs/laravel.log | grep SMS
```

### تست فایل‌ها
```bash
# تست آپلود فایل
docker exec -it app php artisan test --filter=FileUploadTest

# مشاهده فایل‌های آپلود شده
ls -la storage/app/public/receipts/
```

## عیب‌یابی

### مشکلات رایج

#### 1. خطای اتصال به پایگاه داده
```bash
# بررسی وضعیت کانتینر MySQL
docker ps | grep mysql

# بررسی لاگ‌های MySQL
docker logs mysql
```

#### 2. خطای SMS
```bash
# بررسی تنظیمات Kavenegar
docker exec -it app php artisan tinker
>>> config('sms.sandbox')
>>> env('KAVENEGAR_API_KEY')
```

#### 3. مشکل فایل‌های آپلود
```bash
# بررسی مجوزهای فایل
docker exec -it app ls -la storage/app/public/

# ایجاد لینک ذخیره‌سازی
docker exec -it app php artisan storage:link
```

#### 4. مشکل کش
```bash
# پاک کردن تمام کش‌ها
docker exec -it app php artisan optimize:clear
```

## مشارکت در توسعه

### ساختار پروژه
```
app/
├── Http/Controllers/     # کنترلرها
├── Models/              # مدل‌ها
├── Services/            # سرویس‌های تجاری
├── Policies/            # پالیسی‌های دسترسی
└── Enums/              # شمارش‌ها

resources/
├── views/              # قالب‌های Blade
├── lang/fa/           # ترجمه‌های فارسی
└── css/               # فایل‌های CSS

database/
├── migrations/         # مایگریشن‌ها
└── seeders/           # سیدرها
```

### استانداردهای کدنویسی
- استفاده از PSR-12
- کامنت‌گذاری کدها
- تست‌نویسی برای عملکردها
- استفاده از Type Hints
- مدیریت خطاها

### تست‌ها
```bash
# اجرای تمام تست‌ها
docker exec -it app php artisan test

# تست‌های خاص
docker exec -it app php artisan test --filter=SmsServiceTest
docker exec -it app php artisan test --filter=FileUploadTest
```

## مجوز

این پروژه تحت مجوز MIT منتشر شده است.

## پشتیبانی

برای گزارش مشکلات یا درخواست ویژگی‌های جدید، لطفاً از بخش Issues استفاده کنید.

---

**نکته:** این سیستم برای استفاده در محیط تولید طراحی شده و شامل تمام ویژگی‌های امنیتی و بهینه‌سازی‌های لازم است.
