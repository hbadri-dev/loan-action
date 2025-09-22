<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            نمونه رابط کاربری فارسی
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Stepper Component Examples -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">نمونه کامپوننت مراحل (Stepper)</h3>

                        <!-- Horizontal Stepper -->
                        <div class="mb-6">
                            <h4 class="text-md font-medium mb-3">مراحل افقی (خریدار)</h4>
                            <x-stepper
                                :steps="[
                                    ['title' => 'جزئیات وام', 'description' => 'مشاهده اطلاعات وام'],
                                    ['title' => 'متن قرارداد', 'description' => 'تأیید متن قرارداد'],
                                    ['title' => 'پرداخت کارمزد', 'description' => 'واریز کارمزد ۳ میلیون تومان'],
                                    ['title' => 'ثبت پیشنهاد', 'description' => 'ثبت مبلغ پیشنهادی'],
                                    ['title' => 'انتظار فروشنده', 'description' => 'در انتظار تأیید فروشنده'],
                                    ['title' => 'پرداخت خرید', 'description' => 'واریز مبلغ خرید'],
                                    ['title' => 'انتقال وام', 'description' => 'در انتظار انتقال وام'],
                                    ['title' => 'اتمام', 'description' => 'پایان فرایند']
                                ]"
                                :current-step="3"
                                :completed-steps="[1, 2]"
                                variant="default"
                            />
                        </div>

                        <!-- Compact Stepper -->
                        <div class="mb-6">
                            <h4 class="text-md font-medium mb-3">مراحل فشرده (فروشنده)</h4>
                            <x-stepper
                                :steps="[
                                    ['title' => 'جزئیات وام', 'description' => 'مشاهده اطلاعات وام'],
                                    ['title' => 'قرارداد فروش', 'description' => 'تأیید قرارداد فروش'],
                                    ['title' => 'پرداخت کارمزد', 'description' => 'واریز کارمزد ۳ میلیون تومان'],
                                    ['title' => 'پذیرش پیشنهاد', 'description' => 'انتخاب خریدار'],
                                    ['title' => 'انتظار پرداخت', 'description' => 'در انتظار واریز خریدار'],
                                    ['title' => 'انتقال وام', 'description' => 'انجام انتقال وام'],
                                    ['title' => 'تأیید انتقال', 'description' => 'در انتظار تأیید نهایی'],
                                    ['title' => 'تکمیل فروش', 'description' => 'پایان فرایند فروش']
                                ]"
                                :current-step="4"
                                :completed-steps="[1, 2, 3]"
                                variant="compact"
                            />
                        </div>

                        <!-- Vertical Stepper -->
                        <div class="mb-6">
                            <h4 class="text-md font-medium mb-3">مراحل عمودی</h4>
                            <div class="max-w-md">
                                <x-stepper
                                    :steps="[
                                        ['title' => 'شروع فرایند', 'description' => 'شروع فرایند مزایده', 'status' => 'completed'],
                                        ['title' => 'ثبت پیشنهاد', 'description' => 'ثبت مبلغ پیشنهادی', 'status' => 'completed'],
                                        ['title' => 'انتظار تأیید', 'description' => 'در انتظار تأیید فروشنده', 'status' => 'current'],
                                        ['title' => 'پرداخت', 'description' => 'واریز مبلغ خرید', 'status' => 'pending'],
                                        ['title' => 'اتمام', 'description' => 'پایان فرایند', 'status' => 'pending']
                                    ]"
                                    :current-step="3"
                                    :completed-steps="[1, 2]"
                                    variant="vertical"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Amount Input Examples -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">نمونه فیلد مبلغ</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Amount Input -->
                            <div>
                                <h4 class="text-md font-medium mb-3">فیلد مبلغ پایه</h4>
                                <x-amount-input
                                    name="basic_amount"
                                    label="مبلغ پایه"
                                    placeholder="مبلغ را وارد کنید..."
                                    :required="true"
                                    :min="1000000"
                                    :step="100000"
                                    currency="تومان"
                                />
                            </div>

                            <!-- Bid Amount Input -->
                            <div>
                                <h4 class="text-md font-medium mb-3">مبلغ پیشنهاد</h4>
                                <x-amount-input
                                    name="bid_amount"
                                    label="مبلغ پیشنهاد"
                                    placeholder="مبلغ پیشنهادی خود را وارد کنید..."
                                    :required="true"
                                    :min="5000000"
                                    :max="100000000"
                                    :step="1000000"
                                    currency="تومان"
                                    :show-currency="true"
                                    :show-thousand-separator="true"
                                    :persian-numbers="true"
                                />
                            </div>

                            <!-- Fee Amount Input -->
                            <div>
                                <h4 class="text-md font-medium mb-3">مبلغ کارمزد</h4>
                                <x-amount-input
                                    name="fee_amount"
                                    label="کارمزد"
                                    placeholder="۳,۰۰۰,۰۰۰"
                                    :required="true"
                                    :min="3000000"
                                    :max="3000000"
                                    :step="1000"
                                    currency="تومان"
                                    :show-currency="true"
                                    :show-thousand-separator="true"
                                    :persian-numbers="true"
                                />
                            </div>

                            <!-- Loan Amount Input -->
                            <div>
                                <h4 class="text-md font-medium mb-3">مبلغ اصل وام</h4>
                                <x-amount-input
                                    name="loan_amount"
                                    label="مبلغ اصل وام"
                                    placeholder="مبلغ اصل وام را وارد کنید..."
                                    :required="true"
                                    :min="10000000"
                                    :max="500000000"
                                    :step="1000000"
                                    currency="تومان"
                                    :show-currency="true"
                                    :show-thousand-separator="true"
                                    :persian-numbers="true"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- File Upload Examples -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">نمونه آپلود فایل</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Receipt Upload -->
                            <div>
                                <h4 class="text-md font-medium mb-3">آپلود رسید پرداخت</h4>
                                <x-file-upload
                                    name="receipt_image"
                                    label="رسید پرداخت"
                                    accept="image/jpeg,image/jpg,image/png,image/webp"
                                    max-size="5MB"
                                    :required="true"
                                    :preview="true"
                                />
                            </div>

                            <!-- Transfer Receipt Upload -->
                            <div>
                                <h4 class="text-md font-medium mb-3">آپلود رسید انتقال</h4>
                                <x-file-upload
                                    name="transfer_receipt"
                                    label="رسید انتقال وام"
                                    accept="image/jpeg,image/jpg,image/png,image/webp"
                                    max-size="5MB"
                                    :required="true"
                                    :preview="true"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Persian Messages Examples -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">نمونه پیام‌های فارسی</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Success Messages -->
                            <div>
                                <h4 class="text-md font-medium mb-3">پیام‌های موفقیت</h4>
                                <div class="space-y-2">
                                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                                        <strong>موفقیت:</strong> {{ __('messages.success') }}
                                    </div>
                                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                                        <strong>ثبت پیشنهاد:</strong> {{ __('messages.bid.placed') }}
                                    </div>
                                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                                        <strong>تأیید رسید:</strong> {{ __('messages.payment.receipt_approved') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Error Messages -->
                            <div>
                                <h4 class="text-md font-medium mb-3">پیام‌های خطا</h4>
                                <div class="space-y-2">
                                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                        <strong>خطا:</strong> {{ __('messages.error') }}
                                    </div>
                                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                        <strong>مبلغ نامعتبر:</strong> {{ __('messages.bid.invalid_amount') }}
                                    </div>
                                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                        <strong>فایل بزرگ:</strong> {{ __('messages.file.file_too_large', ['max' => '5']) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Status Messages -->
                            <div>
                                <h4 class="text-md font-medium mb-3">وضعیت‌ها</h4>
                                <div class="space-y-2">
                                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                                        <strong>فعال:</strong> {{ __('messages.status.active') }}
                                    </div>
                                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                                        <strong>در انتظار:</strong> {{ __('messages.status.pending') }}
                                    </div>
                                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                                        <strong>تکمیل شده:</strong> {{ __('messages.status.completed') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Button Text -->
                            <div>
                                <h4 class="text-md font-medium mb-3">متن دکمه‌ها</h4>
                                <div class="space-y-2">
                                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        {{ __('messages.buttons.participate_auction') }}
                                    </button>
                                    <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        {{ __('messages.buttons.place_bid') }}
                                    </button>
                                    <button class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                        {{ __('messages.buttons.upload_receipt') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RTL Layout Examples -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">نمونه چیدمان راست‌چین</h3>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-4 space-x-reverse">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-bold">۱</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium">مرحله اول</h4>
                                        <p class="text-sm text-gray-600">جزئیات وام</p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">تکمیل شده</span>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-600">
                                        <span class="persian-numbers">۱۲,۵۰۰,۰۰۰</span> تومان
                                    </div>
                                    <div class="text-sm font-medium">
                                        مبلغ پیشنهادی
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Example -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">نمونه فرم کامل</h3>

                        <form class="space-y-6" dir="rtl">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-amount-input
                                    name="auction_amount"
                                    label="مبلغ مزایده"
                                    placeholder="مبلغ مزایده را وارد کنید..."
                                    :required="true"
                                    :min="10000000"
                                    :max="1000000000"
                                    :step="1000000"
                                    currency="تومان"
                                />

                                <x-amount-input
                                    name="minimum_bid"
                                    label="حداقل پیشنهاد"
                                    placeholder="حداقل مبلغ پیشنهادی..."
                                    :required="true"
                                    :min="1000000"
                                    :step="100000"
                                    currency="تومان"
                                />
                            </div>

                            <x-file-upload
                                name="auction_image"
                                label="تصویر مزایده"
                                accept="image/jpeg,image/jpg,image/png,image/webp"
                                max-size="5MB"
                                :required="false"
                                :preview="true"
                            />

                            <div class="flex items-center justify-end space-x-4 space-x-reverse">
                                <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    {{ __('messages.cancel') }}
                                </button>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    {{ __('messages.submit') }}
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

