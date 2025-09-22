@props(['auction', 'currentHighest'])

<div class="bg-white rounded-lg shadow p-6" dir="rtl">
    <h3 class="text-lg font-medium text-gray-900 mb-4">ثبت پیشنهاد قیمت</h3>

    @if($auction->isLocked())
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-gray-400 ml-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-gray-600">این مزایده قفل شده است و امکان ثبت پیشنهاد جدید وجود ندارد.</p>
            </div>
        </div>
    @else
        <form method="POST" action="{{ route('buyer.bid.store', $auction) }}" class="space-y-4">
            @csrf

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    مبلغ پیشنهادی (تومان)
                </label>

                @if($currentHighest)
                    <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <strong>بالاترین پیشنهاد فعلی:</strong>
                            {{ number_format($currentHighest->amount) }} تومان
                        </p>
                    </div>
                @endif

                <div class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <strong>حداقل قیمت خرید:</strong>
                        {{ number_format($auction->min_purchase_price) }} تومان
                    </p>
                </div>

                <input
                    type="number"
                    id="amount"
                    name="amount"
                    value="{{ old('amount') }}"
                    min="{{ $auction->min_purchase_price }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="مبلغ پیشنهادی خود را وارد کنید"
                    required
                >

                @error('amount')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3 space-x-reverse">
                <a
                    href="{{ route('buyer.auction.payment', $auction) }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition duration-200"
                >
                    بازگشت
                </a>

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200"
                >
                    ثبت پیشنهاد
                </button>
            </div>
        </form>
    @endif
</div>

