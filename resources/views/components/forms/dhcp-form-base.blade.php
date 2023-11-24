<div>

    @if (session()->has('success'))
        @include('components.alerts.alert', ['message' => session('success')])
    @endif

    <form>
        <div class="space-y-12 sm:space-y-16">
            <div>
                <h1 class="text-base font-semibold leading-7 text-gray-900">
                    @yield('form-title')
                </h1>

                <div
                    class="mt-10 space-y-8 border-b border-gray-900/10 pb-12 sm:space-y-0 sm:divide-y sm:divide-gray-900/10 sm:border-t sm:pb-0">

                    {{-- MAC addresses --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="macAddress" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">
                            MAC Address
                        </label>
                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <div
                                class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">

                                @yield('mac-address-input')
                            </div>

                            @error('macAddress')
                            <div class='error-message text-red-700 mt-3 flex items-center'>
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span class='px-2 text-sm'>
                                    {{ $message}}
                                </span>
                            </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Owner (email address) --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="owner" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">
                            Owner (email address)
                        </label>
                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <div
                                class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">

                                @yield('owner-input')
                            </div>

                            @error('owner')
                            <div class='error-message text-red-700 mt-3 flex items-center'>
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span class='px-2 text-sm'>
                                    {{ $message}}
                                </span>
                            </div>
                            @enderror
                        </div>
                    </div>

                    {{-- IP address --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="ipAddress" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">
                            Fixed IP Address
                        </label>
                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <div
                                class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">

                                @yield('ip-address-input')
                            </div>

                            @error('ipAddress')
                            <div class='error-message text-red-700 mt-3 flex items-center'>
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span class='px-2 text-sm'>
                                    {{ $message}}
                                </span>
                            </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Hostname --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="hostName" class="block text-sm font-medium leading-6 text-gray-900 sm:pt-1.5">
                            Hostname
                        </label>

                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                            <div
                                class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">

                                @yield('hostname-input')
                            </div>

                            <div class='mt-3 flex items-center'>
                                <i class="fa-solid fa-circle-info h-4"></i>
                                <span class='px-2 text-sm'>Hostname must only contain letters,
                                    numbers, hyphens or full-stops.</span>
                            </div>

                            @error('hostname')
                            <div class='error-message text-red-700 mt-3 flex items-center'>
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span class='px-2 text-sm'>
                                    {{ $message }}
                                </span>
                            </div>
                            @enderror
                        </div>
                    </div>

                    {{-- SSD --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="isSsd">SSD?</label>

                        @yield('ssd-input')
                    </div>

                    {{-- Status --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label for="isActive">Active?</label>

                        @yield('status-input')
                    </div>


                    {{-- Notes --}}
                    <div class="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        @yield('note-section')
                    </div>

                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">

            @yield('save-cancel-buttons')
        </div>
    </form>
</div>
