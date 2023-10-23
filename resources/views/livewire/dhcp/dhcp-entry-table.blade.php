@php
    $headers = [
        'hostname' => [
            'label' => 'Hostname',
            'active' => false,
        ],
        'macAddresses' => [
            'label' => 'MAC',
            'active' => false,
        ],
        'ip_address' => [
            'label' => 'IP',
            'active' => false,
        ],
        'created_at' => [
            'label' => 'Added on',
            'active' => false,
        ],
        'added_by' => [
            'label' => 'Added by',
            'active' => false,
        ],
        'owner' => [
            'label' => 'Owner',
            'active' => false,
        ],
        'notes' => [
            'label' => 'Notes',
            'active' => false,
        ],
    ];
@endphp


<div class="px-4 sm:px-6 lg:px-8">

    {{-- {{dump($dhcpEntries)}} --}}

    <div class="justify-around sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">DHCP Entries</h1>
            <p class="mt-2 text-sm text-gray-700">DHCP entry list</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <button type="button"
                class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <a href="{{ route('dhcp-entry.create') }}">
                    Add DHCP entry
                </a>
            </button>
        </div>
    </div>


    <div class="flex justify-between">
        {{-- Search --}}
        <div class="relative mt-2 flex items-center">
            <input
                wire:model.live="search" type="text" name="search" id="search"
                class="block w-full rounded-md border-0 py-1.5 pr-14 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>

        {{-- Pagination dropdown --}}
        <div>
            <label for="perPage" class="block text-sm font-medium leading-6 text-gray-900">Results per page</label>
            <select
                wire:model.live="perPage"
                id="perPage" name="perPage"
                class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                <option>10</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
            </select>
        </div>
    </div>
    {{-- End search --}}

    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead>
                        <tr>

                            @foreach ($headers as $header)
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                    <a href="#" class="group inline-flex">
                                        {{ $header['label'] }}
                                        <!-- Active: "bg-gray-100 text-gray-900 group-hover:bg-gray-200", Not Active: "invisible text-gray-400 group-hover:visible group-focus:visible" -->
                                        <span class="ml-2 flex-none rounded
                                            {{-- bg-gray-100 text-gray-900 group-hover:bg-gray-200 --}}
                                            invisible text-gray-400 group-hover:visible group-focus:visible
                                            ">
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </a>
                                </th>
                            @endforeach

                            <th scope="col" class="relative py-3.5 pl-3 pr-0">
                                <span class="sr-only">Edit</span>
                            </th>

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($dhcpEntries as $dhcpEntry)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                    {{ $dhcpEntry->hostname }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    @foreach ($dhcpEntry->macAddresses as $macAddress)
                                        <div>{{ $macAddress->mac_address }}</div>
                                    @endforeach
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $dhcpEntry->ip_address }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $dhcpEntry->created_at }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $dhcpEntry->added_by }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $dhcpEntry->owner }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $dhcpEntry->notes }}
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm sm:pr-0">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit
                                        <span class="sr-only"></span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- Pagination --}}
    <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
        <div class="-mt-px flex w-0 flex-1">
            <a href="#"
                class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                <svg class="mr-3 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M18 10a.75.75 0 01-.75.75H4.66l2.1 1.95a.75.75 0 11-1.02 1.1l-3.5-3.25a.75.75 0 010-1.1l3.5-3.25a.75.75 0 111.02 1.1l-2.1 1.95h12.59A.75.75 0 0118 10z"
                        clip-rule="evenodd" />
                </svg>
                Previous
            </a>
        </div>
        <div class="hidden md:-mt-px md:flex">
            <a href="#"
                class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">1</a>
            <!-- Current: "border-indigo-500 text-indigo-600", Default: "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" -->
            <a href="#"
                class="inline-flex items-center border-t-2 border-indigo-500 px-4 pt-4 text-sm font-medium text-indigo-600"
                aria-current="page">2</a>
            <a href="#"
                class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">3</a>
            <span
                class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500">...</span>
            <a href="#"
                class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">8</a>
            <a href="#"
                class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">9</a>
            <a href="#"
                class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">10</a>
        </div>
        <div class="-mt-px flex w-0 flex-1 justify-end">
            <a href="#"
                class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                Next
                <svg class="ml-3 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M2 10a.75.75 0 01.75-.75h12.59l-2.1-1.95a.75.75 0 111.02-1.1l3.5 3.25a.75.75 0 010 1.1l-3.5 3.25a.75.75 0 11-1.02-1.1l2.1-1.95H2.75A.75.75 0 012 10z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </nav>
</div>
