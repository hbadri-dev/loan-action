@section('title', 'ورود / ثبت نام')
<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-extrabold tracking-tight text-gray-900">ورود / ثبت‌نام</h1>
        <p class="mt-1 text-sm text-gray-600">برای ادامه شماره تلفن و نوع حساب را مشخص کنید</p>
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

    <form method="POST" action="{{ route('unified.otp.request') }}" class="space-y-6">
        @csrf

        <!-- Phone Number -->
        <div>
            <x-input-label for="phone" :value="__('شماره تلفن')" />
            <x-text-input id="phone" class="block mt-1 w-full text-left ltr" type="text" name="phone"
                          :value="old('phone')" required autofocus inputmode="numeric"
                          placeholder="09123456789" pattern="09[0-9]{9}" />
            <p class="mt-1 text-xs text-gray-500">مثال: 09123456789</p>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Role Selection (Professional segmented control) -->
        <fieldset x-data="{ role: '{{ old('role', 'buyer') }}' }" class="space-y-3">
            <legend class="text-sm font-medium text-gray-700">انتخاب نوع حساب</legend>
            <input type="hidden" name="role" x-model="role" required>
            <div class="inline-flex w-full rounded-xl shadow-sm overflow-hidden ring-1 ring-gray-200 bg-white">
                <!-- Buyer button -->
                <button type="button"
                        :class="role === 'buyer' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                        class="px-4 py-2 text-sm font-medium flex items-center justify-center gap-2 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 flex-1"
                        @click="role = 'buyer'"
                        :aria-pressed="(role === 'buyer').toString()"
                        aria-label="خریدار">
                    <span class="inline-flex items-center justify-center w-5 h-5">
                        <!-- Active check icon -->
                        <svg x-show="role === 'buyer'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-white">
                            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm4.28 7.72a.75.75 0 0 0-1.06-1.06l-4.47 4.47-1.72-1.72a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.06 0l5-5Z" clip-rule="evenodd" />
                        </svg>
                        <!-- Inactive dot -->
                        <span x-show="role !== 'buyer'" class="block w-2.5 h-2.5 rounded-full border border-gray-300"></span>
                    </span>
                    <span>خریدار</span>
                </button>
                <!-- Seller button -->
                <button type="button"
                        :class="role === 'seller' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                        class="px-4 py-2 text-sm font-medium flex items-center justify-center gap-2 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 border-s border-gray-200 flex-1"
                        @click="role = 'seller'"
                        :aria-pressed="(role === 'seller').toString()"
                        aria-label="فروشنده">
                    <span class="inline-flex items-center justify-center w-5 h-5">
                        <!-- Active check icon -->
                        <svg x-show="role === 'seller'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-white">
                            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm4.28 7.72a.75.75 0 0 0-1.06-1.06l-4.47 4.47-1.72-1.72a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.06 0l5-5Z" clip-rule="evenodd" />
                        </svg>
                        <!-- Inactive dot -->
                        <span x-show="role !== 'seller'" class="block w-2.5 h-2.5 rounded-full border border-gray-300"></span>
                    </span>
                    <span>فروشنده</span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-1" />
        </fieldset>

        <div class="pt-2 text-center">
            <p class="text-sm text-gray-600">
                با وارد کردن شماره تلفن، شما موافقت خود را با قوانین و مقررات اعلام می‌دارید
            </p>
        </div>
        <div>
            <x-primary-button class="w-full justify-center h-12 text-lg">
                {{ __('درخواست کد تأیید') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        // Normalize Persian/Arabic-Indic digits to English for phone input
        (function () {
            var map = {
                '۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9',
                '٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'
            };
            function normalizeDigits(value){
                return value.replace(/[۰-۹٠-٩]/g, function(d){ return map[d] || d; });
            }
            var phone = document.getElementById('phone');
            if (phone) {
                phone.addEventListener('input', function(){
                    var cur = this.selectionStart;
                    this.value = normalizeDigits(this.value).replace(/[^0-9]/g, '');
                    this.setSelectionRange(cur, cur);
                });
            }
        })();
    </script>


</x-guest-layout>
