<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>وام یار - پلتفرم آنلاین انتقال وام</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
            text-decoration: none;
        }

        .header-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .header-btn {
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            white-space: nowrap;
        }

        .header-btn.login {
            background: transparent;
            color: #667eea;
            border-color: #667eea;
        }

        .header-btn.login:hover {
            background: #667eea;
            color: white;
        }

        .header-btn.dashboard {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .header-btn.dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .header-btn.telegram {
            background: linear-gradient(135deg, #0088cc, #00a8ff);
            color: white;
        }

        .header-btn.telegram:hover {
            background: linear-gradient(135deg, #0077b3, #0099e6);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 136, 204, 0.3);
        }



        /* Mobile Menu Styles */
        .mobile-menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            background: rgba(102, 126, 234, 0.1);
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            background: rgba(102, 126, 234, 0.2);
        }

        .hamburger-line {
            width: 25px;
            height: 3px;
            background: #667eea;
            margin: 3px 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .mobile-menu-toggle.active .hamburger-line:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }

        .mobile-menu-toggle.active .hamburger-line:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle.active .hamburger-line:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-radius: 0 0 15px 15px;
            padding: 20px;
            z-index: 999;
        }

        .mobile-menu.active {
            display: block;
        }

        .mobile-menu-item {
            display: block;
            padding: 15px 20px;
            margin: 8px 0;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .mobile-menu-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .mobile-menu-item.telegram {
            background: linear-gradient(135deg, #0088cc, #00a8ff);
        }

        .mobile-menu-item.telegram:hover {
            box-shadow: 0 8px 25px rgba(0, 136, 204, 0.3);
        }


        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 120px 0 80px 0;
            text-align: center;
            margin-top: 70px;
        }

        .hero .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-button {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(238, 90, 36, 0.3);
            text-align: center;
            min-width: 200px;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(238, 90, 36, 0.4);
            color: white;
            text-decoration: none;
        }

        .cta-button.bg-blue-600 {
            background: linear-gradient(45deg, #3b82f6, #1d4ed8);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .cta-button.bg-blue-600:hover {
            box-shadow: 0 12px 35px rgba(59, 130, 246, 0.4);
        }

        .cta-button.bg-green-600 {
            background: linear-gradient(45deg, #16a34a, #15803d);
            box-shadow: 0 8px 25px rgba(22, 163, 74, 0.3);
        }

        .cta-button.bg-green-600:hover {
            box-shadow: 0 12px 35px rgba(22, 163, 74, 0.4);
        }

        .stats-section {
            background: white;
            padding: 80px 0;
        }

        .stats-section .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }

        .stat-card {
            text-align: center;
            padding: 40px 20px;
            border-radius: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-description {
            font-size: 0.9rem;
            color: #888;
        }

        .features-section {
            background: #f8f9fa;
            padding: 80px 0;
        }

        .features-section .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .feature-description {
            color: #666;
            line-height: 1.6;
        }

        .how-it-works {
            background: white;
            padding: 80px 0;
        }

        .how-it-works .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }

        .step-card {
            text-align: center;
            padding: 40px 20px;
        }

        .step-number {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            margin: 0 auto 20px;
        }

        .step-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .step-description {
            color: #666;
            line-height: 1.6;
        }

        .quick-access {
            background: #f8f9fa;
            padding: 80px 0;
        }

        .quick-access .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .access-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            max-width: 800px;
            margin: 0 auto;
        }

        .access-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            border: 3px solid transparent;
        }

        .access-card:hover {
            transform: translateY(-10px);
        }

        .access-card.buyer {
            border-color: #16a34a;
        }

        .access-card.seller {
            border-color: #3b82f6;
        }

        .access-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .access-card h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .access-card p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .access-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .access-buttons .access-btn {
            flex: 1;
            max-width: 200px;
        }

        .access-btn {
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            min-width: 120px;
            text-align: center;
        }

        .access-btn.primary {
            background: linear-gradient(45deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .access-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
            color: white;
        }

        .access-btn.secondary {
            background: transparent;
            color: #3b82f6;
            border: 2px solid #3b82f6;
        }

        .access-btn.secondary:hover {
            background: #3b82f6;
            color: white;
        }

        .access-card.seller .access-btn.primary {
            background: linear-gradient(45deg, #16a34a, #15803d);
        }

        .access-card.seller .access-btn.primary:hover {
            box-shadow: 0 8px 25px rgba(22, 163, 74, 0.3);
        }

        .access-card.seller .access-btn.secondary {
            color: #16a34a;
            border-color: #16a34a;
        }

        .access-card.seller .access-btn.secondary:hover {
            background: #3b82f6;
            color: white;
        }

        /* Buyer buttons - Green theme */
        .access-btn.buyer-login {
            background: linear-gradient(45deg, #16a34a, #15803d);
        }

        .access-btn.buyer-login:hover {
            box-shadow: 0 8px 25px rgba(22, 163, 74, 0.3);
        }

        .access-btn.buyer-register {
            color: #16a34a;
            border-color: #16a34a;
        }

        .access-btn.buyer-register:hover {
            background: #16a34a;
            color: white;
        }

        /* Seller buttons - Blue theme */
        .access-btn.seller-login {
            background: linear-gradient(45deg, #3b82f6, #1d4ed8);
        }

        .access-btn.seller-login:hover {
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .access-btn.seller-register {
            color: #3b82f6;
            border-color: #3b82f6;
        }

        .access-btn.seller-register:hover {
            background: #3b82f6;
            color: white;
        }

        .final-cta {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .final-cta .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .final-cta h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .final-cta p {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        .footer {
            background: #2c3e50;
            color: white;
            padding: 60px 0 30px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section {
            text-align: center;
        }

        .footer-logo {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #ecf0f1;
        }

        .footer-description {
            color: #bdc3c7;
            font-size: 1.1rem;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .footer-address {
            color: #bdc3c7;
            font-size: 1rem;
            line-height: 1.8;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            border-right: 4px solid #3498db;
        }

        .footer-trust {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .footer-trust img {
            max-width: 120px;
            height: auto;
        }

        .footer-copyright {
            color: #95a5a6;
            font-size: 0.9rem;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #34495e;
        }

        @media (max-width: 768px) {
            .header {
                padding: 12px 0;
                position: relative;
            }

            .header-content {
                padding: 0 15px;
            }

            .logo {
                font-size: 1.5rem;
            }

            .header-buttons {
                display: none;
            }

            .mobile-menu-toggle {
                display: flex;
            }

            .header-btn {
                padding: 8px 16px;
                font-size: 0.9rem;
                border-radius: 20px;
            }

            /* Hero section mobile styles */
            .hero .flex {
                flex-direction: column;
                gap: 15px;
            }

            .hero .cta-button {
                width: 100%;
                max-width: 300px;
                margin: 0 auto;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .footer {
                padding: 40px 0 20px;
            }

            .footer-logo {
                font-size: 1.5rem;
            }

            .footer-description {
                font-size: 1rem;
            }

            .footer-address {
                font-size: 0.9rem;
                padding: 12px;
            }
        }

        @media (max-width: 600px) {
            .header-content {
                padding: 0 12px;
            }

            .logo {
                font-size: 1.3rem;
            }

            .header-buttons {
                gap: 6px;
            }

            .header-btn {
                padding: 7px 14px;
                font-size: 0.85rem;
                border-radius: 18px;
            }

            /* Hero section small mobile styles */
            .hero .flex {
                flex-direction: column;
                gap: 12px;
            }

            .hero .cta-button {
                width: 100%;
                max-width: 280px;
                padding: 12px 20px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .header {
                padding: 10px 0;
            }

            .header-content {
                padding: 0 10px;
            }

            .logo {
                font-size: 1.2rem;
            }

            .header-buttons {
                gap: 4px;
            }

            .header-btn {
                padding: 6px 12px;
                font-size: 0.8rem;
                border-radius: 15px;
            }

            /* Hero section mobile styles */
            .hero .flex {
                flex-direction: column;
                gap: 10px;
            }

            .hero .cta-button {
                width: 100%;
                max-width: 260px;
                padding: 10px 18px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 360px) {
            .header-content {
                padding: 0 8px;
            }

            .logo {
                font-size: 1.1rem;
            }

            .header-buttons {
                gap: 3px;
            }

            .header-btn {
                padding: 5px 10px;
                font-size: 0.75rem;
                border-radius: 12px;
            }

            /* Hero section extra small mobile styles */
            .hero .flex {
                flex-direction: column;
                gap: 8px;
            }

            .hero .cta-button {
                width: 100%;
                max-width: 240px;
                padding: 8px 16px;
                font-size: 0.8rem;
            }
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 60px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        @media (max-width: 768px) {
            .header-content {
                padding: 0 15px;
            }

            .logo {
                font-size: 1.5rem;
            }

            .header-btn {
                padding: 8px 20px;
                font-size: 0.9rem;
            }

            .hero {
                padding: 100px 0 60px 0;
                margin-top: 60px;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
                padding: 0 20px;
            }

            .stats-grid,
            .features-grid,
            .steps-grid,
            .access-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .access-grid {
                max-width: 100%;
            }

            .access-card {
                padding: 30px 20px;
            }

            .access-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .access-buttons .access-btn {
                max-width: 100%;
            }

            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="/" class="logo">وام یار</a>
            <div class="header-buttons">
                <a href="https://t.me/sajbazar" target="_blank" rel="noopener noreferrer" class="header-btn telegram">
                    مشاوره تلگرام
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="header-btn dashboard">داشبورد</a>
                @else
                    <a href="{{ route('unified.otp.login') }}" class="header-btn dashboard" style="background: linear-gradient(135deg, #16a34a, #3b82f6);">ورود / ثبت نام</a>
                @endauth
            </div>

            <!-- Mobile Menu Toggle -->
            <div class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <div class="hamburger-line"></div>
                <div class="hamburger-line"></div>
                <div class="hamburger-line"></div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <a href="https://t.me/sajbazar" target="_blank" rel="noopener noreferrer" class="mobile-menu-item telegram">
                مشاوره تلگرام
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="mobile-menu-item">داشبورد</a>
            @else
                <a href="{{ route('unified.otp.login') }}" class="mobile-menu-item">ورود / ثبت نام</a>
            @endauth
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>پلتفرم آنلاین انتقال وام</h1>
            <p>راهکار های نوین برای افراد حقیقی و حقوقی</p>
            <p>اولین پلتفرم وام‌دهی فرد به فرد در ایران، با ضمانت کامل امانت‌داری پول شما.</p>
            <div class="flex justify-center items-center gap-4 flex-wrap">
                <a href="{{ route('unified.otp.login') }}" class="cta-button bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700">شروع کنید</a>
                <a href="https://t.me/sajbazar" target="_blank" rel="noopener noreferrer" class="cta-button bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600">
                    مشاوره رایگان
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <h2 class="section-title">آمار و اعداد پلتفرم</h2>
            <p class="section-subtitle">اعتماد هزاران کاربر و ارائه خدمات با کیفیت بالا</p>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">وام فعال</div>
                    <div class="stat-description">وام‌های در حال پردازش</div>
                </div>

                <div class="stat-card">
                    <div class="stat-number">150+</div>
                    <div class="stat-label">کاربر فعال</div>
                    <div class="stat-description">کاربران راضی و فعال</div>
                </div>

                <div class="stat-card">
                    <div class="stat-number">∞</div>
                    <div class="stat-label">پشتیبانی 24/7</div>
                    <div class="stat-description">پشتیبانی مداوم و مطمئن</div>
                </div>

                <div class="stat-card">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">امنیت بانکی</div>
                    <div class="stat-description">بانک امین دارایی شما</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">امکانات پیشرفته وام ساز</h2>
            <p class="section-subtitle">تمامی ابزار های مورد نیاز برای خرید و فروش سریع و به صرفه وام شما</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3 class="feature-title">فروش سریع وام</h3>
                    <p class="feature-description">فروش سریع وام شما</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3 class="feature-title">بهترین قیمت خرید</h3>
                    <p class="feature-description">پیدا کردن بهترین قیمت خرید وام</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3 class="feature-title">ضمانت های قابل اتکا</h3>
                    <p class="feature-description">وجود ضمانت های قابل اتکا</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📈</div>
                    <h3 class="feature-title">سود سالانه بالا</h3>
                    <p class="feature-description">کسب سود سالانه بالاتر از هر راهکار درآمد ثابت برای انتقال دهنده وام</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🧮</div>
                    <h3 class="feature-title">محاسبه بلادرنگ</h3>
                    <p class="feature-description">ابزار محاسبه اقساط و سود با پارامترهای مختلف</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3 class="feature-title">امنیت بالا</h3>
                    <p class="feature-description">امنیت بالا به دلیل امین دارایی بودن بانک</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">چگونه کار می‌کند؟</h2>
            <p class="section-subtitle">در سه مرحله ساده شروع به کار کنید</p>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">۱</div>
                    <h3 class="step-title">ثبت نام</h3>
                    <p class="step-description">با شماره موبایل خود ثبت نام کرده و هویت خود را تایید کنید</p>
                </div>

                <div class="step-card">
                    <div class="step-number">۲</div>
                    <h3 class="step-title">مدیریت وام‌ها</h3>
                    <p class="step-description">وام‌های خود را ثبت کرده و از ابزارهای مدیریت استفاده کنید</p>
                </div>

                <div class="step-card">
                    <div class="step-number">۳</div>
                    <h3 class="step-title">فروش و مزایده</h3>
                    <p class="step-description">در بازار مزایده شرکت کرده و با دیوار ارتباط برقرار کنید</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Access Section -->
    <section class="quick-access">
        <div class="container">
            <h2 class="section-title">شروع آسان</h2>
            <p class="section-subtitle">فقط با شماره موبایل خود وارد شوید</p>

            <div class="flex justify-center items-center">
                <div class="access-card">
                    <div class="access-icon">📱</div>
                    <h3>ورود / ثبت نام</h3>
                    <p>شماره موبایل خود را وارد کنید و کد تأیید دریافت کنید</p>
                    <div class="access-buttons">
                        <a href="{{ route('unified.otp.login') }}" class="access-btn primary" style="background: linear-gradient(135deg, #16a34a, #3b82f6);">شروع کنید</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="final-cta">
        <div class="container">
            <h2>آماده شروع هستید؟</h2>
            <p>همین امروز با وام ساز شروع کنید و تجربه جدیدی از مدیریت وام داشته باشید</p>
            <div class="flex justify-center items-center">
                <a href="{{ route('unified.otp.login') }}" class="cta-button bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700">شروع رایگان</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-grid">
                <!-- Company Info Section -->
                <div class="footer-section">
                    <div class="footer-logo"> nationalkind.ir</div>
                    <p class="footer-description">ارائه دهنده راهکار های نوین مالی</p>
                </div>

                <!-- Address Section -->
                <div class="footer-section">
                    <div class="footer-address">
                        <strong>آدرس دفتر:</strong><br>
                        ایستگاه نوآوری دانشگاه شریف، جنب ساختمان نوآوری ایرانسل و اسنپ، خیابان حبیب الهی، محله تیموری
                    </div>
                </div>

                <!-- Trust Section -->
                <div class="footer-section">
                    <div class="footer-trust">
                        <a referrerpolicy='origin' target='_blank' href='https://trustseal.enamad.ir/?id=642085&Code=09HwelsIV9UjcdGw1o3s0dFs2cKyggbC'>
                            <img referrerpolicy='origin' src='https://trustseal.enamad.ir/logo.aspx?id=642085&Code=09HwelsIV9UjcdGw1o3s0dFs2cKyggbC' alt='نماد اعتماد الکترونیکی' style='cursor:pointer' code='09HwelsIV9UjcdGw1o3s0dFs2cKyggbC'>
                        </a>
                    </div>
                </div>
            </div>

            <p class="footer-copyright">© ۱۴۰۴ تمام حقوق محفوظ است</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle function
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const toggle = document.querySelector('.mobile-menu-toggle');

            mobileMenu.classList.toggle('active');
            toggle.classList.toggle('active');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobileMenu');
            const toggle = document.querySelector('.mobile-menu-toggle');

            if (!mobileMenu.contains(event.target) && !toggle.contains(event.target)) {
                mobileMenu.classList.remove('active');
                toggle.classList.remove('active');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add scroll animation to cards
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards
        document.querySelectorAll('.stat-card, .feature-card, .step-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>
