<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('جزئیات مزایده') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('seller.dashboard') }}"
                               class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                پنل فروشنده
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400">جزئیات مزایده</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Auction Details Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            {{ $auction->title }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $auction->description }}
                        </p>
                    </div>

                    @if($sellerSale && $sellerSale->current_step == 1)
                        <!-- Sale Details Content for Step 1 -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-blue-800 dark:text-blue-200 mb-4">
                                اطلاعات مزایده
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-gray-600 dark:text-gray-400">نوع وام:</span>
                                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $auction->loan_type === 'personal' ? 'مهربانی بانک ملی' : 'اعتبار ملی' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-gray-600 dark:text-gray-400">مبلغ وام:</span>
                                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ number_format($auction->principal_amount) }} تومان
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-gray-600 dark:text-gray-400">نرخ سود:</span>
                                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $auction->interest_rate_percent }}%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-gray-600 dark:text-gray-400">مدت بازپرداخت:</span>
                                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $auction->term_months }} ماه
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-gray-600 dark:text-gray-400">حداقل قیمت خرید:</span>
                                            <span class="font-medium text-red-600 dark:text-red-400">
                                                {{ number_format($auction->min_purchase_price) }} تومان
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                            <span class="text-gray-600 dark:text-gray-400">وضعیت:</span>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                {{ $auction->status->label() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Auction Statistics -->
                                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">آمار مزایده</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div class="text-center w-full">
                                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                                {{ $auction->bids()->count() }}
                                            </p>
                                            <p class="text-gray-600 dark:text-gray-400">تعداد پیشنهادات</p>
                                        </div>
                                        <div class="text-center w-full">
                                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                                {{ $highestBid ? number_format($highestBid->amount) : '0' }}
                                            </p>
                                            <p class="text-gray-600 dark:text-gray-400">بالاترین پیشنهاد (تومان)</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Current Highest Bid -->
                                @if($highestBid)
                                    <div class="mt-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                        <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-2">
                                            بالاترین پیشنهاد فعلی
                                        </h3>
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                                    {{ number_format($highestBid->amount) }} تومان
                                                </p>

                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm text-green-600 dark:text-green-400">
                                                    {{ $highestBid->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                        <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                            هنوز پیشنهادی ثبت نشده
                                        </h3>
                                        <p class="text-yellow-600 dark:text-yellow-400">
                                            منتظر اولین پیشنهاد باشید!
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif($sellerSale && $sellerSale->current_step == 2)
                        <!-- Contract Content for Step 2 -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-blue-800 dark:text-blue-200 mb-4">
                                تأیید قرارداد
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                                <div class="prose dark:prose-invert max-w-none">
                                    <div class="text-gray-700 dark:text-gray-300 leading-relaxed mb-6 text-sm">
                                        <p class="text-center font-bold text-lg mb-6">شرایط و قوانین مشارکت در فروش امتیاز وام در پلتفرم «وام‌ساز»</p>

                                        <p class="mb-4">
                                            این سند به منزله یک قرارداد قانونی و الزام‌آور میان پلتفرم آنلاین «وام‌ساز» (که از این پس «پلتفرم» نامیده می‌شود) و کاربری است که قصد فروش امتیاز وام خود را از طریق خدمات این پلتفرم دارد (که از این پس «فروشنده» نامیده می‌شود). ثبت‌نام و اقدام به عرضه امتیاز وام در این پلتفرم، به معنای مطالعه دقیق، درک کامل و پذیرش تمامی بندهای این قرارداد از سوی فروشنده است.
                                        </p>

                                        <div class="space-y-4">
                                            <div>
                                                <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2">ماده ۱- موضوع قرارداد</h4>
                                                <p>
                                                    موضوع این قرارداد، ایجاد یک بستر آنلاین برای عرضه و واگذاری امتیاز وام متعلق به فروشنده، به شخص ثالثی (که از این پس «خریدار» نامیده می‌شود) از طریق بازار مزایده‌ای است که توسط پلتفرم مدیریت می‌شود. کلیه مشخصات وام، از جمله مبلغ کل، تعداد اقساط، کارمزد و بانک عامل، توسط فروشنده در هنگام ثبت درخواست در پلتفرم اعلام می‌گردد و مسئولیت صحت اطلاعات وارد شده بر عهده وی می‌باشد.
                                                </p>
                                            </div>

                                            <div>
                                                <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2">ماده ۲- مبالغ و نحوه پرداخت</h4>
                                                <p class="mb-2">۱-۲- فروشنده برای شرکت در بازار مزایده، موظف است مبلغی را تحت عنوان «ورودی مزایده» که میزان آن در هنگام ثبت وام در پلتفرم مشخص شده است، به حساب معرفی‌شده توسط پلتفرم واریز نماید. این مبلغ به عنوان تضمین تعهد فروشنده برای انجام فرآیند واگذاری در نظر گرفته می‌شود.</p>
                                                <p>۲-۲- پس از انتقال موفقیت‌آمیز امتیاز وام در یکی از شعب بانک عامل به نام خریدار، پلتفرم متعهد است مبلغ نهایی پیشنهاد شده در مزایده را که از خریدار دریافت کرده است، حداکثر ظرف مدت ۴ ساعت کاری به حساب بانکی ثبت‌شده توسط فروشنده در پلتفرم واریز نماید.</p>
                                            </div>

                                            <div>
                                                <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2">ماده ۳- تعهدات فروشنده</h4>
                                                <p class="mb-2">۱-۳- فروشنده متعهد می‌شود حداکثر ظرف مدت ۲۴ ساعت پس از اعلام برنده مزایده، با هماهنگی پلتفرم، جهت انجام کلیه مراحل اداری و امضای اسناد لازم برای انتقال امتیاز وام به خریدار معرفی‌شده، در شعبه بانک مربوطه حاضر شود.</p>
                                                <p class="mb-2">۲-۳- پس از نهایی شدن نتیجه مزایده و مشخص شدن خریدار، فروشنده حق واگذاری یا انتقال امتیاز وام خود را به هیچ شخص دیگری، خارج از چارچوب پلتفرم، نخواهد داشت.</p>
                                                <p>۳-۳- در صورت انصراف فروشنده پس از پذیرش پیشنهاد خریدار و نهایی شدن مزایده، مبلغ «ورودی مزایده» وی ضبط شده و غیرقابل استرداد خواهد بود. همچنین، معادل پنجاه درصد (۵۰%) از این مبلغ به عنوان خسارت عدم انجام تعهد، به خریداری که از سوی پلتفرم معرفی شده است، پرداخت خواهد شد. فروشنده با پذیرش این شرایط، حق هرگونه اعتراض در این خصوص را از خود سلب می‌نماید.</p>
                                            </div>

                                            <div>
                                                <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2">ماده ۴- تعهدات پلتفرم</h4>
                                                <p class="mb-2">۱-۴- پلتفرم متعهد به ایجاد بستر مزایده، معرفی خریدار به فروشنده و هماهنگی‌های لازم جهت حضور طرفین در بانک برای انتقال امتیاز وام است. پلتفرم هیچ‌گونه مسئولیتی در قبال فرآیندهای داخلی بانک یا نتایج ناشی از آن نخواهد داشت.</p>
                                                <p>۲-۴- در صورتی که خریدار معرفی‌شده از سوی پلتفرم، از تکمیل فرآیند انتقال وام انصراف دهد یا در انجام تعهدات خود کوتاهی کند، پلتفرم موظف است اصل مبلغ «ورودی مزایده» را به فروشنده بازگرداند. علاوه بر آن، معادل پنجاه درصد (۵۰%) از مبلغ ورودی که خریدار پرداخت کرده است، به عنوان خسارت به فروشنده پرداخت خواهد شد.</p>
                                            </div>

                                            <div>
                                                <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2">ماده ۵- فسخ و بازگشت وجه</h4>
                                                <p class="mb-2">۱-۵- در صورتی که فروشنده پیش از انتخاب خریدار و نهایی شدن مزایده از فروش امتیاز وام خود منصرف شود، مبلغ «ورودی مزایده» به طور کامل به وی بازگردانده خواهد شد.</p>
                                                <p>۲-۵- چنانچه پلتفرم در معرفی خریدار واجد شرایط به فروشنده کوتاهی کند یا فرآیند مزایده به دلیلی از سمت پلتفرم لغو گردد، فروشنده حق فسخ قرارداد و استرداد کامل وجه ورودی خود را خواهد داشت.</p>
                                            </div>

                                            <div>
                                                <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2">ماده ۶- حل اختلاف</h4>
                                                <p>در صورت بروز هرگونه اختلاف ناشی از تفسیر یا اجرای این قرارداد، طرفین تلاش خواهند کرد تا موضوع را از طریق مذاکره مستقیم و مسالمت‌آمیز حل‌وفصل نمایند. در صورت عدم دستیابی به توافق ظرف مدت ۷ روز کاری، مرجع صالح برای رسیدگی به اختلاف، مراجع قضایی ذیصلاح شهر تهران خواهد بود.</p>
                                            </div>

                                            <div>
                                                <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2">ماده ۷- مقررات عمومی</h4>
                                                <p>این قرارداد به عنوان توافق‌نامه کامل میان پلتفرم و فروشنده تلقی می‌شود. پلتفرم حق تغییر یا به‌روزرسانی مفاد این قرارداد را در هر زمان برای خود محفوظ می‌دارد و نسخه جدید از طریق وب‌سایت به اطلاع کاربران خواهد رسید. ادامه استفاده کاربر از خدمات پلتفرم به منزله پذیرش تغییرات جدید خواهد بود.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- OTP Form -->
                                    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-4">تأیید قرارداد با کد تأیید</h4>
                                        <div id="otp-form" class="space-y-4">
                                            <div>
                                                <label for="otp_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    کد تأیید به این شماره ارسال میشود: {{ Auth::user()->phone }}
                                                </label>
                                                <input type="text" id="otp_code" name="otp_code" maxlength="6"
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-center text-lg tracking-widest"
                                                       placeholder="000000">
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="button" id="send-otp-btn"
                                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                                    ارسال کد تأیید
                                                </button>
                                                <button type="button" id="verify-otp-btn" disabled
                                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                    تأیید کد
                                                </button>
                                            </div>
                                            <div id="otp-timer" class="text-center text-sm text-gray-500 dark:text-gray-400 hidden">
                                                ارسال مجدد کد در <span id="countdown">60</span> ثانیه
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($sellerSale && $sellerSale->current_step == 3)
                        <!-- Payment Content for Step 3 -->
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-green-800 dark:text-green-200 mb-4">
                                پرداخت کارمزد فروش
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-green-200 dark:border-green-700">
                                <div class="space-y-6">
                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                        <h3 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                            مبلغ کارمزد: 3,000,000 تومان
                                        </h3>
                                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-4">
                                            برای ادامه فرآیند فروش، لطفاً کارمزد فروش را پرداخت کرده و فیش واریزی را آپلود کنید.
                                        </p>

                                        <!-- Payment Information -->
                                        <div class="bg-indigo-50 dark:bg-indigo-900/30 rounded-lg p-4 border border-indigo-400 dark:border-indigo-600">
                                            <h4 class="font-semibold text-indigo-700 dark:text-amber-400 mb-3 text-center">
                                                اطلاعات پرداخت
                                            </h4>
                                            <div class="text-center space-y-2">
                                                <div class="bg-indigo-100 dark:bg-indigo-800 rounded-lg p-3">
                                                    <span class="text-2xl md:text-3xl font-extrabold text-indigo-800 dark:text-amber-300 tracking-widest select-all">6037-9915-6739-2208</span>
                                                </div>
                                                <div class="bg-indigo-50 dark:bg-indigo-900 rounded-lg p-3">
                                                    <span class="text-base md:text-lg font-semibold text-indigo-900 dark:text-indigo-100">سجاد باقری آذر چشمقان</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $sellerReceipt = \App\Models\PaymentReceipt::where('auction_id', $auction->id)
                                            ->where('user_id', Auth::id())
                                            ->where('type', \App\Enums\PaymentType::SELLER_FEE)
                                            ->first();
                                    @endphp

                                    @if($sellerReceipt && $sellerReceipt->image_path)
                                        <!-- Receipt Status -->
                                        <div class="space-y-4">
                                            @if($sellerReceipt->status === \App\Enums\PaymentStatus::APPROVED)
                                                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                                    <div class="flex items-center space-x-3">
                                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        <div>
                                                            <h4 class="font-semibold text-green-800 dark:text-green-200">رسید تأیید شده</h4>
                                                            <p class="text-sm text-green-700 dark:text-green-300">رسید پرداخت کارمزد شما توسط مدیر تأیید شده است.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <a href="{{ route('seller.sale.bid-acceptance', $auction) }}"
                                                       class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors">
                                                        ادامه به مرحله بعد
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </a>
                                                </div>
                                            @elseif($sellerReceipt->status === \App\Enums\PaymentStatus::REJECTED)
                                                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                                    <div class="flex items-center space-x-3">
                                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        <div>
                                                            <h4 class="font-semibold text-red-800 dark:text-red-200">رسید رد شده</h4>
                                                            <p class="text-sm text-red-700 dark:text-red-300">رسید پرداخت کارمزد شما توسط مدیر رد شده است.</p>
                                                            @if($sellerReceipt->reject_reason)
                                                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                                                                    <strong>دلیل رد:</strong> {{ $sellerReceipt->reject_reason }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Re-upload form -->
                                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">آپلود مجدد رسید</h4>
                                                    <form action="{{ route('seller.receipt.upload', $auction) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                        @csrf
                                                        <div>
                                                            <label for="receipt_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                فیش واریزی (فرمت‌های مجاز: JPG, PNG, PDF - حداکثر 5MB)
                                                            </label>
                                                            <input type="file" id="receipt_image" name="receipt_image" accept=".jpg,.jpeg,.png,.pdf" required
                                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                                        </div>
                                                        <button type="submit"
                                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                                                            آپلود رسید
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                                    <div class="flex items-center space-x-3">
                                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <div>
                                                            <h4 class="font-semibold text-yellow-800 dark:text-yellow-200">در انتظار تأیید</h4>
                                                            <p class="text-sm text-yellow-700 dark:text-yellow-300">رسید پرداخت کارمزد شما در انتظار بررسی و تأیید مدیر است.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <!-- Upload Form -->
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">آپلود فیش واریزی</h4>
                                            <form action="{{ route('seller.receipt.upload', $auction) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                @csrf
                                                <div>
                                                    <label for="receipt_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                        فیش واریزی (فرمت‌های مجاز: JPG, PNG, PDF - حداکثر 5MB)
                                                    </label>
                                                    <input type="file" id="receipt_image" name="receipt_image" accept=".jpg,.jpeg,.png,.pdf" required
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                                </div>
                                                <button type="submit"
                                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                                    آپلود رسید
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($sellerSale && $sellerSale->current_step == 4)
                        <!-- Bid Acceptance Content for Step 4 -->
                        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-purple-800 dark:text-purple-200 mb-4">
                                پذیرش پیشنهادات
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-purple-200 dark:border-purple-700">
                                <div class="space-y-6">
                                    @if($highestBid)
                                        <!-- Highest Bid Display -->
                                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                                            <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-4">
                                                بالاترین پیشنهاد
                                            </h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div class="space-y-3">
                                                    <div class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-700">
                                                        <span class="text-green-700 dark:text-green-300">مبلغ پیشنهاد:</span>
                                                        <span class="font-bold text-green-800 dark:text-green-200 text-xl">
                                                            {{ number_format($highestBid->amount) }} تومان
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <div class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-700">
                                                        <span class="text-green-700 dark:text-green-300">تاریخ پیشنهاد:</span>
                                                        <span class="font-medium text-green-800 dark:text-green-200">
                                                            {{ $highestBid->created_at->format('Y/m/d H:i') }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-700">
                                                        <span class="text-green-700 dark:text-green-300">وضعیت:</span>
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                            {{ $highestBid->status->label() }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Auction Details for Reference -->
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                                اطلاعات وام
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">نوع وام:</span>
                                                    <span class="font-medium">{{ $auction->loan_type === 'personal' ? 'مهربانی بانک ملی' : 'اعتبار ملی' }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">مبلغ وام:</span>
                                                    <span class="font-medium">{{ number_format($auction->principal_amount) }} تومان</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">نرخ سود:</span>
                                                    <span class="font-medium">{{ $auction->interest_rate_percent }}%</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">مدت بازپرداخت:</span>
                                                    <span class="font-medium">{{ $auction->term_months }} ماه</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bid Acceptance Form -->
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                                            <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-4">
                                                تأیید پیشنهاد
                                            </h4>
                                            <p class="text-blue-700 dark:text-blue-300 mb-4">
                                                آیا می‌خواهید این پیشنهاد را بپذیرید؟ با پذیرش این پیشنهاد، فرآیند فروش ادامه خواهد یافت.
                                            </p>
                                            <form action="{{ route('seller.bid.accept', $auction) }}" method="POST" class="space-y-4">
                                                @csrf
                                                <input type="hidden" name="bid_id" value="{{ $highestBid->id }}">
                                                <div class="flex gap-4">
                                                    <button type="submit"
                                                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        پذیرش پیشنهاد
                                                    </button>

                                                </div>
                                            </form>
                                        </div>
                                    @else
                                        <!-- No Bids Available -->
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                                            <div class="text-center">
                                                <svg class="w-16 h-16 text-yellow-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                                    هنوز پیشنهادی ثبت نشده
                                                </h3>
                                                <p class="text-yellow-600 dark:text-yellow-400">
                                                    منتظر دریافت پیشنهادات از خریداران باشید.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($sellerSale && $sellerSale->current_step == 6)
                        <!-- Loan Transfer Content for Step 6 -->
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-green-800 dark:text-green-200 mb-4">
                                انتقال وام
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-green-200 dark:border-green-700">
                                <div class="space-y-6">
                                    @php
                                        $acceptedBid = $auction->bids()
                                            ->where('status', \App\Enums\BidStatus::ACCEPTED)
                                            ->first();
                                        $buyer = $acceptedBid ? $acceptedBid->buyer : null;
                                        $loanTransfer = \App\Models\LoanTransfer::where('auction_id', $auction->id)->first();
                                    @endphp

                                    @if($acceptedBid && $buyer)
                                        <!-- Buyer Information -->
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                                            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                                                اطلاعات خریدار
                                            </h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div class="space-y-3">

                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">کد ملی:</span>
                                                        <span class="font-medium text-blue-800 dark:text-blue-200 text-lg">
                                                            {{ $buyer->national_id ?? 'ثبت نشده' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">

                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">مبلغ پرداخت:</span>
                                                        <span class="font-bold text-green-600 dark:text-green-400 text-lg">
                                                            {{ number_format($acceptedBid->amount) }} تومان
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <!-- Transfer Instructions -->
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                            <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                                دستورالعمل انتقال وام
                                            </h4>
                                            <div class="text-sm text-yellow-700 dark:text-yellow-300 space-y-2">
                                                <p>• وام را به کد ملی خریدار انتقال دهید: <strong>{{ $buyer->national_id ?? 'کد ملی ثبت نشده' }}</strong></p>
                                                <p>• پس از انتقال، فیش انتقال وام را آپلود کنید</p>
                                                <p>• خریدار فیش را بررسی و تأیید خواهد کرد</p>
                                            </div>
                                        </div>

                                        @php
                                            $transferReceipt = \App\Models\PaymentReceipt::where('auction_id', $auction->id)
                                                ->where('user_id', Auth::id())
                                                ->where('type', \App\Enums\PaymentType::LOAN_TRANSFER)
                                                ->latest()
                                                ->first();
                                        @endphp

                                        @if($transferReceipt && $transferReceipt->image_path)
                                            <!-- Transfer Receipt Status -->
                                            <div class="space-y-4">
                                                @if($transferReceipt->status->value === 'approved')
                                                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                                        <div class="flex items-center space-x-3">
                                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        <div>
                                                            <h4 class="font-semibold text-green-800 dark:text-green-200">فیش انتقال تأیید شده</h4>
                                                            <p class="text-sm text-green-700 dark:text-green-300">فیش انتقال وام شما توسط ادمین تأیید شده است.</p>
                                                            @if($transferReceipt->iban)
                                                                <div class="mt-2 p-2 bg-green-100 dark:bg-green-800 rounded">
                                                                    <p class="text-xs text-green-600 dark:text-green-300">شماره شبا ثبت شده:</p>
                                                                    <p class="font-mono text-sm font-bold text-green-800 dark:text-green-200">{{ $transferReceipt->iban }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        </div>
                                                    </div>
                                                @elseif($transferReceipt->status->value === 'rejected')
                                                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                                        <div class="flex items-center space-x-3">
                                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            <div>
                                                                <h4 class="font-semibold text-red-800 dark:text-red-200">فیش انتقال رد شده</h4>
                                                                <p class="text-sm text-red-700 dark:text-red-300">فیش انتقال وام شما توسط ادمین رد شده است.</p>
                                                                @if($transferReceipt->reject_reason)
                                                                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                                                                        <strong>دلیل رد:</strong> {{ $transferReceipt->reject_reason }}
                                                                    </p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Re-upload form -->
                                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">آپلود مجدد فیش انتقال</h4>

                                                        <form action="{{ route('seller.loan.transfer', $auction) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                            @csrf
                                                            <div>
                                                                <label for="transfer_receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                    فیش انتقال وام (فرمت‌های مجاز: JPG, PNG, PDF - حداکثر 10MB)
                                                                </label>
                                                                <input type="file" id="transfer_receipt" name="transfer_receipt" accept=".jpg,.jpeg,.png,.pdf" required
                                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label for="receipt_iban_reupload" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                    شماره شبا برای دریافت پرداخت
                                                                </label>
                                                                <input type="text"
                                                                       id="receipt_iban_reupload"
                                                                       name="iban"
                                                                       value="{{ Auth::user()->iban ?? old('iban') }}"
                                                                       placeholder="123456789012345678901234"
                                                                       maxlength="24"
                                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white font-mono text-center tracking-wider"
                                                                       required>
                                                                @error('iban')
                                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                            <button type="submit"
                                                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                                                                آپلود مجدد فیش
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                                        <div class="flex items-center space-x-3">
                                                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <div>
                                                                <h4 class="font-semibold text-yellow-800 dark:text-yellow-200">در انتظار تأیید ادمین</h4>
                                                                <p class="text-sm text-yellow-700 dark:text-yellow-300">فیش انتقال وام شما در انتظار بررسی و تأیید ادمین است.</p>
                                                                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                                                    وضعیت فعلی: {{ $transferReceipt->status->label() }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <!-- Upload Transfer Receipt Form -->
                                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">آپلود فیش انتقال وام</h4>

                                                <form action="{{ route('seller.loan.transfer', $auction) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                    @csrf
                                                    <div>
                                                        <label for="transfer_receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            فیش انتقال وام (فرمت‌های مجاز: JPG, PNG, PDF - حداکثر 10MB)
                                                        </label>
                                                        <input type="file" id="transfer_receipt" name="transfer_receipt" accept=".jpg,.jpeg,.png,.pdf" required
                                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                                    </div>
                                                    <div>
                                                        <label for="receipt_iban" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            شماره شبا برای دریافت پرداخت
                                                        </label>
                                                        <input type="text"
                                                               id="receipt_iban"
                                                               name="iban"
                                                               value="{{ Auth::user()->iban ?? old('iban') }}"
                                                               placeholder="123456789012345678901234"
                                                               maxlength="24"
                                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white font-mono text-center tracking-wider"
                                                               required>
                                                        @error('iban')
                                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <button type="submit"
                                                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                                        آپلود فیش انتقال
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @else
                                        <!-- No Accepted Bid -->
                                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                                            <div class="text-center">
                                                <svg class="w-16 h-16 text-red-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-2">
                                                    هیچ پیشنهاد پذیرفته شده‌ای یافت نشد
                                                </h3>
                                                <p class="text-red-600 dark:text-red-400">
                                                    ابتدا باید پیشنهادی را بپذیرید تا بتوانید وام را انتقال دهید.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($sellerSale && $sellerSale->current_step == 8)
                        <!-- Transfer Confirmation Content for Step 8 -->
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-green-800 dark:text-green-200 mb-4">
                                تأیید انتقال وام
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-green-200 dark:border-green-700">
                                <div class="space-y-6">
                                    <!-- Success Message -->
                                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                                        <div class="flex items-center space-x-4">
                                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <h3 class="text-xl font-semibold text-green-800 dark:text-green-200">
                                                    انتقال وام تأیید شد
                                                </h3>
                                                <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                                    فیش انتقال وام شما توسط ادمین تأیید شده است. خریدار نیز انتقال را تأیید کرده است.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $acceptedBid = $auction->bids()
                                            ->where('status', \App\Enums\BidStatus::ACCEPTED)
                                            ->first();
                                        $buyer = $acceptedBid ? $acceptedBid->buyer : null;
                                    @endphp

                                    @if($acceptedBid && $buyer)
                                        <!-- Transaction Summary -->
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                                            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                                                خلاصه معامله
                                            </h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div class="space-y-3">

                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">کد ملی:</span>
                                                        <span class="font-medium text-blue-800 dark:text-blue-200">
                                                            {{ $buyer->national_id ?? 'ثبت نشده' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">مبلغ معامله:</span>
                                                        <span class="font-bold text-green-600 dark:text-green-400 text-lg">
                                                            {{ number_format($acceptedBid->amount) }} تومان
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">وضعیت:</span>
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                            تکمیل شده
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Completion Message -->
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                            فرآیند فروش تکمیل شد
                                        </h4>
                                        <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                            <div class="flex items-start space-x-2">
                                                <span class="text-green-500 mt-1">✓</span>
                                                <span>وام با موفقیت به خریدار انتقال یافت</span>
                                            </div>
                                            <div class="flex items-start space-x-2">
                                                <span class="text-green-500 mt-1">✓</span>
                                                <span>پرداخت مبلغ معامله دریافت شد</span>
                                            </div>
                                            <div class="flex items-start space-x-2">
                                                <span class="text-green-500 mt-1">✓</span>
                                                <span>خریدار انتقال را تأیید کرد</span>
                                            </div>
                                            <div class="flex items-start space-x-2">
                                                <span class="text-green-500 mt-1">✓</span>
                                                <span>معامله با موفقیت تکمیل شد</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($sellerSale && $sellerSale->current_step > 4)
                        <!-- Content for Steps 5-8 -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-blue-800 dark:text-blue-200 mb-4">
                                مرحله {{ $sellerSale->getDisplayStep() }} از 8
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                                        <div>
                                            <h3 class="font-semibold text-blue-800 dark:text-blue-200">
                                                فرآیند فروش در حال انجام است
                                            </h3>
                                            <p class="text-sm text-blue-600 dark:text-blue-400">
                                                مرحله فعلی:
                                                @php
                                                    $stepTitles = [
                                                        1 => 'اطلاعات مزایده',
                                                        2 => 'تأیید قرارداد',
                                                        3 => 'پرداخت کارمزد',
                                                        4 => 'پذیرش پیشنهاد',
                                                        5 => 'انتظار پرداخت خریدار',
                                                        6 => 'انتقال وام',
                                                        7 => 'تأیید انتقال',
                                                        8 => 'تکمیل فروش'
                                                    ];
                                                @endphp
                                                {{ $stepTitles[$sellerSale->current_step] ?? 'مرحله ناشناخته' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                            اطلاعات مزایده
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">نوع وام:</span>
                                                <span class="font-medium">{{ $auction->loan_type === 'personal' ? 'مهربانی بانک ملی' : 'اعتبار ملی' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">مبلغ وام:</span>
                                                <span class="font-medium">{{ number_format($auction->principal_amount) }} تومان</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">نرخ سود:</span>
                                                <span class="font-medium">{{ $auction->interest_rate_percent }}%</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">مدت بازپرداخت:</span>
                                                <span class="font-medium">{{ $auction->term_months }} ماه</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">حداقل قیمت خرید:</span>
                                                <span class="font-medium text-red-600 dark:text-red-400">{{ number_format($auction->min_purchase_price) }} تومان</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if($highestBid)
                                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                            <h4 class="font-semibold text-green-800 dark:text-green-200 mb-2">
                                                بالاترین پیشنهاد فعلی
                                            </h4>
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <p class="text-xl font-bold text-green-600 dark:text-green-400">
                                                        {{ number_format($highestBid->amount) }} تومان
                                                    </p>

                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm text-green-600 dark:text-green-400">
                                                        {{ $highestBid->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if($sellerSale && $sellerSale->current_step == 1)
                            <!-- Step 1: Continue to Next Step -->
                            <form method="POST" action="{{ route('seller.sale.continue', $auction) }}" class="flex-1">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                    ادامه به مرحله بعد
                                </button>
                            </form>
                        @elseif($sellerSale && $sellerSale->current_step == 2)
                            <!-- Step 2: OTP form is in the content above -->
                            <div class="text-center py-3">
                                <p class="text-gray-500 dark:text-gray-400 text-sm">
                                    لطفاً قرارداد را با کد تأیید تأیید کنید
                                </p>
                            </div>
                        @elseif($sellerSale && $sellerSale->current_step == 8)
                            <!-- Step 8: Transaction completed - no action buttons needed -->
                            <div class="text-center py-3">
                                <p class="text-green-600 dark:text-green-400 text-sm font-medium">
                                    معامله با موفقیت تکمیل شد
                                </p>
                            </div>
                        @elseif($sellerSale && $sellerSale->current_step > 2)
                            <!-- Steps 3-7: Show appropriate action based on step -->
                            @php
                                $actionRoute = match($sellerSale->status->value) {
                                    'contract_confirmed' => route('seller.sale.payment', $auction),
                                    'fee_approved' => route('seller.sale.bid-acceptance', $auction),
                                    'offer_accepted' => route('seller.sale.awaiting-buyer-payment', $auction),
                                    'awaiting_buyer_payment' => route('seller.sale.awaiting-buyer-payment', $auction),
                                    'buyer_payment_approved' => route('seller.sale.loan-transfer', $auction),
                                    'loan_transferred' => route('seller.sale.awaiting-transfer-confirmation', $auction),
                                    'transfer_confirmed' => route('seller.sale.completion', $auction),
                                    'completed' => route('seller.sale.completion', $auction),
                                    default => null,
                                };
                            @endphp

                            @if($actionRoute)

                            @else
                                <div class="text-center py-3">
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                                        لطفاً منتظر تکمیل مرحله فعلی باشید
                                    </p>
                                </div>
                            @endif
                        @endif

                        <a href="{{ route('seller.dashboard') }}"
                           class="w-full sm:w-auto bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                            بازگشت
                        </a>
                    </div>

                    <!-- Step Progress Component -->
                    @if($sellerSale)
                        <div class="mt-6">
                            <x-seller-step-progress :sellerSale="$sellerSale" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($sellerSale && $sellerSale->current_step == 2)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sendOtpBtn = document.getElementById('send-otp-btn');
            const verifyOtpBtn = document.getElementById('verify-otp-btn');
            const otpInput = document.getElementById('otp_code');
            const timerDiv = document.getElementById('otp-timer');
            const countdownSpan = document.getElementById('countdown');
            let countdown = 0;

            // Send OTP functionality
            sendOtpBtn.addEventListener('click', function() {
                // Disable button immediately
                sendOtpBtn.disabled = true;
                sendOtpBtn.textContent = 'در حال ارسال...';

                fetch('{{ route("seller.sale.contract.otp-send", $auction) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        startCountdown();
                    } else {
                        alert(data.message || 'خطا در ارسال کد تأیید');
                        // Re-enable button on error
                        sendOtpBtn.disabled = false;
                        sendOtpBtn.textContent = 'ارسال کد تأیید';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('خطا در ارسال کد تأیید');
                    // Re-enable button on error
                    sendOtpBtn.disabled = false;
                    sendOtpBtn.textContent = 'ارسال کد تأیید';
                });
            });

            // Verify OTP functionality
            verifyOtpBtn.addEventListener('click', function() {
                const otpCode = otpInput.value.trim();
                if (otpCode.length !== 6) {
                    alert('لطفاً کد تأیید 6 رقمی را وارد کنید');
                    return;
                }

                const formData = new FormData();
                formData.append('otp_code', otpCode);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                fetch('{{ route("seller.sale.contract.verify-otp", $auction) }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok || response.status === 302) {
                        // Success - reload the page
                        window.location.reload();
                    } else {
                        return response.text().then(text => {
                            throw new Error(text);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('کد تأیید نامعتبر است');
                });
            });

            // Enable verify button when OTP is entered
            otpInput.addEventListener('input', function() {
                verifyOtpBtn.disabled = this.value.length !== 6;
            });

            // Start countdown timer
            function startCountdown() {
                countdown = 60;
                sendOtpBtn.disabled = true;
                sendOtpBtn.textContent = 'ارسال مجدد';
                timerDiv.classList.remove('hidden');

                const interval = setInterval(() => {
                    countdownSpan.textContent = countdown;
                    countdown--;

                    if (countdown < 0) {
                        clearInterval(interval);
                        sendOtpBtn.disabled = false;
                        sendOtpBtn.textContent = 'ارسال کد تأیید';
                        timerDiv.classList.add('hidden');
                    }
                }, 1000);
            }
        });
    </script>
    @endif

    @if($sellerSale && $sellerSale->current_step == 6)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Format IBAN inputs for receipt forms - only numbers
            const ibanInputs = document.querySelectorAll('#receipt_iban, #receipt_iban_reupload');

            ibanInputs.forEach(ibanInput => {
                if (ibanInput) {
                    // Format IBAN input - only allow numbers
                    ibanInput.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, ''); // Only digits

                        // Limit to 24 characters
                        if (value.length > 24) {
                            value = value.substring(0, 24);
                        }

                        e.target.value = value;
                    });

                    // Prevent non-numeric characters
                    ibanInput.addEventListener('keydown', function(e) {
                        // Allow: backspace, delete, tab, escape, enter, arrows
                        if ([8, 9, 27, 13, 46, 37, 38, 39, 40].indexOf(e.keyCode) !== -1 ||
                            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                            (e.keyCode === 65 && e.ctrlKey === true) ||
                            (e.keyCode === 67 && e.ctrlKey === true) ||
                            (e.keyCode === 86 && e.ctrlKey === true) ||
                            (e.keyCode === 88 && e.ctrlKey === true)) {
                            return;
                        }
                        // Ensure that it is a number and stop the keypress
                        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                            e.preventDefault();
                        }
                    });
                }
            });
        });
    </script>
    @endif
</x-app-layout>
