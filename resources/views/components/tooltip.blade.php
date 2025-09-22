@props(['position' => 'top', 'text' => ''])

<div class="relative inline-block group">
    {{ $slot }}

    @if($text)
        <div class="absolute z-10 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-200
            @if($position === 'top') bottom-full left-1/2 transform -translate-x-1/2 mb-2 @endif
            @if($position === 'bottom') top-full left-1/2 transform -translate-x-1/2 mt-2 @endif
            @if($position === 'left') right-full top-1/2 transform -translate-y-1/2 mr-2 @endif
            @if($position === 'right') left-full top-1/2 transform -translate-y-1/2 ml-2 @endif
        ">
            <div class="bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                {{ $text }}
                <div class="absolute w-2 h-2 bg-gray-900 transform rotate-45
                    @if($position === 'top') top-full left-1/2 -translate-x-1/2 -mt-1 @endif
                    @if($position === 'bottom') bottom-full left-1/2 -translate-x-1/2 -mb-1 @endif
                    @if($position === 'left') left-full top-1/2 -translate-y-1/2 -ml-1 @endif
                    @if($position === 'right') right-full top-1/2 -translate-y-1/2 -mr-1 @endif
                "></div>
            </div>
        </div>
    @endif
</div>

