@props([
    'name' => 'file',
    'accept' => 'image/jpeg,image/jpg,image/png,image/webp',
    'maxSize' => '5MB',
    'label' => 'آپلود فایل',
    'placeholder' => 'فایل را انتخاب کنید...',
    'required' => false,
    'multiple' => false,
    'preview' => true,
    'class' => '',
])

@php
    $inputId = $name . '_input';
    $previewId = $name . '_preview';
@endphp

<div class="file-upload-container {{ $class }}" dir="rtl">
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative">
        <input
            type="file"
            id="{{ $inputId }}"
            name="{{ $name }}"
            accept="{{ $accept }}"
            {{ $required ? 'required' : '' }}
            {{ $multiple ? 'multiple' : '' }}
            class="hidden"
            onchange="handleFileSelect(this)"
        >

        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors cursor-pointer"
             onclick="document.getElementById('{{ $inputId }}').click()">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-600">
                <span class="font-medium text-blue-600 hover:text-blue-500">کلیک کنید</span>
                یا فایل را اینجا بکشید
            </p>
            <p class="text-xs text-gray-500 mt-1">
                حداکثر {{ $maxSize }} - فرمت‌های مجاز: JPG, PNG, WebP
            </p>
        </div>
    </div>

    @if($preview)
        <div id="{{ $previewId }}" class="mt-4 hidden">
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">پیش‌نمایش فایل:</h4>
                <div class="flex items-center space-x-3 space-x-reverse">
                    <img id="{{ $previewId }}_img" class="h-16 w-16 object-cover rounded-lg" alt="پیش‌نمایش">
                    <div class="flex-1">
                        <p id="{{ $previewId }}_name" class="text-sm font-medium text-gray-900"></p>
                        <p id="{{ $previewId }}_size" class="text-xs text-gray-500"></p>
                    </div>
                    <button
                        type="button"
                        onclick="removeFile('{{ $inputId }}', '{{ $previewId }}')"
                        class="text-red-500 hover:text-red-700"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <div id="{{ $name }}_error" class="mt-2 text-sm text-red-600 hidden"></div>
</div>

@push('scripts')
<script>
function handleFileSelect(input) {
    const file = input.files[0];
    const errorDiv = document.getElementById('{{ $name }}_error');
    const previewDiv = document.getElementById('{{ $previewId }}');

    // Clear previous errors
    errorDiv.classList.add('hidden');
    errorDiv.textContent = '';

    if (!file) {
        hidePreview();
        return;
    }

    // Validate file size (5MB = 5 * 1024 * 1024 bytes)
    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
        showError('حجم فایل نباید بیشتر از 5 مگابایت باشد.');
        input.value = '';
        return;
    }

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showError('فرمت فایل مجاز نیست. فقط فایل‌های JPG، PNG و WebP قابل قبول هستند.');
        input.value = '';
        return;
    }

    // Show preview
    showPreview(file);
}

function showPreview(file) {
    const previewDiv = document.getElementById('{{ $previewId }}');
    const img = document.getElementById('{{ $previewId }}_img');
    const name = document.getElementById('{{ $previewId }}_name');
    const size = document.getElementById('{{ $previewId }}_size');

    if (!previewDiv || !img || !name || !size) return;

    // Create preview URL
    const url = URL.createObjectURL(file);
    img.src = url;
    name.textContent = file.name;
    size.textContent = formatFileSize(file.size);

    previewDiv.classList.remove('hidden');
}

function hidePreview() {
    const previewDiv = document.getElementById('{{ $previewId }}');
    if (previewDiv) {
        previewDiv.classList.add('hidden');
    }
}

function removeFile(inputId, previewId) {
    const input = document.getElementById(inputId);
    const previewDiv = document.getElementById(previewId);

    input.value = '';
    if (previewDiv) {
        previewDiv.classList.add('hidden');
    }

    // Clear any errors
    const errorDiv = document.getElementById('{{ $name }}_error');
    if (errorDiv) {
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
    }
}

function showError(message) {
    const errorDiv = document.getElementById('{{ $name }}_error');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Drag and drop functionality
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.querySelector('.file-upload-container .border-dashed');
    const input = document.getElementById('{{ $inputId }}');

    if (!uploadArea || !input) return;

    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-blue-400', 'bg-blue-50');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-400', 'bg-blue-50');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            input.files = files;
            handleFileSelect(input);
        }
    });
});
</script>
@endpush
