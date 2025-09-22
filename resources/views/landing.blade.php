<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>وام ساز - پلتفرم آنلاین انتقال وام</title>

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
            padding: 40px 0;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-logo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .footer-text {
            color: #bdc3c7;
            margin-bottom: 20px;
        }

        .footer-copyright {
            color: #95a5a6;
            font-size: 0.9rem;
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
            <a href="/" class="logo">وام ساز</a>
            <div class="header-buttons">
                @auth
                    <a href="{{ route('dashboard') }}" class="header-btn dashboard">داشبورد</a>
                @else
                    <a href="{{ route('unified.otp.login') }}" class="header-btn dashboard" style="background: linear-gradient(135deg, #16a34a, #3b82f6);">ورود / ثبت نام</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>پلتفرم آنلاین انتقال وام</h1>
            <p>راهکار های نوین برای افراد حقیقی و حقوقی</p>
            <p>وام ساز پلتفرم جامعی است که تمامی نیازهای مدیریت وام، مزایده‌گذاری، و ارتباط با بازار دیوار را پوشش می‌دهد. با استفاده از هوش مصنوعی و تکنولوژی‌های نوین، بهترین تجربه را برای شما فراهم می‌کنیم.</p>
            <div class="flex justify-center items-center">
                <a href="{{ route('unified.otp.login') }}" class="cta-button bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700">شروع کنید</a>
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
            <div class="footer-logo">شرکت nationalkind.ir</div>
            <p class="footer-text">ارائه دهنده راهکار های نوین مالی</p>
            <p class="footer-text">
                <a referrerpolicy='origin' target='_blank' href='https://trustseal.enamad.ir/?id=642085&Code=09HwelsIV9UjcdGw1o3s0dFs2cKyggbC'><img referrerpolicy='origin' src='https://trustseal.enamad.ir/logo.aspx?id=642085&Code=09HwelsIV9UjcdGw1o3s0dFs2cKyggbC' alt='' style='cursor:pointer' code='09HwelsIV9UjcdGw1o3s0dFs2cKyggbC'></a>
            </p>
            <p class="footer-copyright">© ۱۴۰۴ تمام حقوق محفوظ است</p>
        </div>
    </footer>

    <script>
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
