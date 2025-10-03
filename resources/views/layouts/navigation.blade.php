<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center ml-4">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800 dark:text-gray-200">
                        وام یار
                    </a>
                </div>

                <!-- Navigation Links -->
                @auth
                    @php
                        $currentRole = session('current_role', 'buyer'); // default role
                    @endphp
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        @if(auth()->user()->hasRole('admin'))
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                                {{ __('پنل مدیریت') }}
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->hasRole('buyer'))
                            <x-nav-link :href="route('buyer.dashboard')" :active="request()->routeIs('buyer.*') && $currentRole === 'buyer'">
                                {{ __('پنل خریدار') }}
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->hasRole('seller'))
                            <x-nav-link :href="route('seller.dashboard')" :active="request()->routeIs('seller.*') && $currentRole === 'seller'">
                                {{ __('پنل فروشنده') }}
                            </x-nav-link>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Settings Dropdown -->
            @auth
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    @if(auth()->user()->hasRole('buyer') || auth()->user()->hasRole('seller'))
                        <a href="https://t.me/sajbazar" target="_blank" rel="noopener noreferrer" class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 mr-4 text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9.036 15.216l-.396 5.58c.564 0 .807-.243 1.104-.54l2.652-2.52 5.496 4.02c1.008.564 1.716.264 1.98-.936l3.588-16.8.012-.012c.324-1.512-.54-2.1-1.524-1.74L1.812 9.288c-1.476.576-1.452 1.404-.252 1.776l5.448 1.704 12.648-7.98c.6-.396 1.152-.18.7.216"/>
                            </svg>
                            پشتیبانی تلگرام
                        </a>
                    @endif
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Role Switcher -->
                            @if(auth()->user()->hasRole('buyer') && auth()->user()->hasRole('seller'))
                                @php
                                    $currentRole = session('current_role', 'buyer');
                                @endphp
                                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">تعویض نقش:</div>
                                    <div class="space-y-1">
                                        @if($currentRole !== 'buyer')
                                            <a href="{{ route('switch-role', 'buyer') }}" class="block px-3 py-1 text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                                                خریدار
                                            </a>
                                        @endif
                                        @if($currentRole !== 'seller')
                                            <a href="{{ route('switch-role', 'seller') }}" class="block px-3 py-1 text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                                                فروشنده
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('خروج') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @else
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                        {{ __('ورود') }}
                    </a>
                    <a href="{{ route('register.buyer') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium mr-4">
                        {{ __('ثبت‌نام خریدار') }}
                    </a>
                    <a href="{{ route('register.seller') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium mr-4">
                        {{ __('ثبت‌نام فروشنده') }}
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    @auth
        @php
            $currentRole = session('current_role', 'buyer'); // default role
        @endphp
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                @if(auth()->user()->hasRole('admin'))
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                        {{ __('پنل مدیریت') }}
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->hasRole('buyer'))
                    <x-responsive-nav-link :href="route('buyer.dashboard')" :active="request()->routeIs('buyer.*') && $currentRole === 'buyer'">
                        {{ __('پنل خریدار') }}
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->hasRole('seller'))
                    <x-responsive-nav-link :href="route('seller.dashboard')" :active="request()->routeIs('seller.*') && $currentRole === 'seller'">
                        {{ __('پنل فروشنده') }}
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->hasRole('buyer') || auth()->user()->hasRole('seller'))
                    <a href="https://t.me/sajbazar" target="_blank" rel="noopener noreferrer" class="block px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                        پشتیبانی تلگرام
                    </a>
                @endif
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('خروج') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    @endauth
</nav>
