@props([
    'steps' => [],
    'currentStep' => 1,
    'completedSteps' => [],
    'showNumbers' => true,
    'variant' => 'default', // default, compact, vertical
    'class' => ''
])

@php
    $totalSteps = count($steps);
    $isVertical = $variant === 'vertical';
    $isCompact = $variant === 'compact';
@endphp

<div class="stepper-container {{ $class }}" dir="rtl">
    @if($variant === 'vertical')
        <!-- Vertical Stepper -->
        <div class="stepper-vertical space-y-6">
            @foreach($steps as $index => $step)
                @php
                    $stepNumber = $index + 1;
                    $isCompleted = in_array($stepNumber, $completedSteps);
                    $isCurrent = $stepNumber === $currentStep;
                    $isPending = $stepNumber > $currentStep;
                @endphp

                <div class="stepper-vertical-item flex items-start">
                    <!-- Step Icon/Number -->
                    <div class="flex-shrink-0 me-4">
                        <div class="stepper-icon-wrapper">
                            @if($isCompleted)
                                <!-- Completed Step -->
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @elseif($isCurrent)
                                <!-- Current Step -->
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    @if($showNumbers)
                                        <span class="text-white font-medium text-sm">{{ $stepNumber }}</span>
                                    @else
                                        <div class="w-3 h-3 bg-white rounded-full"></div>
                                    @endif
                                </div>
                            @else
                                <!-- Pending Step -->
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    @if($showNumbers)
                                        <span class="text-gray-600 font-medium text-sm">{{ $stepNumber }}</span>
                                    @else
                                        <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Connecting Line (except for last step) -->
                        @if($index < $totalSteps - 1)
                            <div class="stepper-line w-0.5 h-8 bg-gray-300 mx-auto mt-2"></div>
                        @endif
                    </div>

                    <!-- Step Content -->
                    <div class="flex-1 min-w-0">
                        <div class="stepper-content">
                            <h3 class="text-sm font-medium {{ $isCurrent ? 'text-blue-600' : ($isCompleted ? 'text-green-600' : 'text-gray-500') }}">
                                {{ $step['title'] ?? "مرحله {$stepNumber}" }}
                            </h3>
                            @if(isset($step['description']) && !$isCompact)
                                <p class="text-sm text-gray-600 mt-1">{{ $step['description'] }}</p>
                            @endif
                            @if(isset($step['status']) && !$isCompact)
                                <div class="mt-2">
                                    @if($step['status'] === 'completed')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 me-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            تکمیل شده
                                        </span>
                                    @elseif($step['status'] === 'current')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full me-1 animate-pulse"></div>
                                            در حال انجام
                                        </span>
                                    @elseif($step['status'] === 'pending')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            در انتظار
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Horizontal Stepper -->
        <div class="stepper-horizontal">
            <div class="flex items-center justify-between {{ $isCompact ? 'space-x-4 space-x-reverse' : 'space-x-8 space-x-reverse' }}">
                @foreach($steps as $index => $step)
                    @php
                        $stepNumber = $index + 1;
                        $isCompleted = in_array($stepNumber, $completedSteps);
                        $isCurrent = $stepNumber === $currentStep;
                        $isPending = $stepNumber > $currentStep;
                    @endphp

                    <div class="stepper-item flex flex-col items-center {{ $isCompact ? 'flex-1' : '' }}">
                        <!-- Step Icon/Number -->
                        <div class="stepper-icon-wrapper relative">
                            @if($isCompleted)
                                <!-- Completed Step -->
                                <div class="w-{{ $isCompact ? '8' : '10' }} h-{{ $isCompact ? '8' : '10' }} bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-{{ $isCompact ? '4' : '5' }} h-{{ $isCompact ? '4' : '5' }} text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @elseif($isCurrent)
                                <!-- Current Step -->
                                <div class="w-{{ $isCompact ? '8' : '10' }} h-{{ $isCompact ? '8' : '10' }} bg-blue-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-blue-100">
                                    @if($showNumbers)
                                        <span class="text-white font-bold text-{{ $isCompact ? 'sm' : 'base' }}">{{ $stepNumber }}</span>
                                    @else
                                        <div class="w-{{ $isCompact ? '3' : '4' }} h-{{ $isCompact ? '3' : '4' }} bg-white rounded-full"></div>
                                    @endif
                                </div>
                            @else
                                <!-- Pending Step -->
                                <div class="w-{{ $isCompact ? '8' : '10' }} h-{{ $isCompact ? '8' : '10' }} bg-gray-300 rounded-full flex items-center justify-center">
                                    @if($showNumbers)
                                        <span class="text-gray-600 font-medium text-{{ $isCompact ? 'sm' : 'base' }}">{{ $stepNumber }}</span>
                                    @else
                                        <div class="w-{{ $isCompact ? '3' : '4' }} h-{{ $isCompact ? '3' : '4' }} bg-gray-500 rounded-full"></div>
                                    @endif
                                </div>
                            @endif

                            <!-- Progress Ring for Current Step -->
                            @if($isCurrent)
                                <div class="absolute inset-0 w-{{ $isCompact ? '8' : '10' }} h-{{ $isCompact ? '8' : '10' }} rounded-full border-2 border-blue-200 animate-pulse"></div>
                            @endif
                        </div>

                        <!-- Step Content -->
                        @if(!$isCompact)
                            <div class="stepper-content mt-3 text-center max-w-24">
                                <h3 class="text-xs font-medium {{ $isCurrent ? 'text-blue-600' : ($isCompleted ? 'text-green-600' : 'text-gray-500') }}">
                                    {{ $step['title'] ?? "مرحله {$stepNumber}" }}
                                </h3>
                                @if(isset($step['description']))
                                    <p class="text-xs text-gray-600 mt-1 leading-tight">{{ $step['description'] }}</p>
                                @endif
                            </div>
                        @endif

                        <!-- Connecting Line (except for last step) -->
                        @if($index < $totalSteps - 1)
                            <div class="absolute top-{{ $isCompact ? '4' : '5' }} {{ $isCompact ? 'left-8' : 'left-10' }} w-full h-0.5 bg-gray-300 -z-10">
                                <div class="h-full bg-green-500 transition-all duration-300 {{ $isCompleted ? 'w-full' : 'w-0' }}"></div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Compact Mode: Show titles below -->
            @if($isCompact)
                <div class="mt-4 grid grid-cols-{{ $totalSteps }} gap-2">
                    @foreach($steps as $index => $step)
                        @php
                            $stepNumber = $index + 1;
                            $isCompleted = in_array($stepNumber, $completedSteps);
                            $isCurrent = $stepNumber === $currentStep;
                        @endphp

                        <div class="text-center">
                            <h3 class="text-xs font-medium {{ $isCurrent ? 'text-blue-600' : ($isCompleted ? 'text-green-600' : 'text-gray-500') }}">
                                {{ $step['title'] ?? "مرحله {$stepNumber}" }}
                            </h3>
                            @if(isset($step['description']))
                                <p class="text-xs text-gray-600 mt-1">{{ $step['description'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>

<style>
    .stepper-container {
        font-family: 'Vazirmatn', sans-serif;
    }

    .stepper-horizontal .stepper-item {
        position: relative;
    }

    .stepper-vertical .stepper-line {
        background: linear-gradient(to bottom, #d1d5db 0%, #d1d5db 100%);
    }

    .stepper-horizontal .stepper-item:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 50%;
        right: -2rem;
        width: 4rem;
        height: 2px;
        background: #d1d5db;
        transform: translateY(-50%);
        z-index: -1;
    }

    .stepper-horizontal .stepper-item.completed:not(:last-child)::after {
        background: #10b981;
    }

    /* Animation for step transitions */
    .stepper-icon-wrapper {
        transition: all 0.3s ease-in-out;
    }

    .stepper-icon-wrapper:hover {
        transform: scale(1.05);
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .stepper-horizontal .stepper-item:not(:last-child)::after {
            width: 2rem;
            right: -1rem;
        }

        .stepper-horizontal .stepper-content {
            max-width: 16rem;
        }

        .stepper-horizontal .stepper-content h3 {
            font-size: 0.75rem;
        }

        .stepper-horizontal .stepper-content p {
            font-size: 0.625rem;
        }
    }
</style>
