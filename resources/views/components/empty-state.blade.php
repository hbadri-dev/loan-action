@props(['title' => 'هیچ موردی یافت نشد', 'description' => '', 'icon' => null, 'action' => null])

<div class="text-center py-12">
    @if($icon)
        <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
            {!! $icon !!}
        </div>
    @else
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
    @endif

    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $title }}</h3>

    @if($description)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
    @endif

    @if($action)
        <div class="mt-6">
            {{ $action }}
        </div>
    @endif
</div>

