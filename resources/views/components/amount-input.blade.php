@props([
    'name' => 'amount',
    'label' => 'مبلغ',
    'placeholder' => 'مبلغ را وارد کنید...',
    'required' => false,
    'min' => 0,
    'max' => null,
    'step' => 1000,
    'currency' => 'تومان',
    'showCurrency' => true,
    'showThousandSeparator' => true,
    'persianNumbers' => true,
    'class' => '',
    'id' => null
])

@php
    $inputId = $id ?? $name . '_input';
    $displayId = $name . '_display';
@endphp

<div class="amount-input-container {{ $class }}" dir="rtl">
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative">
        <input
            type="text"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ $attributes->get('value', old($name)) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-right']) }}
            autocomplete="off"
            data-min="{{ $min }}"
            data-max="{{ $max }}"
            data-step="{{ $step }}"
            data-currency="{{ $currency }}"
            data-show-currency="{{ $showCurrency ? 'true' : 'false' }}"
            data-show-thousand-separator="{{ $showThousandSeparator ? 'true' : 'false' }}"
            data-persian-numbers="{{ $persianNumbers ? 'true' : 'false' }}"
        >

        @if($showCurrency)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 text-sm">{{ $currency }}</span>
            </div>
        @endif

        <!-- Hidden input for form submission -->
        <input type="hidden" name="{{ $name }}_raw" id="{{ $name }}_raw">
    </div>

    <!-- Display formatted amount -->
    <div id="{{ $displayId }}" class="mt-2 text-sm text-gray-600 hidden">
        <span class="font-medium">مبلغ: </span>
        <span class="formatted-amount"></span>
    </div>

    <!-- Validation messages -->
    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <!-- Help text -->
    @if($min > 0 || $max)
        <div class="mt-1 text-xs text-gray-500">
            @if($min > 0 && $max)
                محدوده مجاز: {{ number_format($min) }} تا {{ number_format($max) }} {{ $currency }}
            @elseif($min > 0)
                حداقل مبلغ: {{ number_format($min) }} {{ $currency }}
            @elseif($max)
                حداکثر مبلغ: {{ number_format($max) }} {{ $currency }}
            @endif
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInputs = document.querySelectorAll('.amount-input-container input[type="text"]');

    amountInputs.forEach(function(input) {
        const config = {
            min: parseInt(input.dataset.min) || 0,
            max: parseInt(input.dataset.max) || null,
            step: parseInt(input.dataset.step) || 1000,
            currency: input.dataset.currency || 'تومان',
            showCurrency: input.dataset.showCurrency === 'true',
            showThousandSeparator: input.dataset.showThousandSeparator === 'true',
            persianNumbers: input.dataset.persianNumbers === 'true'
        };

        const hiddenInput = document.getElementById(input.name + '_raw');
        const displayDiv = document.getElementById(input.name + '_display');
        const formattedSpan = displayDiv ? displayDiv.querySelector('.formatted-amount') : null;

        // Format number with thousand separators
        function formatNumber(num) {
            if (!num) return '';

            let formatted = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            if (config.persianNumbers) {
                formatted = toPersianNumbers(formatted);
            }

            return formatted;
        }

        // Convert to Persian numbers
        function toPersianNumbers(str) {
            const persianDigits = '۰۱۲۳۴۵۶۷۸۹';
            const englishDigits = '0123456789';

            return str.replace(/[0-9]/g, function(w) {
                return persianDigits[englishDigits.indexOf(w)];
            });
        }

        // Convert from Persian numbers
        function fromPersianNumbers(str) {
            const persianDigits = '۰۱۲۳۴۵۶۷۸۹';
            const englishDigits = '0123456789';

            return str.replace(/[۰-۹]/g, function(w) {
                return englishDigits[persianDigits.indexOf(w)];
            });
        }

        // Parse number from formatted string
        function parseNumber(str) {
            if (!str) return 0;

            // Remove currency text
            str = str.replace(new RegExp(config.currency, 'g'), '').trim();

            // Convert Persian numbers to English
            if (config.persianNumbers) {
                str = fromPersianNumbers(str);
            }

            // Remove thousand separators and non-numeric characters
            str = str.replace(/[^\d]/g, '');

            return parseInt(str) || 0;
        }

        // Validate amount
        function validateAmount(amount) {
            if (amount < config.min) {
                return `مبلغ باید حداقل ${formatNumber(config.min)} ${config.currency} باشد`;
            }

            if (config.max && amount > config.max) {
                return `مبلغ نباید بیشتر از ${formatNumber(config.max)} ${config.currency} باشد`;
            }

            if (amount % config.step !== 0) {
                return `مبلغ باید مضربی از ${formatNumber(config.step)} ${config.currency} باشد`;
            }

            return null;
        }

        // Update display
        function updateDisplay(amount) {
            if (displayDiv && formattedSpan && amount > 0) {
                formattedSpan.textContent = formatNumber(amount) + ' ' + config.currency;
                displayDiv.classList.remove('hidden');
            } else if (displayDiv) {
                displayDiv.classList.add('hidden');
            }
        }

        // Handle input events
        input.addEventListener('input', function(e) {
            let value = e.target.value;

            // Remove currency text if user types it
            value = value.replace(new RegExp(config.currency, 'g'), '').trim();

            // Convert Persian numbers to English for processing
            if (config.persianNumbers) {
                value = fromPersianNumbers(value);
            }

            // Remove all non-numeric characters except commas
            value = value.replace(/[^\d,]/g, '');

            // Remove multiple commas
            value = value.replace(/,+/g, ',');

            // Remove leading commas
            value = value.replace(/^,+/g, '');

            // Parse the number
            const amount = parseNumber(value);

            // Update hidden input with raw value
            if (hiddenInput) {
                hiddenInput.value = amount;
            }

            // Update display
            updateDisplay(amount);

            // Validate
            const error = validateAmount(amount);
            if (error) {
                input.setCustomValidity(error);
            } else {
                input.setCustomValidity('');
            }

            // Format and display
            if (amount > 0) {
                const formatted = formatNumber(amount);
                if (config.showThousandSeparator) {
                    e.target.value = formatted;
                } else {
                    e.target.value = amount.toString();
                }
            } else {
                e.target.value = '';
            }
        });

        // Handle focus
        input.addEventListener('focus', function(e) {
            const amount = parseNumber(e.target.value);
            if (amount > 0) {
                e.target.value = amount.toString();
            }
        });

        // Handle blur
        input.addEventListener('blur', function(e) {
            const amount = parseNumber(e.target.value);
            if (amount > 0) {
                const formatted = formatNumber(amount);
                if (config.showThousandSeparator) {
                    e.target.value = formatted;
                } else {
                    e.target.value = amount.toString();
                }
                updateDisplay(amount);
            }
        });

        // Handle paste
        input.addEventListener('paste', function(e) {
            setTimeout(function() {
                input.dispatchEvent(new Event('input'));
            }, 10);
        });

        // Initialize with existing value
        if (input.value) {
            input.dispatchEvent(new Event('input'));
        }
    });

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            const amountInputs = document.querySelectorAll('.amount-input-container input[type="text"]');
            const focusedInput = document.activeElement;

            if (amountInputs.contains(focusedInput)) {
                const input = focusedInput;
                const config = {
                    step: parseInt(input.dataset.step) || 1000
                };

                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const currentAmount = parseInt(input.dataset.value || '0');
                    const newAmount = currentAmount + config.step;
                    input.value = newAmount.toString();
                    input.dispatchEvent(new Event('input'));
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const currentAmount = parseInt(input.dataset.value || '0');
                    const newAmount = Math.max(0, currentAmount - config.step);
                    input.value = newAmount.toString();
                    input.dispatchEvent(new Event('input'));
                }
            }
        }
    });
});
</script>
@endpush

