<div>
    <form>
        <div class="space-y-12 sm:space-y-16">
            <div>
                <h1 class="text-base font-semibold leading-7 text-gray-900">
                    Create Host
                </h1>

                <div
                    class="mt-10 space-y-8 border-b border-gray-900/10 pb-12 sm:space-y-0 sm:divide-y sm:divide-gray-900/10 sm:border-t sm:pb-0">

                    {{-- MAC addresses --}}
                    <livewire:mac-address-component
                        :macAddresses="$macAddresses"
                    />

                    {{-- Owner (email address) --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label
                            for="owner"
                            class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">
                            Owner (email address)
                        </label>
                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <div
                                class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input
                                    wire:model="owner"
                                    type="text"
                                    name="owner"
                                    id="owner"
                                    autocomplete=""
                                    class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                                    placeholder="Owner (email address)"/>
                            </div>
                        </div>
                    </div>

                    {{-- IP address --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label
                            for="ipAddress"
                            class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">
                            Fixed IP Address
                        </label>
                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <div
                                class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input
                                    wire:model="ipAddress"
                                    type="text"
                                    name="ipAddress"
                                    id="ipAddress"
                                    autocomplete=""
                                    class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                                    placeholder="IP address (leave blank for pool)"/>
                            </div>
                        </div>
                    </div>

                    {{-- Hostname --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label
                            for="hostName"
                            class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">
                            Hostname
                        </label>

                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <div
                                class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input
                                    wire:model="hostname"
                                    type="text"
                                    name="hostName"
                                    id="hostName"
                                    autocomplete=""
                                    class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                                    placeholder="Hostname (only for fixed IP machines)"/>
                            </div>

                            <div class='mt-3 flex items-center'>
                                <i class="fa-solid fa-circle-info h-4"></i>
                                <span class='px-2 text-sm'>Hostname must only contain letters,
                                    numbers, hyphens or full-stops.</span>
                            </div>
                        </div>
                    </div>

                    {{-- SSD --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="isSsd">SSD?</label>
                        <input
                            wire:model="isSsd"
                            type="checkbox"
                            id="isSsd"
                            name="isSsd"
                            value="true">
                    </div>

                    {{-- Status --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="isActive">Active?</label>
                        <input
                            wire:model="isActive"
                            type="checkbox"
                            id="isActive"
                            name="isActive"
                            value="true"
                            checked>
                    </div>


                    {{-- Notes --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="notes" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">Notes</label>
                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <textarea
                                wire:model="notes.0"
                                placeholder="Add note here..."
                                id="notes"
                                name="notes"
                                rows="3"
                                class="block w-full max-w-2xl rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <button type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
            <button
                wire:click.prevent="createDhcpEntry()"
                type="submit"
                class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Save
            </button>
        </div>
    </form>
</div>
