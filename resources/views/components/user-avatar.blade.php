@props(['user' => null, 'size' => 'md', 'showName' => false, 'showRole' => false])

@php
$sizeClasses = match($size) {
    'sm' => 'w-8 h-8 text-sm',
    'lg' => 'w-16 h-16 text-lg',
    'xl' => 'w-20 h-20 text-xl',
    default => 'w-10 h-10 text-base',
};

$initials = '';
if ($user) {
    $name = $user->name ?? '';
    $parts = explode(' ', $name);
    if (count($parts) >= 2) {
        $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
    } else {
        $initials = strtoupper(substr($name, 0, 2));
    }
}
@endphp

<div class="flex items-center">
    <div class="relative">
        <div class="{{ $sizeClasses }} rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 font-medium">
            @if($user && $user->avatar)
                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="{{ $sizeClasses }} rounded-full object-cover">
            @else
                {{ $initials }}
            @endif
        </div>

        @if($user && $user->is_online)
            <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-green-400 ring-2 ring-white dark:ring-gray-800"></span>
        @endif
    </div>

    @if($showName || $showRole)
        <div class="mr-3">
            @if($showName && $user)
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $user->name }}
                </p>
            @endif

            @if($showRole && $user)
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    @if($user->hasRole('admin'))
                        مدیر
                    @elseif($user->hasRole('buyer'))
                        خریدار
                    @elseif($user->hasRole('seller'))
                        فروشنده
                    @endif
                </p>
            @endif
        </div>
    @endif
</div>

