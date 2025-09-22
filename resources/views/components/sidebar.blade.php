@props(['items' => [], 'activeItem' => ''])

<div class="bg-white dark:bg-gray-800 shadow-sm border-l border-gray-200 dark:border-gray-700 h-full">
    <nav class="mt-5 px-2">
        <div class="space-y-1">
            @foreach($items as $item)
                @if(isset($item['children']))
                    <!-- Section Header -->
                    <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ $item['title'] }}
                    </div>

                    <!-- Section Items -->
                    @foreach($item['children'] as $child)
                        <a href="{{ $child['url'] }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ $activeItem === $child['key'] ? 'bg-indigo-100 text-indigo-900 dark:bg-indigo-900 dark:text-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-gray-100' }}">
                            @if(isset($child['icon']))
                                <span class="ml-3 flex-shrink-0 h-6 w-6 {{ $activeItem === $child['key'] ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}">
                                    {!! $child['icon'] !!}
                                </span>
                            @endif
                            {{ $child['title'] }}
                        </a>
                    @endforeach
                @else
                    <!-- Single Item -->
                    <a href="{{ $item['url'] }}"
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ $activeItem === $item['key'] ? 'bg-indigo-100 text-indigo-900 dark:bg-indigo-900 dark:text-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-gray-100' }}">
                        @if(isset($item['icon']))
                            <span class="ml-3 flex-shrink-0 h-6 w-6 {{ $activeItem === $item['key'] ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}">
                                {!! $item['icon'] !!}
                            </span>
                        @endif
                        {{ $item['title'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </nav>
</div>

