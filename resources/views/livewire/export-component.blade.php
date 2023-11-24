<div class="w-full">

    @if ((session('info') || session('success') || session('error')) && $showAlertMessage)
        @include('components.alerts.alert')
    @endif

    <div class="justify-around sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">
                Export
            </h1>
        </div>
    </div>

    <div class="mt-6 flex flex-row gap-x-6">
        <button type="button"
            wire:click="exportCsv"
            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Export CSV
        </button>

        <button type="button"
            wire:click="exportJson"
            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Export JSON
        </button>
    </div>
</div>
