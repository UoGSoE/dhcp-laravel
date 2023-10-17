{{-- @extends('layouts.app') --}}

{{-- @section('content') --}}
    <div class="w-full flex align-center justify-center min-h-full flex-col py-12 sm:px-6 lg:px-8">
        <div class="w-full mt-8 mx-auto lg:max-w-1/2">
            <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
                <form wire:submit="authenticate" class="space-y-6">
                    <div>
                        <label for="guid" class="block text-sm font-medium leading-6 text-gray-900">
                            GUID
                        </label>

                        <div class="mt-2">
                            <input id="guid" name="guid" wire:model="guid" type="text" placeholder="Username" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                            Password
                        </label>
                        <div class="mt-2">
                            <input
                                wire:model="password"
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                placeholder="Password"
                                required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input wire:model="rememberMe" id="remember-me" name="remember-me" type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            <label for="remember-me" class="ml-3 block text-sm leading-6 text-gray-900">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            class="flex w-full justify-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Sign in
                    </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

{{-- @endsection --}}
