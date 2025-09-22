@props(['title' => '', 'subtitle' => '', 'actions' => null, 'breadcrumbs' => []])

<div class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if(count($breadcrumbs) > 0)
            <div class="mb-4">
                <x-breadcrumb :items="$breadcrumbs" />
            </div>
        @endif

        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                @if($title)
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-gray-100 sm:text-3xl sm:truncate">
                        {{ $title }}
                    </h1>
                @endif

                @if($subtitle)
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            @if($actions)
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    {{ $actions }}
                </div>
            @endif
        </div>
    </div>
</div>

