@props(['auction'])

@if($auction->isLocked())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6" dir="rtl">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="mr-3">
                <h3 class="text-sm font-medium text-red-800">
                    مزایده قفل شده
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>این مزایده قفل شده است و امکان ثبت پیشنهاد جدید وجود ندارد.</p>
                    @if($auction->locked_at)
                        <p class="mt-1">
                            <strong>زمان قفل شدن:</strong>
                            {{ $auction->locked_at->format('Y/m/d H:i') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
