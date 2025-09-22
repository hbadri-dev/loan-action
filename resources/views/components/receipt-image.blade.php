@props(['path', 'alt' => 'رسید پرداخت', 'class' => 'max-w-full h-auto rounded-lg shadow-md'])

@php
    $imageUrl = $path ? Storage::url($path) : null;
@endphp

@if($imageUrl)
    <div class="receipt-image-container" dir="rtl">
        <img
            src="{{ $imageUrl }}"
            alt="{{ $alt }}"
            class="{{ $class }}"
            loading="lazy"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
        >
        <div class="hidden bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">تصویر قابل نمایش نیست</p>
        </div>
    </div>
@else
    <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <p class="mt-2 text-sm text-gray-500">رسید پرداخت آپلود نشده است</p>
    </div>
@endif

