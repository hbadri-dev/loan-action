@section('title', 'تأیید شماره تلفن')
<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">تأیید شماره تلفن</h2>
        <p class="text-gray-600">کد تأیید ارسال شده به شماره <strong>{{ $phone }}</strong> را وارد کنید</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('otp.verify.web') }}">
        @csrf

        <input type="hidden" name="phone" value="{{ $phone }}">

        <!-- OTP Code -->
        <div>
            <x-input-label for="code" :value="__('کد تأیید')" />
            <x-text-input id="code" class="block mt-1 w-full text-center text-lg tracking-widest font-mono"
                          type="text" name="code" required autofocus
                          placeholder="123456" maxlength="6" pattern="[0-9]{6}" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('تأیید و ورود') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('otp.login') }}"
           class="text-sm text-blue-600 hover:text-blue-500 underline">
            تغییر شماره تلفن
        </a>
    </div>

    <div class="mt-4 text-center">
        <p class="text-xs text-gray-500">
            کد تأیید دریافت نکردید؟
            <a href="{{ route('otp.request.web') }}" class="underline">ارسال مجدد</a>
        </p>
    </div>

    <script>
        // Auto-focus and format OTP input
        document.getElementById('code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');

            // Auto submit when 6 digits are entered
            if (this.value.length === 6) {
                this.form.submit();
            }
        });

        // Auto focus on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('code').focus();
        });
    </script>
</x-guest-layout>
