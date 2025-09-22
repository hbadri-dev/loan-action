<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مرحله ۴: ثبت پیشنهاد قیمت') }}
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
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            4
                        </div>
                        <span class="mr-2 text-sm font-medium text-blue-600 dark:text-blue-400">ثبت پیشنهاد</span>
                    </div>
                </div>
            </div>

            <!-- Bid Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        ثبت پیشنهاد قیمت
                    </h1>

                    <!-- Auction Summary -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            خلاصه مزایده
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">عنوان:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $auction->title }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">مبلغ وام:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($auction->principal_amount) }} تومان</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">نرخ سود:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $auction->interest_rate_percent }}%</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">مدت:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $auction->term_months }} ماه</p>
                            </div>
                        </div>

                        @if($existingBid)
                            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">پیشنهاد قبلی شما:</h4>
                                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($existingBid->amount) }} تومان
                                </p>
                                <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                                    وضعیت: {{ $existingBid->status->label() }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Bid Limits -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <h4 class="font-semibold text-red-800 dark:text-red-200 mb-2">حداقل قیمت</h4>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                                {{ number_format($auction->min_purchase_price) }} تومان
                            </p>
                        </div>

                        @if($highestBid)
                            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                                <h4 class="font-semibold text-orange-800 dark:text-orange-200 mb-2">بالاترین پیشنهاد</h4>
                                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                    {{ number_format($highestBid->amount) }} تومان
                                </p>
                                <p class="text-sm text-orange-600 dark:text-orange-400 mt-1">
                                    توسط {{ $highestBid->buyer->name }}
                                </p>
                            </div>
                        @else
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                <h4 class="font-semibold text-green-800 dark:text-green-200 mb-2">اولین پیشنهاد</h4>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                    شما اولین پیشنهاد دهنده خواهید بود
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Display Errors -->
                    @if ($errors->any())
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                            <h4 class="font-semibold text-red-800 dark:text-red-200 mb-2">خطاها:</h4>
                            <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Display Success Message -->
                    @if (session('success'))
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                            <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                        </div>
                    @endif

                    <!-- Bid Form -->
                    <form method="POST" action="{{ route('buyer.auction.bid.post', $auction) }}">
                        @csrf

                        <div class="mb-6">
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                مبلغ پیشنهادی (تومان)
                            </label>
                            <input id="amount"
                                   name="amount"
                                   type="text"
                                   @if($existingBid) value="{{ number_format($existingBid->amount) }}" @endif
                                   placeholder="مبلغ پیشنهادی را وارد کنید (مثال: 1000000)"
                                   pattern="[0-9,]+"
                                   minlength="1"
                                   class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-lg font-medium"
                                   required
                                   autofocus>
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />

                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                @if($highestBid)
                                    <p>حداقل مبلغ: {{ number_format($auction->min_purchase_price) }} تومان</p>
                                    <p>بیشتر از بالاترین پیشنهاد: {{ number_format($highestBid->amount) }} تومان</p>
                                @else
                                    <p>حداقل مبلغ: {{ number_format($auction->min_purchase_price) }} تومان</p>
                                @endif
                            </div>
                        </div>

                        <!-- Bid Preview -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">پیش‌نمایش پیشنهاد</h4>
                            <div class="text-sm text-blue-600 dark:text-blue-400">
                                <p id="preview-amount">مبلغ پیشنهادی: <span class="font-medium">-</span></p>
                                <p id="preview-difference">تفاوت با بالاترین: <span class="font-medium">-</span></p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mb-6">
                            <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                @if($existingBid)
                                    بروزرسانی پیشنهاد
                                @else
                                    ثبت پیشنهاد
                                @endif
                            </button>
                        </div>
                    </form>

                    <!-- Important Notes -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                            نکات مهم:
                        </h4>
                        <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                            <li>• پیشنهاد شما پس از ثبت قابل تغییر است</li>
                            <li>• در صورت پذیرش پیشنهاد، موظف به پرداخت مبلغ هستید</li>
                            <li>• فروشنده صرفاً می‌تواند منتظر پیشنهادهای بالاتر باشد</li>
                            <li>• در صورت برنده نشدن در مزایده، مبلغ کارمزد عودت داده می‌شود</li>
                        </ul>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between">
                        <a href="{{ route('buyer.auction.payment', $auction) }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors">
                            مرحله قبل
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
        // Bid preview functionality
        const amountInput = document.getElementById('amount');
        const previewAmount = document.getElementById('preview-amount');
        const previewDifference = document.getElementById('preview-difference');

        const minAmount = {{ $auction->min_purchase_price }};
        const highestAmount = {{ $highestBid ? $highestBid->amount : 0 }};

        amountInput.addEventListener('input', function() {
            // Only allow numbers and commas
            let value = this.value.replace(/[^0-9,]/g, '');

            // Remove extra commas and format properly
            value = value.replace(/,+/g, ',');
            if (value.startsWith(',')) value = value.substring(1);

            this.value = value;

            const rawValue = value.replace(/,/g, ''); // Remove commas for calculation
            const amount = parseInt(rawValue) || 0;

            if (amount > 0) {
                previewAmount.innerHTML = `مبلغ پیشنهادی: <span class="font-medium">${amount.toLocaleString()} تومان</span>`;

                if (highestAmount > 0) {
                    const difference = amount - highestAmount;
                    previewDifference.innerHTML = `تفاوت با بالاترین: <span class="font-medium ${difference > 0 ? 'text-green-600' : 'text-red-600'}">${difference.toLocaleString()} تومان</span>`;
                } else {
                    const excess = amount - minAmount;
                    previewDifference.innerHTML = `اولین پیشنهاد: <span class="font-medium text-green-600">+${excess.toLocaleString()} تومان</span>`;
                }
            } else {
                previewAmount.innerHTML = 'مبلغ پیشنهادی: <span class="font-medium">-</span>';
                previewDifference.innerHTML = 'تفاوت با بالاترین: <span class="font-medium">-</span>';
            }
        });

        // Format input with commas on blur
        amountInput.addEventListener('blur', function() {
            const rawValue = this.value.replace(/,/g, '');
            if (rawValue && !isNaN(rawValue) && parseInt(rawValue) > 0) {
                this.value = parseInt(rawValue).toLocaleString();
            }
        });

        // Allow typing without commas on focus
        amountInput.addEventListener('focus', function() {
            this.value = this.value.replace(/,/g, '');
        });

        // Clean value before form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            let rawValue = amountInput.value;

            // Remove all non-numeric characters except digits
            rawValue = rawValue.replace(/[^0-9]/g, '');

            // Validate that we have a valid number
            if (!rawValue || rawValue === '' || parseInt(rawValue) <= 0) {
                e.preventDefault();
                amountInput.focus();
                amountInput.setCustomValidity('لطفاً مبلغ معتبری وارد کنید.');
                amountInput.reportValidity();
                return false;
            }

            const amount = parseInt(rawValue);

            // Check minimum amount
            if (amount < minAmount) {
                e.preventDefault();
                amountInput.focus();
                amountInput.setCustomValidity(`مبلغ باید حداقل ${minAmount.toLocaleString()} تومان باشد.`);
                amountInput.reportValidity();
                return false;
            }

            // Clear any custom validity message
            amountInput.setCustomValidity('');

            // Set the clean numeric value (without commas)
            amountInput.value = amount;

            // Log for debugging
            console.log('Submitting amount:', amountInput.value, 'Type:', typeof amountInput.value);
        });
    </script>
</x-app-layout>
