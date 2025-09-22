# Changelog

تمام تغییرات مهم این پروژه در این فایل مستند خواهد شد.

فرمت این فایل بر اساس [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) است،
و این پروژه از [Semantic Versioning](https://semver.org/spec/v2.0.0.html) پیروی می‌کند.

## [Unreleased]

### Added
- سیستم مزایده وام کامل
- پشتیبانی از Docker
- احراز هویت با Laravel Breeze
- API endpoints با Laravel Sanctum
- مدیریت نقش‌ها با Spatie Permission
- پشتیبانی کامل از RTL و فونت Vazirmatn
- سرویس SMS با Kavenegar
- مدیریت تصاویر با Intervention Image
- Code style با Laravel Pint
- Debug bar برای development

### Changed

### Deprecated

### Removed

### Fixed

### Security

## [1.0.0] - 2024-09-19

### Added
- ایجاد پروژه Laravel 11
- پیکربندی Docker با nginx، MySQL 8 (Redis حذف شد)
- نصب و پیکربندی Laravel Breeze
- نصب و پیکربندی Laravel Sanctum
- نصب و پیکربندی Spatie Permission
- ایجاد Enums برای status ها
- ایجاد Models اصلی (Auction, Bid, PaymentReceipt, ContractAgreement, LoanTransfer, SellerSale)
- ایجاد Controllers برای Admin, Buyer, Seller
- ایجاد KavenegarService برای SMS
- ایجاد Middleware برای OTP verification
- ایجاد Seeders برای roles و demo data
- پیکربندی Vite با فونت Vazirmatn و RTL support
- ایجاد layout اصلی با پشتیبانی RTL
- ایجاد migrations برای تمام tables
- پیکربندی .env برای MySQL (Redis حذف شد)
- ایجاد فایل‌های Docker برای development و production
- ایجاد Makefile برای راحتی کار
- ایجاد deployment scripts
- ایجاد test scripts
- ایجاد documentation کامل

### Technical Details
- PHP 8.3
- Laravel 11
- MySQL 8.0
- Redis (حذف شد - از database queue استفاده می‌شود)
- Nginx
- Docker & Docker Compose
- Tailwind CSS
- Vite
- Alpine.js

### Database Schema
- users table با فیلدهای phone و phone_verified_at
- auctions table برای مدیریت مزایده‌ها
- bids table برای مدیریت پیشنهادات
- payment_receipts table برای مدیریت پرداخت‌ها
- contract_agreements table برای مدیریت قراردادها
- loan_transfers table برای مدیریت انتقال وام
- seller_sales table برای مدیریت فروش فروشندگان

### API Endpoints
- احراز هویت OTP
- مدیریت مزایده‌ها
- مدیریت پیشنهادات
- مدیریت پرداخت‌ها
- مدیریت قراردادها

### User Roles
- Admin: مدیریت کامل سیستم
- Seller: ایجاد و مدیریت مزایده‌ها
- Buyer: شرکت در مزایده‌ها و ارائه پیشنهاد

### Features
- سیستم مزایده وام کامل
- احراز هویت با OTP
- مدیریت نقش‌ها و دسترسی‌ها
- پشتیبانی کامل از زبان فارسی
- رابط کاربری RTL
- API کامل برای موبایل
- سیستم SMS
- مدیریت فایل‌ها
- گزارش‌گیری
