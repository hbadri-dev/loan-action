# راهنمای مشارکت در پروژه

از مشارکت شما در پروژه سیستم مزایده وام استقبال می‌کنیم! این راهنما به شما کمک می‌کند تا به راحتی در پروژه مشارکت کنید.

## نحوه مشارکت

### 1. Fork کردن پروژه

ابتدا پروژه را در GitHub fork کنید.

### 2. کلون کردن پروژه

```bash
git clone https://github.com/YOUR_USERNAME/loan-auction.git
cd loan-auction
```

### 3. راه‌اندازی محیط توسعه

```bash
# استفاده از Makefile
make install

# یا استفاده از Docker Compose
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d
```

### 4. ایجاد Branch جدید

```bash
git checkout -b feature/your-feature-name
```

### 5. انجام تغییرات

- کد خود را بنویسید
- تست‌های مربوطه را اضافه کنید
- مطمئن شوید که کد style درست است

### 6. اجرای تست‌ها

```bash
# استفاده از Makefile
make test

# یا استفاده از script
./test.sh
```

### 7. Commit کردن تغییرات

```bash
git add .
git commit -m "Add: توضیح کوتاه تغییرات"
```

### 8. Push کردن تغییرات

```bash
git push origin feature/your-feature-name
```

### 9. ایجاد Pull Request

در GitHub یک Pull Request ایجاد کنید.

## استانداردهای کدنویسی

### PHP

- از Laravel Pint برای code style استفاده کنید
- از PSR-12 standard پیروی کنید
- نام‌های متغیر و تابع‌ها را به انگلیسی بنویسید
- کامنت‌ها را به فارسی بنویسید

### JavaScript/CSS

- از ESLint و Prettier استفاده کنید
- از Tailwind CSS برای styling استفاده کنید

### Database

- نام‌های جدول و ستون‌ها را به انگلیسی بنویسید
- از migration ها برای تغییرات دیتابیس استفاده کنید

## ساختار پروژه

```
app/
├── Enums/                 # Enum classes
├── Http/
│   ├── Controllers/       # Controllers
│   ├── Middleware/        # Custom middleware
│   └── Requests/          # Form requests
├── Models/                # Eloquent models
├── Policies/              # Authorization policies
└── Services/              # Business logic services

database/
├── migrations/            # Database migrations
├── seeders/              # Database seeders
└── factories/            # Model factories

resources/
├── css/                  # Stylesheets
├── js/                   # JavaScript files
└── views/                # Blade templates

tests/
├── Feature/              # Feature tests
└── Unit/                 # Unit tests
```

## تست‌ها

### نوشتن تست

- برای هر feature جدید تست بنویسید
- از PHPUnit استفاده کنید
- تست‌ها را در پوشه مناسب قرار دهید

### اجرای تست‌ها

```bash
# تمام تست‌ها
make test

# تست‌های خاص
docker-compose exec app php artisan test --filter=TestName
```

## Code Style

### Laravel Pint

```bash
# بررسی code style
make pint

# اصلاح خودکار
docker-compose exec app ./vendor/bin/pint
```

## Pull Request

### قبل از ارسال PR

- [ ] تست‌ها پاس می‌شوند
- [ ] Code style درست است
- [ ] Documentation به‌روزرسانی شده
- [ ] Migration ها اضافه شده‌اند (در صورت نیاز)

### عنوان PR

از فرمت زیر استفاده کنید:

```
[Type]: توضیح کوتاه تغییرات
```

انواع Type:
- `Add`: اضافه کردن feature جدید
- `Fix`: رفع باگ
- `Update`: به‌روزرسانی feature موجود
- `Remove`: حذف feature
- `Refactor`: بازنویسی کد
- `Docs`: تغییرات documentation

### توضیحات PR

- توضیح دهید چه تغییراتی انجام داده‌اید
- اگر باگ رفع کرده‌اید، توضیح دهید مشکل چه بوده
- اگر feature جدید اضافه کرده‌اید، نحوه استفاده را توضیح دهید

## گزارش باگ

### قبل از گزارش باگ

- مطمئن شوید که باگ در آخرین نسخه وجود دارد
- بررسی کنید که باگ قبلاً گزارش نشده باشد

### نحوه گزارش

از GitHub Issues استفاده کنید و اطلاعات زیر را ارائه دهید:

- توضیح مشکل
- مراحل تکرار مشکل
- نتیجه مورد انتظار
- نتیجه واقعی
- اطلاعات محیط (OS، PHP version، Laravel version)
- Screenshot (در صورت نیاز)

## پیشنهاد Feature

### قبل از پیشنهاد

- بررسی کنید که feature قبلاً پیشنهاد نشده باشد
- مطمئن شوید که feature با اهداف پروژه سازگار است

### نحوه پیشنهاد

از GitHub Issues استفاده کنید و اطلاعات زیر را ارائه دهید:

- توضیح feature
- دلیل نیاز به این feature
- پیشنهادات برای پیاده‌سازی
- مثال‌های استفاده

## سوالات

اگر سوالی دارید:

- از GitHub Discussions استفاده کنید
- یا issue جدید ایجاد کنید

## لایسنس

با مشارکت در این پروژه، موافقت می‌کنید که کد شما تحت لایسنس MIT منتشر شود.

## تشکر

از مشارکت شما در بهبود این پروژه تشکر می‌کنیم! 🙏
