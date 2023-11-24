<div class="w-full">

    @if ((session('info') || session('success') || session('error')) && $showAlertMessage)
        @include('components.alerts.alert')
    @endif

    <div class="justify-around sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">
                Import CSV
            </h1>
        </div>
    </div>

    <form
        name="upload"
        enctype="multipart/form-data"
        wire:submit.prevent="import"
    >
        @csrf
        <div class="flex flex-row gap-4 sm:py-6">
            <label for="upload" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">
                CSV upload
            </label>
            <div class="mt-2 sm:col-span-2 sm:mt-0">
                <div
                    class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">

                    <input
                        wire:model.live="uploadedCsv"
                        accept=".csv" type="file" name="upload" id="upload"
                        class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" />
                </div>

                @error('uploadedCsv')
                <div class='error-message text-red-700 mt-3 flex items-center'>
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span class='px-2 text-sm'>
                        {{ $message}}
                    </span>
                </div>
                @enderror
            </div>
        </div>

        <button
            type="submit"
            @if (count($errors)> 0)
                aria-disabled
                disabled
            @endif
            class="{{ (count($errors) > 0) ? 'disabled:opacity-75 disabled
            aria-disabled ' : '' }} inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm
            font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2
            focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Upload
        </button>
    </form>
</div>
