<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">ورود به وام ساز</h2>
        <p class="text-gray-600">با شماره تلفن و کد تأیید وارد شوید</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('otp.request.web') }}">
        @csrf

        <!-- Phone Number -->
        <div>
            <x-input-label for="phone" :value="__('شماره تلفن')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone"
                          :value="old('phone')" required autofocus
                          placeholder="09123456789" pattern="09[0-9]{9}" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('درخواست کد تأیید') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 mb-3">
            حساب کاربری ندارید؟
        </p>
        <div class="space-y-2">
            <a href="{{ route('register.buyer') }}"
               class="block w-full py-2 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                ثبت‌نام خریدار
            </a>
            <a href="{{ route('register.seller') }}"
               class="block w-full py-2 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                ثبت‌نام فروشنده
            </a>
        </div>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('admin.login') }}"
           class="text-sm text-gray-600 hover:text-gray-500 underline">
            ورود مدیر
        </a>
    </div>
</x-guest-layout>
