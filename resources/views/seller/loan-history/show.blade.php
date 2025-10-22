<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('جزئیات وام - ' . $auction->title) }}
        </h2>
    </x-slot>
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $auction->title }}</h1>
                <p class="text-gray-600">جزئیات کامل وام و وضعیت معامله</p>
            </div>
            <a href="{{ route('seller.loan-history.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                بازگشت به لیست
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Auction Details -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">اطلاعات مزایده</h2>
            <dl class="grid grid-cols-1 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">عنوان</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auction->title }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">توضیحات</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auction->description }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">مبلغ وام</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ number_format($auction->principal_amount) }} تومان</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">نرخ بهره</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auction->interest_rate_percent }}%</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">مدت وام</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $auction->term_months }} ماه</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">وضعیت مزایده</dt>
                    <dd class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($auction->status->value === 'active') bg-green-100 text-green-800
                            @elseif($auction->status->value === 'locked') bg-yellow-100 text-yellow-800
                            @elseif($auction->status->value === 'completed') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $auction->status->label() }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Sale Details -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">وضعیت فروش</h2>
            @if($sellerSale)
                <dl class="grid grid-cols-1 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">وضعیت فروش</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($sellerSale->status->value === 'completed') bg-green-100 text-green-800
                                @elseif($sellerSale->status->value === 'cancelled') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $sellerSale->status->label() }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">مرحله فعلی</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sellerSale->getDisplayStep() }} از 7</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">تاریخ ایجاد</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sellerSale->created_at->format('Y/m/d H:i') }}</dd>
                    </div>
                </dl>
            @else
                <p class="text-gray-500">فروشی ثبت نشده است</p>
            @endif
        </div>
    </div>

    <!-- Accepted Bid Details -->
    @if($acceptedBid)
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">پیشنهاد پذیرفته شده</h2>
            <dl class="grid grid-cols-1 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">وضعیت</dt>
                    <dd class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            پیشنهاد پذیرفته شده
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">مبلغ پیشنهادی</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ number_format($acceptedBid->amount) }} تومان</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">تاریخ پیشنهاد</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $acceptedBid->created_at->format('Y/m/d H:i') }}</dd>
                </div>
            </dl>
        </div>
    @endif

    <!-- Payments Section -->
    @if($payments->count() > 0)
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">تاریخچه پرداخت‌ها</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع پرداخت</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مبلغ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاریخ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payments as $payment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payment->type_label }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payment->formatted_amount }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($payment->status->value === 'completed') bg-green-100 text-green-800
                                        @elseif($payment->status->value === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($payment->status->value === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $payment->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->created_at->format('Y/m/d H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Loan Transfer Section -->
    @if($loanTransfer)
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">انتقال وام</h2>
            <dl class="grid grid-cols-1 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">وضعیت انتقال</dt>
                    <dd class="mt-1">
                        @if($loanTransfer->isFullyConfirmed())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                تکمیل شده
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                در جریان
                            </span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">تأیید خریدار</dt>
                    <dd class="mt-1">
                        @if($loanTransfer->isBuyerConfirmed())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                تأیید شده
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                در انتظار تأیید
                            </span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">تأیید ادمین</dt>
                    <dd class="mt-1">
                        @if($loanTransfer->isAdminConfirmed())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                تأیید شده
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                در انتظار تأیید
                            </span>
                        @endif
                    </dd>
                </div>
                @if($loanTransfer->buyer_confirmed_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">تاریخ تأیید خریدار</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $loanTransfer->buyer_confirmed_at->format('Y/m/d H:i') }}</dd>
                    </div>
                @endif
                @if($loanTransfer->admin_confirmed_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">تاریخ تأیید ادمین</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $loanTransfer->admin_confirmed_at->format('Y/m/d H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    @endif
</div>
</x-app-layout>
