<div>

    @if (session()->has('success') && $showAlertMessage)
        @include('components.alerts.alert', ['message' => session('success')])
    @endif

    <form>
        <div class="space-y-12 sm:space-y-16">
            <div>
                <h1 class="text-lg font-semibold leading-7 text-gray-900">
                    Edit DHCP Config
                </h1>

                <div
                    class="mt-10 space-y-8 border-b border-gray-900/10 pb-12 sm:space-y-0 sm:divide-y sm:divide-gray-900/10 sm:border-t sm:pb-0">

                    {{-- Header --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="header" class="block text-base font-medium leading-6 text-gray-900 sm:pt-1.5">
                            DHCP Header
                        </label>
                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <textarea wire:model.live="header" placeholder="Add header here..." id="header"
                                name="header" rows="10"
                                class="block w-full max-w-2xl rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"></textarea>
                        </div>
                    </div>

                    {{-- Subnets --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="subnets" class="block text-base font-medium leading-6 text-gray-900 sm:pt-1.5">
                            DHCP Subnets
                        </label>
                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <textarea wire:model.live="subnets" placeholder="Add subnets here..." id="subnets"
                                name="subnets" rows="10"
                                class="block w-full max-w-2xl rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"></textarea>
                        </div>
                    </div>

                    {{-- Groups --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="groups" class="block text-base font-medium leading-6 text-gray-900 sm:pt-1.5">
                            DHCP Groups
                        </label>
                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <textarea wire:model.live="groups" placeholder="Add groups here..." id="groups"
                                name="groups" rows="10"
                                class="block w-full max-w-2xl rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"></textarea>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="footer" class="block text-base font-medium leading-6 text-gray-900 sm:pt-1.5">
                            DHCP Footer
                        </label>
                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <textarea wire:model.live="footer" placeholder="Add footer here..." id="footer"
                                name="footer" rows="10"
                                class="block w-full max-w-2xl rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"></textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <button type="button" class="text-base font-semibold leading-6 text-gray-900">
                <a href="{{ route('dhcp-entries')}}">
                    Cancel
                </a>
            </button>
            <button wire:click.prevent="saveDhcpConfig" type="submit" @if (count($errors)> 0)
                aria-disabled
                disabled
                @endif
                class="{{ (count($errors) > 0) ? 'disabled:opacity-75 disabled
                aria-disabled ' : '' }} inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-base
                font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2
                focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Save
            </button>
        </div>
    </form>
</div>
