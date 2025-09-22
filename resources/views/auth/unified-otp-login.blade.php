<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">ورود / ثبت نام در وام ساز</h2>
        <p class="text-gray-600">شماره تلفن و نقش خود را انتخاب کنید</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('unified.otp.request') }}">
        @csrf

        <!-- Phone Number -->
        <div class="mb-4">
            <x-input-label for="phone" :value="__('شماره تلفن')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone"
                          :value="old('phone')" required autofocus
                          placeholder="09123456789" pattern="09[0-9]{9}" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mb-4">
            <x-input-label for="role" :value="__('نقش خود را انتخاب کنید')" />
            <div class="mt-2 space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="role" value="buyer" class="form-radio text-green-600"
                           {{ old('role') == 'buyer' ? 'checked' : '' }} required>
                    <span class="mr-2 text-sm text-gray-700">خریدار وام</span>
                    <span class="text-xs text-gray-500">(خرید وام از مزایده‌ها)</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="role" value="seller" class="form-radio text-blue-600"
                           {{ old('role') == 'seller' ? 'checked' : '' }} required>
                    <span class="mr-2 text-sm text-gray-700">فروشنده وام</span>
                    <span class="text-xs text-gray-500">(فروش وام در مزایده‌ها)</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('درخواست کد تأیید') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 mb-3">
            با وارد کردن شماره تلفن، شما موافقت خود را با قوانین و مقررات اعلام می‌دارید
        </p>
        <a href="{{ route('admin.login') }}"
           class="text-sm text-gray-600 hover:text-gray-500 underline">
            ورود مدیر
        </a>
    </div>
</x-guest-layout>
