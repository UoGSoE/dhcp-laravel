@props(['headers' => [
        'hostname' => [
            'property' => 'hostname',
            'label' => 'Hostname',
        ],
        'macAddresses' => [
            'property' => 'mac_address',
            'label' => 'MAC',
        ],
        'ip_address' => [
            'property' => 'ip_address',
            'label' => 'IP',
        ],
        'created_at' => [
            'property' => 'created_at',
            'label' => 'Added on',
        ],
        'added_by' => [
            'property' => 'added_by',
            'label' => 'Added by',
        ],
        'owner' => [
            'property' => 'owner',
            'label' => 'Owner',
        ],
        'notes' => [
            'property' => 'notes.note',
            'label' => 'Notes',
        ],
        'ssd' => [
            'property' => 'is_ssd',
            'label' => 'SSD?',
        ],
        'active' => [
            'property' => 'is_active',
            'label' => 'Active',
        ],
        'imported' => [
            'property' => 'is_imported',
            'label' => 'Imported',
        ]
    ]
])

<div class="w-full">
    <div class="justify-around sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">DHCP Entries</h1>
            <p class="mt-2 text-sm text-gray-700">DHCP entry list</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex flex-row gap-4">
            <div class="">
                <a href="{{ route('dhcp-entry.create') }}">
                    <button type="button"
                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Add DHCP entry
                    </button>
                </a>
            </div>

            @if (count($this->selected) > 0)

                {{-- <div class="">
                    <button
                        wire:click="$dispatch('openModal', {component: 'dhcp-edit-modal', arguments: {selected: {{ json_encode($this->selected) }} }})"
                        type="button"
                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Edit selected
                    </button>
                </div> --}}

                <div class="">

                    <button
                        wire:click="deleteSelected({{ json_encode($this->selected) }})"
                        wire:confirm="Are you sure you want to delete these entries?"
                        type="button"
                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Delete selected
                    </button>

                </div>

            @endif


        </div>


    </div>


    <div class="flex justify-between">
        {{-- Search --}}
        <div class="relative mt-2 flex items-center">
            <input
                wire:model.live="search" type="text" name="search" id="search"
                class="block w-full rounded-md border-0 py-1.5 pr-14 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>

        <div>
            {{-- Active filter --}}
            <div>
                <label for="activeFilter" class="block text-sm font-medium leading-6 text-gray-900">Active</label>
                <select wire:model.live="activeFilter" id="activeFilter" name="activeFilter"
                    class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="reset" selected>All</option>
                    <option value="true">Active</option>
                    <option value="false">Inactive</option>
                </select>
            </div>


        </div>
    </div>
    {{-- End search --}}

    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">

                <div class="mb-4">

                    @if ($selectPage)
                        @unless ($selectAll)

                            You selected <strong>{{ count($selected) }}</strong> entries, would you like to select <strong>{{ $dhcpEntries->total() }}</strong> entries?
                            <button wire:click="selectAllEntries" class="btn text-blue-600">
                                Select All
                            </button>
                        @else
                            You have selected all <strong>{{ $dhcpEntries->total() }}</strong> entries.
                        @endunless
                    @endif

                </div>


                <table class="min-w-full divide-y divide-gray-300">
                    <thead>
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                <input
                                    wire:model.live="selectPage"
                                    type="checkbox"
                                    />
                            </th>

                            @foreach ($headers as $header)
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                    <a
                                        wire:click.prevent="sortBy('{{ $header['property'] }}')"
                                        role="button"
                                        href="#"
                                        class="group inline-flex">
                                        {{ $header['label'] }}
                                        <span class="ml-2 flex-none rounded">
                                            @if ($sortField === $header['property'])
                                                <span class="text-gray-400">
                                                    @if ($sortAsc)
                                                        <i class="fa-solid fa-chevron-up"></i>
                                                    @else
                                                        <i class="fa-solid fa-chevron-down"></i>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="invisible text-gray-400 group-hover:visible group-focus:visible">
                                                    <i class="fa-solid fa-sort"></i>
                                                </span>
                                            @endif
                                        </span>
                                    </a>
                                </th>
                            @endforeach

                            <th scope="col" class="relative py-3.5 pl-3 pr-0">
                                <span class="sr-only">Edit/View</span>
                            </th>

                            <th scope="col" class="relative py-3.5 pl-3 pr-0">
                                <span class="sr-only">Delete</span>
                            </th>

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($dhcpEntries as $dhcpEntry)
                            <tr wire:key="{{ $dhcpEntry->id }}" >
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                    <input
                                        wire:model.live="selected"
                                        type="checkbox"
                                        value="{{ $dhcpEntry->id }}"
                                        @checked(in_array($dhcpEntry->id, $selected))
                                    />
                                </td>

                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                    @if ($dhcpEntry->hostname)
                                        {{ $dhcpEntry->hostname }}
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <div>
                                        @if ($dhcpEntry->mac_address)
                                            {{ $dhcpEntry->mac_address }}</div>
                                        @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    @if ($dhcpEntry->ip_address)
                                        {{ $dhcpEntry->ip_address }}
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    @if ($dhcpEntry->created_at)
                                        {{ $dhcpEntry->created_at->format('d/m/Y h:i') }}
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    @if ($dhcpEntry->added_by)
                                        {{ $dhcpEntry->added_by }}
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    @if ($dhcpEntry->owner)
                                        {{ $dhcpEntry->owner }}
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $dhcpEntry->notes->sortByDesc('updated_at')->first()->note ?? '' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span>
                                        @if ($dhcpEntry->is_ssd)
                                            <i class="fa-solid fa-check"></i>
                                        @else
                                            <i class="fa-solid fa-times"></i>
                                        @endif
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                    {{ $dhcpEntry->is_active ? ' bg-green-50 text-green-700 ring-green-600/20' : ' bg-red-50 text-red-700 ring-red-600/20'}}">
                                    {{ $dhcpEntry->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span>
                                        @if ($dhcpEntry->is_imported)
                                        <i class="fa-solid fa-check"></i>
                                        @else
                                        <i class="fa-solid fa-times"></i>
                                        @endif
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <a href="{{ route('dhcp-entry.edit', $dhcpEntry->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit/View
                                        <span class="sr-only">Edit/View</span>
                                    </a>
                                </td>

                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm sm:pr-0">
                                    <button
                                        wire:click="deleteDhcpEntry('{{ $dhcpEntry->id }}')"
                                        wire:confirm="Are you sure you want to delete this entry?"
                                        class="hover:text-indigo-900 text-gray-600">
                                        <i class="fa-solid fa-trash"></i>
                                        <span class="sr-only">Delete</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- Pagination --}}
    <div class="mt-8">
        {{ $dhcpEntries->links() }}
    </div>
</div>
