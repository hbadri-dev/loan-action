<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مرحله ۶: انتظار انتقال وام') }}
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
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            6
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">پرداخت</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            6
                        </div>
                        <span class="mr-2 text-sm font-medium text-blue-600 dark:text-blue-400">انتظار انتقال وام</span>
                    </div>
                </div>
            </div>

            <!-- Loan Transfer Status -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        انتظار انتقال وام
                    </h1>

                    @if(!$loanTransfer)
                        <!-- Waiting for Seller -->
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-6">
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600 mr-3"></div>
                                <div>
                                    <h4 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">
                                        انتظار انتقال وام توسط فروشنده
                                    </h4>
                                    <p class="text-yellow-600 dark:text-yellow-400">
                                        فروشنده در حال آماده‌سازی انتقال وام است...
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-blue-800 dark:text-blue-200 text-sm">
                                    پس از انتقال وام توسط فروشنده، کد ملی شما برای تأیید درخواست خواهد شد.
                                </span>
                            </div>
                        </div>
                    @else
                        <!-- Loan Transfer Ready -->
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-6">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h4 class="text-lg font-semibold text-green-800 dark:text-green-200">
                                        فروشنده وام را انتقال داده است
                                    </h4>
                                    <p class="text-green-600 dark:text-green-400">
                                        لطفاً کد ملی خود را وارد کنید تا انتقال وام تأیید شود.
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($loanTransfer->buyer_confirmed_at)
                            <!-- Already Confirmed -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-blue-800 dark:text-blue-200 font-medium">
                                        انتقال وام تأیید شد. تاریخ تأیید: {{ $loanTransfer->buyer_confirmed_at->format('Y/m/d H:i') }}
                                    </span>
                                </div>
                            </div>

                            @if($loanTransfer->admin_confirmed_at)
                                <a href="{{ route('buyer.auction.complete', $auction) }}"
                                   class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors block mb-6">
                                    ادامه به مرحله نهایی
                                </a>
                            @else
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-yellow-800 dark:text-yellow-200 font-medium">
                                            منتظر تأیید نهایی توسط مدیر هستیم...
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- Confirmation Form -->
                            <form method="POST" action="{{ route('buyer.auction.loan-transfer.confirm', $auction) }}">
                                @csrf

                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                        تأیید انتقال وام
                                    </h3>

                                    <div class="mb-4">
                                        <label for="national_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            کد ملی (۱۰ رقم)
                                        </label>
                                        <input id="national_id"
                                               name="national_id"
                                               type="text"
                                               maxlength="10"
                                               pattern="[0-9]{10}"
                                               class="block w-full text-center text-lg font-mono tracking-widest border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                               required
                                               autofocus
                                               autocomplete="off">
                                        <x-input-error :messages="$errors->get('national_id')" class="mt-2" />
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            کد ملی که فروشنده در هنگام انتقال وام وارد کرده است.
                                        </p>
                                    </div>

                                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                        <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                            اطلاعات انتقال وام:
                                        </h4>
                                        <div class="text-sm text-blue-600 dark:text-blue-400 space-y-1">
                                            <p>• کد ملی وارد شده: <span class="font-mono">{{ $loanTransfer->national_id_of_buyer }}</span></p>
                                            <p>• تاریخ انتقال: {{ $loanTransfer->created_at->format('Y/m/d H:i') }}</p>
                                            @if($loanTransfer->transfer_receipt_path)
                                                <p>• رسید انتقال: <a href="{{ Storage::url($loanTransfer->transfer_receipt_path) }}" target="_blank" class="underline">مشاهده رسید</a></p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <button type="submit"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors mb-6">
                                    تأیید انتقال وام
                                </button>
                            </form>
                        @endif
                    @endif

                    <!-- Important Notes -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                            نکات مهم:
                        </h4>
                        <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                            <li>• کد ملی باید دقیقاً مطابق با کد ملی وارد شده توسط فروشنده باشد</li>
                            <li>• پس از تأیید، وام به نام شما ثبت می‌شود</li>
                            <li>• این مرحله آخرین مرحله فرآیند است</li>
                            <li>• در صورت خطا در کد ملی، با پشتیبانی تماس بگیرید</li>
                        </ul>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between">
                        <a href="{{ route('buyer.dashboard') }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors">
                            بازگشت به داشبورد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh page if no loan transfer yet
        @if(!$loanTransfer)
            setTimeout(function() {
                window.location.reload();
            }, 10000); // Refresh every 10 seconds
        @endif

        // Format national ID input
        document.getElementById('national_id')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</x-app-layout>
