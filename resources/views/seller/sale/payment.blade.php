<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مرحله ۳: پرداخت کارمزد') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 1 ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            1
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 1 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">جزئیات وام</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 2 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 2 ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            2
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 2 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">قرارداد</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 3 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 3 ? ($sellerSale->current_step == 3 ? 'bg-blue-500' : 'bg-green-500') : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            3
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 3 ? ($sellerSale->current_step == 3 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-gray-100') : 'text-gray-500 dark:text-gray-400' }}">پرداخت کارمزد</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 4 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 4 ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            4
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 4 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">پذیرش پیشنهاد</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 5 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 5 ? ($sellerSale->current_step == 5 ? 'bg-blue-500' : 'bg-green-500') : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            5
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 5 ? ($sellerSale->current_step == 5 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-gray-100') : 'text-gray-500 dark:text-gray-400' }}">انتظار پرداخت</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 6 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 6 ? ($sellerSale->current_step == 6 ? 'bg-blue-500' : 'bg-green-500') : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            6
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 6 ? ($sellerSale->current_step == 6 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-gray-100') : 'text-gray-500 dark:text-gray-400' }}">انتقال وام</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 7 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 7 ? ($sellerSale->current_step == 7 ? 'bg-blue-500' : 'bg-green-500') : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            7
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 7 ? ($sellerSale->current_step == 7 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-gray-100') : 'text-gray-500 dark:text-gray-400' }}">انتظار تأیید</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 8 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 8 ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            8
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 8 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">تکمیل</span>
                    </div>
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        پرداخت کارمزد فروشنده
                    </h1>

                    <!-- Payment Amount -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                مبلغ قابل پرداخت
                            </h3>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format(3000000) }} تومان
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
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">1</span>
                                <p>مبلغ <strong>3,000,000 تومان</strong> را به کارت زیر واریز کنید:</p>
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
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">2</span>
                                <p>رسید پرداخت را در فرم زیر آپلود کنید</p>
                            </div>
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">3</span>
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
                            <form method="POST" action="{{ route('seller.sale.payment.receipt', $auction) }}"
                                  enctype="multipart/form-data" class="mb-6">
                                @csrf

                                <div class="mb-4">
                                    <label for="receipt_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        آپلود رسید پرداخت
                                    </label>
                                    <input id="receipt_image"
                                           name="receipt_image"
                                           type="file"
                                           accept="image/jpeg,image/png,image/webp"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300"
                                           required>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        فرمت‌های مجاز: JPG, PNG, WebP (حداکثر ۵ مگابایت)
                                    </p>
                                </div>

                                <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                    آپلود رسید
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
                                    پرداخت کارمزد تأیید شد. تاریخ تأیید: {{ $paymentReceipt->reviewed_at->format('Y/m/d H:i') }}
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('seller.sale.bid-acceptance', $auction) }}"
                           class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors block mb-6">
                            ادامه به مرحله بعد
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
                        <form method="POST" action="{{ route('seller.sale.payment.receipt', $auction) }}"
                              enctype="multipart/form-data" class="mb-6">
                            @csrf

                            <div class="mb-4">
                                <label for="receipt_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    آپلود مجدد رسید پرداخت
                                </label>
                                <input id="receipt_image"
                                       name="receipt_image"
                                       type="file"
                                       accept="image/jpeg,image/png,image/webp"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300"
                                       required>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    فرمت‌های مجاز: JPG, PNG, WebP (حداکثر ۵ مگابایت)
                                </p>
                            </div>

                            <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                آپلود مجدد رسید
                            </button>
                        </form>
                    @endif

                    <!-- Navigation -->
                    <div class="flex justify-between">
                        <a href="{{ route('seller.sale.contract', $auction) }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors">
                            مرحله قبل
                        </a>

                        <button type="button"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors"
                                onclick="if(confirm('آیا از انصراف از این فروش اطمینان دارید؟')) window.location.href='{{ route('seller.dashboard') }}'">
                            انصراف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
