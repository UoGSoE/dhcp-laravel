<div>
    @foreach($macAddresses as $index => $macAddress)
        {{-- MAC, multiple entry --}}
        <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
            <label
                for="macAddress"
                wire:model="macAddresses.{{ $index }}.macAddress"
                class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">
                MAC Address
            </label>
            <div class="mt-2 sm:col-span-2 sm:mt-0">
                <div
                    class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                    <input
                        wire:model.live="macAddresses.{{ $index }}.macAddress"
                        type="text" name="macAddress" id="macAddress" autocomplete="macAddress"
                        class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                        placeholder="MAC address" />
                </div>

                @error('macAddresses.' . $index . '.macAddress')
                    <span class="error">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    @endforeach

    <button
        wire:click="addMacAddress()"
        type="button"
        class="rounded-full bg-indigo-600 p-1 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Add MAC address
    </button>
</div>
