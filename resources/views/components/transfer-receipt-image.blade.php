@props(['loanTransfer', 'alt' => 'رسید انتقال وام', 'class' => 'max-w-full h-auto rounded-lg shadow-md'])

@php
    $imageUrl = null;
    if ($loanTransfer && $loanTransfer->transfer_receipt_path) {
        $imageUrl = Storage::url($loanTransfer->transfer_receipt_path);
    }
@endphp

@if($imageUrl)
    <div class="transfer-receipt-image-container" dir="rtl">
        <img
            src="{{ $imageUrl }}"
            alt="{{ $alt }}"
            class="{{ $class }}"
            loading="lazy"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
        >
        <div class="hidden bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="mt-2 text-sm text-gray-500">تصویر قابل نمایش نیست</p>
        </div>

        @if(auth()->user()->can('delete', $loanTransfer->transfer_receipt_path))
            <div class="mt-2 text-center">
                <button
                    type="button"
                    onclick="deleteTransferReceipt('{{ $loanTransfer->id }}')"
                    class="inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    حذف رسید
                </button>
            </div>
        @endif
    </div>
@else
    <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="mt-2 text-sm text-gray-500">رسید انتقال وام آپلود نشده است</p>
    </div>
@endif

@push('scripts')
<script>
function deleteTransferReceipt(transferId) {
    if (confirm('آیا از حذف رسید انتقال وام اطمینان دارید؟')) {
        fetch(`/transfer-receipts/${transferId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('خطا در حذف رسید: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('خطا در حذف رسید');
        });
    }
}
</script>
@endpush

