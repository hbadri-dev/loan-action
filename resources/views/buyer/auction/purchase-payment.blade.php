<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مرحله ۵: پرداخت مبلغ خرید') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            1
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">جزئیات وام</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            2
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">قرارداد</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            3
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">کارمزد</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            4
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">پیشنهاد</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            5
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">تأیید فروشنده</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            5
                        </div>
                        <span class="mr-2 text-sm font-medium text-blue-600 dark:text-blue-400">پرداخت خرید</span>
                    </div>
                </div>
            </div>

            <!-- Purchase Payment -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        پرداخت مبلغ خرید
                    </h1>

                    <!-- Success Message -->
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-green-800 dark:text-green-200 font-medium">
                                تبریک! پیشنهاد شما توسط فروشنده پذیرفته شد.
                            </span>
                        </div>
                    </div>

                    <!-- Bid Summary -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                            خلاصه پیشنهاد پذیرفته شده
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">مبلغ پیشنهادی:</span>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($userBid->amount) }} تومان
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">تاریخ پذیرش:</span>
                                <p class="font-medium text-blue-600 dark:text-blue-400">
                                    {{ $userBid->updated_at->format('Y/m/d H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Amount -->
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 mb-6">
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-2">
                                مبلغ قابل پرداخت
                            </h3>
                            <p class="text-3xl font-bold text-red-600 dark:text-red-400">
                                {{ number_format($userBid->amount) }} تومان
                            </p>
                        </div>
                    </div>

                    <!-- Payment Instructions -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            دستورالعمل پرداخت:
                        </h3>
                        <div class="space-y-3 text-gray-700 dark:text-gray-300">
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">1</span>
                                <p>مبلغ <strong>{{ number_format($userBid->amount) }} تومان</strong> را به کارت زیر واریز کنید:</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mr-9">
                                <div class="text-center">
                                    <p class="text-lg font-mono font-bold text-gray-900 dark:text-gray-100">
                                        6037-9915-6739-2208
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        به نام: سجاد باقری آذر چشمقان
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">2</span>
                                <p>رسید پرداخت را در فرم زیر آپلود کنید</p>
                            </div>
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">3</span>
                                <p>منتظر تأیید رسید توسط مدیر باشید</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    @if($paymentReceipt->status->value === 'pending_review')
                        @if($paymentReceipt->image_path)
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-yellow-800 dark:text-yellow-200 font-medium">
                                        رسید پرداخت آپلود شده و در انتظار بررسی است.
                                    </span>
                                </div>
                            </div>
                        @else
                            <!-- Upload Form -->
                            <form method="POST" action="{{ route('buyer.auction.purchase-payment.receipt', $auction) }}"
                                  enctype="multipart/form-data" class="mb-6">
                                @csrf

                                <div class="mb-4">
                                    <label for="national_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        کد ملی
                                    </label>
                                    <input id="national_id"
                                           name="national_id"
                                           type="text"
                                           maxlength="10"
                                           pattern="[0-9]{10}"
                                           class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                                           placeholder="کد ملی ۱۰ رقمی خود را وارد کنید"
                                           required>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        کد ملی ۱۰ رقمی خود را وارد کنید
                                    </p>
                                </div>

                                <div class="mb-4">
                                    <label for="receipt_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        آپلود رسید پرداخت
                                    </label>
                                    <input id="receipt_image"
                                           name="receipt_image"
                                           type="file"
                                           accept="image/jpeg,image/png,image/webp"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 dark:file:bg-red-900 dark:file:text-red-300"
                                           required>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        فرمت‌های مجاز: JPG, PNG, WebP (حداکثر ۵ مگابایت)
                                    </p>
                                </div>

                                <button type="submit"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                    آپلود رسید پرداخت
                                </button>
                            </form>
                        @endif
                    @elseif($paymentReceipt->status->value === 'approved')
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-green-800 dark:text-green-200 font-medium">
                                    پرداخت تأیید شد. تاریخ تأیید: {{ $paymentReceipt->reviewed_at->format('Y/m/d H:i') }}
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('buyer.auction.loan-transfer', $auction) }}"
                           class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors block mb-6">
                            ادامه به مرحله بعد (انتظار انتقال وام)
                        </a>
                    @elseif($paymentReceipt->status->value === 'rejected')
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-red-800 dark:text-red-200 font-medium">
                                    رسید پرداخت رد شد.
                                </span>
                            </div>
                            @if($paymentReceipt->reject_reason)
                                <p class="text-red-700 dark:text-red-300 text-sm">
                                    دلیل رد: {{ $paymentReceipt->reject_reason }}
                                </p>
                            @endif
                        </div>

                        <!-- Re-upload Form -->
                        <form method="POST" action="{{ route('buyer.auction.purchase-payment.receipt', $auction) }}"
                              enctype="multipart/form-data" class="mb-6">
                            @csrf

                            <div class="mb-4">
                                <label for="national_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    کد ملی
                                </label>
                                <input id="national_id"
                                       name="national_id"
                                       type="text"
                                       maxlength="10"
                                       pattern="[0-9]{10}"
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="کد ملی ۱۰ رقمی خود را وارد کنید"
                                       required>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    کد ملی ۱۰ رقمی خود را وارد کنید
                                </p>
                            </div>

                            <div class="mb-4">
                                <label for="receipt_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    آپلود مجدد رسید پرداخت
                                </label>
                                <input id="receipt_image"
                                       name="receipt_image"
                                       type="file"
                                       accept="image/jpeg,image/png,image/webp"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 dark:file:bg-red-900 dark:file:text-red-300"
                                       required>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    فرمت‌های مجاز: JPG, PNG, WebP (حداکثر ۵ مگابایت)
                                </p>
                            </div>

                            <button type="submit"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                آپلود مجدد رسید
                            </button>
                        </form>
                    @endif

                    <!-- Important Notes -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                            نکات مهم:
                        </h4>
                        <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                            <li>• مبلغ پرداختی باید دقیقاً {{ number_format($userBid->amount) }} تومان باشد</li>
                            <li>• رسید پرداخت باید واضح و خوانا باشد</li>
                            <li>• پس از تأیید پرداخت، منتظر انتقال وام باشید</li>
                            <li>• در صورت عدم پرداخت، مزایده لغو می‌شود</li>
                        </ul>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between">
                        <a href="{{ route('buyer.dashboard') }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors">
                            بازگشت به داشبورد
                        </a>

                        @if($paymentReceipt->status->value !== 'approved')
                            <button type="button"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors"
                                    onclick="if(confirm('آیا از انصراف از این مزایده اطمینان دارید؟')) window.location.href='{{ route('buyer.dashboard') }}'">
                                انصراف
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
