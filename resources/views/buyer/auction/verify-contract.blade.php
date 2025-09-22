<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('تأیید کد قرارداد') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
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
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            2
                        </div>
                        <span class="mr-2 text-sm font-medium text-blue-600 dark:text-blue-400">تأیید قرارداد</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-medium">
                            3
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-500 dark:text-gray-400">پرداخت کارمزد</span>
                    </div>
                </div>
            </div>

            <!-- Verification Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6 text-center">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            تأیید کد قرارداد
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            کد تأیید ارسال شده به شماره <span class="font-medium">{{ auth()->user()->phone }}</span> را وارد کنید
                        </p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('buyer.auction.verify-contract.post', $auction) }}">
                        @csrf

                        <!-- OTP Code -->
                        <div class="mb-6">
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                کد تأیید (۶ رقم)
                            </label>
                            <input id="code"
                                   name="code"
                                   type="text"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   class="block w-full text-center text-2xl font-mono tracking-widest border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                   required
                                   autofocus
                                   autocomplete="off">
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div class="mb-6">
                            <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                تأیید و ادامه
                            </button>
                        </div>
                    </form>

                    <!-- Resend OTP -->
                    <div class="text-center mb-6">
                        <form method="POST" action="{{ route('buyer.auction.contract.otp', $auction) }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                ارسال مجدد کد
                            </button>
                        </form>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between">
                        <a href="{{ route('buyer.auction.contract', $auction) }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors">
                            بازگشت
                        </a>

                        <button type="button"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors"
                                onclick="if(confirm('آیا از انصراف از این مزایده اطمینان دارید؟')) window.location.href='{{ route('buyer.dashboard') }}'">
                            انصراف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus and format OTP input
        document.getElementById('code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');

            // Auto-submit when 6 digits are entered
            if (this.value.length === 6) {
                this.form.submit();
            }
        });

        // Handle paste event
        document.getElementById('code').addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text');
            const numericData = pastedData.replace(/[^0-9]/g, '').substring(0, 6);
            this.value = numericData;

            if (numericData.length === 6) {
                this.form.submit();
            }
        });
    </script>
</x-app-layout>

