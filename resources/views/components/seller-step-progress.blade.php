<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
        پیشرفت فرآیند فروش
    </h3>

    <div class="space-y-4">
        @foreach($steps as $step)
            <div class="flex items-start space-x-4">
                <!-- Step Icon -->
                <div class="flex-shrink-0">
                    @if($step['status'] === 'completed')
                        <!-- Completed Step - Green Check -->
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    @elseif($step['status'] === 'current')
                        <!-- Current Step - Blue Circle -->
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-bold">{{ $step['number'] }}</span>
                        </div>
                    @else
                        <!-- Pending Step - Gray Circle -->
                        <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                            <span class="text-gray-600 dark:text-gray-300 text-sm font-bold">{{ $step['number'] }}</span>
                        </div>
                    @endif
                </div>

                <!-- Step Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium {{ $step['status'] === 'completed' ? 'text-green-600 dark:text-green-400' : ($step['status'] === 'current' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400') }}">
                            {{ $step['title'] }}
                        </h4>
                        @if($step['status'] === 'completed')
                            <span class="text-xs text-green-600 dark:text-green-400 font-medium bg-green-100 dark:bg-green-900 px-2 py-1 rounded-full">تکمیل شده</span>
                        @elseif($step['status'] === 'current')
                            <span class="text-xs text-blue-600 dark:text-blue-400 font-medium bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded-full">مرحله فعلی</span>
                        @else
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">در انتظار</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $step['description'] }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>
