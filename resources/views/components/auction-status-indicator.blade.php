@props(['auction'])

@php
    $statusConfig = [
        'active' => [
            'class' => 'bg-green-100 text-green-800 border-green-200',
            'icon' => 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z',
            'text' => 'فعال'
        ],
        'locked' => [
            'class' => 'bg-red-100 text-red-800 border-red-200',
            'icon' => 'M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z',
            'text' => 'قفل شده'
        ],
        'completed' => [
            'class' => 'bg-blue-100 text-blue-800 border-blue-200',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'text' => 'تکمیل شده'
        ],
        'paused' => [
            'class' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'icon' => 'M10 9v3m0 0v3m0-3h3m-3 0H7m13 0a9 9 0 11-18 0 9 9 0 0118 0z',
            'text' => 'متوقف شده'
        ]
    ];

    $status = $auction->status->value;
    $config = $statusConfig[$status] ?? $statusConfig['active'];
@endphp

<div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $config['class'] }}" dir="rtl">
    <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="{{ $config['icon'] }}" clip-rule="evenodd"></path>
    </svg>
    {{ $config['text'] }}

    @if($auction->is_locked && $auction->locked_at)
        <span class="mr-2 text-xs opacity-75">
            ({{ $auction->locked_at->format('Y/m/d H:i') }})
        </span>
    @endif
</div>

