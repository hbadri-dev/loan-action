@props(['title' => '', 'value' => '', 'change' => null, 'changeType' => 'neutral', 'icon' => null])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
    <div class="p-5">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                @if($icon)
                    <div class="w-8 h-8 {{ $changeType === 'positive' ? 'text-green-400' : ($changeType === 'negative' ? 'text-red-400' : 'text-gray-400') }}">
                        {!! $icon !!}
                    </div>
                @else
                    <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="mr-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                        {{ $title }}
                    </dt>
                    <dd class="flex items-baseline">
                        <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $value }}
                        </div>
                        @if($change !== null)
                            <div class="mr-2 flex items-baseline text-sm font-semibold {{ $changeType === 'positive' ? 'text-green-600' : ($changeType === 'negative' ? 'text-red-600' : 'text-gray-600') }}">
                                @if($changeType === 'positive')
                                    <svg class="self-center flex-shrink-0 h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">افزایش</span>
                                @elseif($changeType === 'negative')
                                    <svg class="self-center flex-shrink-0 h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">کاهش</span>
                                @endif
                                {{ $change }}
                            </div>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

