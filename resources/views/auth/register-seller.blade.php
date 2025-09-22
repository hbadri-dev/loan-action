<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">ثبت نام فروشنده</h2>
        <p class="text-gray-600">برای فروش وام‌های خود در مزایده ثبت نام کنید</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('register.seller') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('نام و نام خانوادگی')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                          :value="old('name')" required autofocus placeholder="نام و نام خانوادگی خود را وارد کنید" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('شماره تلفن')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone"
                          :value="old('phone')" required
                          placeholder="09123456789" pattern="09[0-9]{9}" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('ثبت‌نام') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 mb-3">
            قبلاً ثبت‌نام کرده‌اید؟
        </p>
        <a href="{{ route('otp.login') }}"
           class="block w-full py-2 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-center">
            ورود
        </a>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('register.buyer') }}"
           class="text-sm text-gray-600 hover:text-gray-500 underline">
            ثبت نام خریدار
        </a>
    </div>
</x-guest-layout>
