<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('ویرایش مزایده') }}
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
                                   class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-300">
                                    مزایده‌ها
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400">ویرایش</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Edit Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.auctions.update', $auction) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                عنوان مزایده
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title', $auction->title) }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                توضیحات
                            </label>
                            <textarea id="description" name="description" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                      required>{{ old('description', $auction->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Loan Type and Principal Amount -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="loan_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    نوع وام
                                </label>
                                <select id="loan_type" name="loan_type"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        required>
                                    <option value="personal" {{ old('loan_type', $auction->loan_type) === 'personal' ? 'selected' : '' }}>
                                        مهربانی بانک ملی
                                    </option>
                                    <option value="commercial" {{ old('loan_type', $auction->loan_type) === 'commercial' ? 'selected' : '' }}>
                                        اعتبار ملی
                                    </option>
                                </select>
                                @error('loan_type')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="principal_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    مبلغ وام (تومان)
                                </label>
                                <input type="number" id="principal_amount" name="principal_amount"
                                       value="{{ old('principal_amount', $auction->principal_amount) }}"
                                       min="1000000" step="100000"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                       required>
                                @error('principal_amount')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Term and Interest Rate -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="term_months" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    مدت بازپرداخت (ماه)
                                </label>
                                <input type="number" id="term_months" name="term_months"
                                       value="{{ old('term_months', $auction->term_months) }}"
                                       min="1" max="60"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                       required>
                                @error('term_months')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="interest_rate_percent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    نرخ سود (%)
                                </label>
                                <input type="number" id="interest_rate_percent" name="interest_rate_percent"
                                       value="{{ old('interest_rate_percent', $auction->interest_rate_percent) }}"
                                       min="0" max="100" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                       required>
                                @error('interest_rate_percent')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Min Purchase Price and Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="min_purchase_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    حداقل قیمت خرید (تومان)
                                </label>
                                <input type="number" id="min_purchase_price" name="min_purchase_price"
                                       value="{{ old('min_purchase_price', $auction->min_purchase_price) }}"
                                       min="1000000" step="100000"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                       required>
                                @error('min_purchase_price')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    وضعیت
                                </label>
                                <select id="status" name="status"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        required>
                                    <option value="active" {{ old('status', $auction->status->value) === 'active' ? 'selected' : '' }}>
                                        فعال
                                    </option>
                                    <option value="locked" {{ old('status', $auction->status->value) === 'locked' ? 'selected' : '' }}>
                                        قفل شده
                                    </option>
                                    <option value="completed" {{ old('status', $auction->status->value) === 'completed' ? 'selected' : '' }}>
                                        تکمیل شده
                                    </option>
                                    <option value="cancelled" {{ old('status', $auction->status->value) === 'cancelled' ? 'selected' : '' }}>
                                        لغو شده
                                    </option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-4 space-x-reverse">
                            <a href="{{ route('admin.auctions.index') }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                انصراف
                            </a>
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                ذخیره تغییرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Auction Statistics -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        آمار مزایده
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <h4 class="font-medium text-blue-800 dark:text-blue-200">تعداد پیشنهادات</h4>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $auction->bids()->count() }}
                            </p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <h4 class="font-medium text-green-800 dark:text-green-200">بالاترین پیشنهاد</h4>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($auction->bids()->max('amount') ?? 0) }} تومان
                            </p>
                        </div>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                            <h4 class="font-medium text-yellow-800 dark:text-yellow-200">تاریخ ایجاد</h4>
                            <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                {{ $auction->created_at->format('Y/m/d H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


