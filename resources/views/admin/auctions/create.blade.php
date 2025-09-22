<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('ایجاد مزایده جدید') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.dashboard') }}"
                               class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                پنل مدیریت
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('admin.auctions.index') }}"
                                   class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white">مزایدات</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400">ایجاد جدید</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Create Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-6 text-gray-900 dark:text-gray-100">
                        اطلاعات مزایده
                    </h3>

                    <form method="POST" action="{{ route('admin.auctions.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title -->
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    عنوان مزایده *
                                </label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}"
                                       class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       required>
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    توضیحات *
                                </label>
                                <textarea id="description" name="description" rows="4"
                                          class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                          required>{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <!-- Loan Type -->
                            <div>
                                <label for="loan_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    نوع وام *
                                </label>
                                <select id="loan_type" name="loan_type"
                                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required>
                                    <option value="">انتخاب کنید</option>
                                    <option value="personal" {{ old('loan_type') === 'personal' ? 'selected' : '' }}>مهربانی بانک ملی</option>
                                    <option value="commercial" {{ old('loan_type') === 'commercial' ? 'selected' : '' }}>اعتبار ملی</option>
                                </select>
                                <x-input-error :messages="$errors->get('loan_type')" class="mt-2" />
                            </div>

                            <!-- Principal Amount -->
                            <div>
                                <label for="principal_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    مبلغ وام (تومان) *
                                </label>
                                <input type="number" id="principal_amount" name="principal_amount" value="{{ old('principal_amount') }}"
                                       min="1000000" step="100000"
                                       class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       required>
                                <x-input-error :messages="$errors->get('principal_amount')" class="mt-2" />
                            </div>

                            <!-- Term Months -->
                            <div>
                                <label for="term_months" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    مدت بازپرداخت (ماه) *
                                </label>
                                <input type="number" id="term_months" name="term_months" value="{{ old('term_months') }}"
                                       min="1" max="60"
                                       class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       required>
                                <x-input-error :messages="$errors->get('term_months')" class="mt-2" />
                            </div>

                            <!-- Interest Rate -->
                            <div>
                                <label for="interest_rate_percent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    نرخ سود (درصد) *
                                </label>
                                <input type="number" id="interest_rate_percent" name="interest_rate_percent" value="{{ old('interest_rate_percent') }}"
                                       min="0" max="100" step="0.01"
                                       class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       required>
                                <x-input-error :messages="$errors->get('interest_rate_percent')" class="mt-2" />
                            </div>

                            <!-- Min Purchase Price -->
                            <div class="md:col-span-2">
                                <label for="min_purchase_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    حداقل قیمت خرید (تومان) *
                                </label>
                                <input type="number" id="min_purchase_price" name="min_purchase_price" value="{{ old('min_purchase_price') }}"
                                       min="1000000" step="100000"
                                       class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                       required>
                                <x-input-error :messages="$errors->get('min_purchase_price')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4 mt-8">
                            <a href="{{ route('admin.auctions.index') }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                انصراف
                            </a>
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                ایجاد مزایده
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Format number inputs
        document.getElementById('principal_amount').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        document.getElementById('min_purchase_price').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</x-app-layout>

