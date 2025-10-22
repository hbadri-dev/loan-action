<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('جزئیات فیش پرداخت') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
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
                                <a href="{{ route('admin.payment-receipts.index') }}"
                                   class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white">فیش‌های پرداخت</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400">جزئیات</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Receipt Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                            اطلاعات فیش
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">نوع فیش:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($receipt->type->value === 'buyer_fee') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                    @elseif($receipt->type->value === 'seller_fee') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($receipt->type->value === 'buyer_purchase_amount') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300
                                    @elseif($receipt->type->value === 'loan_transfer') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300
                                    @elseif($receipt->type->value === 'loan_verification') bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300
                                    @endif">
                                    {{ $receipt->type->label() }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">مبلغ:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ number_format($receipt->amount) }} تومان
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">وضعیت:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($receipt->status->value === 'pending_review') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @elseif($receipt->status->value === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($receipt->status->value === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @endif">
                                    {{ $receipt->status->label() }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">تاریخ ارسال:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $receipt->created_at->format('Y/m/d H:i') }}
                                </span>
                            </div>

                            @if($receipt->reviewed_at)
                                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400">تاریخ بررسی:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $receipt->reviewed_at->format('Y/m/d H:i') }}
                                    </span>
                                </div>
                            @endif

                            @if($receipt->reviewer)
                                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400">بررسی‌کننده:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $receipt->reviewer->name }}
                                    </span>
                                </div>
                            @endif

                            @if($receipt->type->value === 'loan_transfer' && $receipt->iban)
                                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400">شماره شبا:</span>
                                    <div class="text-right">
                                        <span class="font-mono text-sm font-bold text-gray-900 dark:text-gray-100 bg-blue-50 dark:bg-blue-900/20 px-3 py-1 rounded">
                                            {{ $receipt->iban }}
                                        </span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">برای دریافت پرداخت</p>
                                    </div>
                                </div>
                            @endif

                            @if($receipt->type->value === 'loan_verification')
                                @if($receipt->full_name)
                                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-gray-600 dark:text-gray-400">نام و نام خانوادگی:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $receipt->full_name }}
                                        </span>
                                    </div>
                                @endif

                                @if($receipt->national_id)
                                    <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-gray-600 dark:text-gray-400">کد ملی:</span>
                                        <span class="font-mono text-sm font-bold text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 px-3 py-1 rounded">
                                            {{ $receipt->national_id }}
                                        </span>
                                    </div>
                                @endif
                            @endif

                            @if($receipt->reject_reason)
                                <div class="py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400 block mb-2">دلیل رد:</span>
                                    <p class="text-gray-900 dark:text-gray-100 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg">
                                        {{ $receipt->reject_reason }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                            اطلاعات کاربر
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">نام:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $receipt->user->name }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">شماره تماس:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $receipt->user->phone }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">نقش:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $receipt->user->roles->first()->name ?? 'نامشخص' }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">وضعیت تأیید:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($receipt->user->is_phone_verified) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @endif">
                                    {{ $receipt->user->is_phone_verified ? 'تأیید شده' : 'تأیید نشده' }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">تاریخ عضویت:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $receipt->user->created_at->format('Y/m/d') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Auction Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                            اطلاعات مزایده
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">عنوان:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $receipt->auction->title }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">وضعیت:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($receipt->auction->status->value === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($receipt->auction->status->value === 'locked') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @elseif($receipt->auction->status->value === 'completed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                    @endif">
                                    {{ $receipt->auction->status->label() }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">مبلغ وام:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ number_format($receipt->auction->principal_amount) }} تومان
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">ایجادکننده:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $receipt->auction->creator->name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Receipt Image -->
                @if($receipt->image_path)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                        <div class="p-6">
                            <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                                تصویر فیش
                            </h3>

                            <div class="text-center">
                                <img src="{{ Storage::url($receipt->image_path) }}"
                                     alt="Receipt Image"
                                     class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                @if($receipt->status->value === 'pending_review')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                        <div class="p-6">
                            <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                                اقدامات
                            </h3>

                            <div class="flex flex-col sm:flex-row gap-4">
                                <!-- Approve Form -->
                                <form method="POST" action="{{ route('admin.payment-receipts.approve', $receipt) }}" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                                            onclick="return confirm('آیا از تأیید این فیش اطمینان دارید؟')">
                                        تأیید فیش
                                    </button>
                                </form>

                                <!-- Reject Form -->
                                <form method="POST" action="{{ route('admin.payment-receipts.reject', $receipt) }}" class="flex-1">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="reject_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            دلیل رد
                                        </label>
                                        <textarea id="reject_reason" name="reject_reason" rows="3"
                                                  class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                                  required></textarea>
                                    </div>
                                    <button type="submit"
                                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                                            onclick="return confirm('آیا از رد این فیش اطمینان دارید؟')">
                                        رد فیش
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('admin.payment-receipts.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    بازگشت
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
