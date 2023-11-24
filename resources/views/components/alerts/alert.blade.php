@php
    $key = '';

    if (session('success')) {
        $key = 'success';
    } elseif (session('info')) {
        $key = 'info';
    } elseif (session('error')) {
        $key = 'error';
    }
@endphp

<div class="p-4 absolute top-0 right-0 w-full shadow-lg @if ($key == 'success') bg-green-50 @elseif ($key == 'info') bg-blue-50 @elseif ($key == 'error') bg-red-50 @endif">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 @if ($key == 'success') text-green-400 @elseif ($key == 'info') text-blue-400 @elseif ($key == 'error') text-red-400  @endif" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                    clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm
            @if ($key == 'success') font-medium text-green-800 @elseif ($key == 'info') font-medium text-blue-800 @elseif ($key == 'error') font-medium text-red-800  @endif
            ">
                {{ session($key) }}
            </p>
        </div>
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button type="button"
                    wire:click="$set('showAlertMessage', false)"
                    class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2
                    @if ($key == 'success') bg-green-50 text-green-500 hover:bg-green-100 focus:ring-green-600 focus:ring-offset-green-50 @elseif ($key == 'info') bg-blue-50 text-blue-500 hover:bg-blue-100 focus:ring-blue-600 focus:ring-offset-blue-50 @elseif ($key == 'error') bg-red-50 text-red-500 hover:bg-red-100 focus:ring-red-600 focus:ring-offset-red-50 @endif
                    ">
                    <span class="sr-only">Dismiss</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path
                            d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
