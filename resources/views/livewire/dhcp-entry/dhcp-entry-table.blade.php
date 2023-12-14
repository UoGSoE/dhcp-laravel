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
            'label' => 'Campus System?',
        ],
        'active' => [
            'property' => 'is_active',
            'label' => 'Active',
        ],
        'last_seen' => [
            'property' => 'last_seen',
            'label' => 'Last Seen',
        ],
    ]
])

<div class="w-full">
    {{-- Alert messages --}}
    @if ((session('info') || session('success') || session('error')) && $showAlertMessage)
        @include('components.alerts.alert')
    @endif

    <div class="justify-around sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-lg font-semibold leading-6 text-gray-900">DHCP Entries</h1>
        </div>
    </div>


    <div class="flex flex-row w-full align-middle mt-10 justify-between">
        {{-- Add DHCP entry button --}}
        <div class="sm:mt-0 sm:flex sm:flex-row sm:gap-4">
            <a href="{{ route('dhcp-entry.create') }}">
                <button type="button"
                    class="block rounded-md bg-primary px-3 py-2 text-center text-base font-semibold text-white shadow-sm hover:bg-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                    Add DHCP entry
                </button>
            </a>

            @if (count($this->selected) > 0)
                <div>
                    <button
                        wire:click="deleteSelected({{ json_encode($this->selected) }})"
                        wire:confirm="Are you sure you want to delete these entries?"
                        type="button"
                        class="block rounded-md bg-primary px-3 py-2 text-center text-base font-semibold text-white shadow-sm hover:bg-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                        Delete selected
                    </button>
                </div>
            @endif
        </div>

        @if (count($dhcpEntries) > 0 || $search !== '')
            {{-- Search and filter --}}
            <div class="flex justify-between align-middle gap-8">

                {{-- Search --}}
                <div class="">
                    <div class="relative rounded-md shadow-sm w-96">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                        </div>
                        <input wire:model.live="search" type="text" name="search" id="search"
                            placeholder="Search"
                            class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-base sm:leading-6" />
                    </div>
                </div>

                {{-- Active filter --}}
                <div class="w-max">
                    <div class="flex flex-row align-middle gap-3">
                        <label for="activeFilter" class="text-base font-medium leading-6 text-gray-900 flex self-center w-40">Filter active:</label>
                        <select wire:model.live="activeFilter" id="activeFilter" name="activeFilter"
                            class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary sm:text-base sm:leading-6">
                            <option value="reset">All</option>
                            <option value="true">Active</option>
                            <option value="false">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        @endif


    </div>

    @if (count($dhcpEntries) > 0 || $search !== '' || $activeFilter !== '')
        <div class="">
            {{-- Table --}}
            <div class="mt-8 flow-root">
                <div class="my-2 overflow-x-auto pb-20">
                    <div class="inline-block min-w-full py-2 align-middle pl-1 -mb-4">

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
                                    {{-- Edit row --}}
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-base font-semibold text-gray-900 sm:pl-0">
                                    </th>

                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-base font-semibold text-gray-900 sm:pl-0">
                                        <input wire:model.live="selectPage" type="checkbox" />
                                    </th>

                                    @foreach ($headers as $header)
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-base font-semibold text-gray-900 sm:pl-0">
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

                                        {{-- Edit row --}}
                                        <td class="whitespace-nowrap py-4 pl-4 pr-6 text-base font-medium text-gray-900 sm:pl-0">

                                            @if (array_key_exists($dhcpEntry->id, $editRowActive) && $editRowActive[$dhcpEntry->id] === true)
                                                <div class="flex flex-row gap-5">
                                                    <i
                                                        wire:click.prevent="saveUpdatedRow({{ $dhcpEntry }})"
                                                        class="fa-solid fa-check fa-lg opacity-50 hover:cursor-pointer hover:opacity-100 text-green-700"></i>

                                                    <i
                                                        wire:click.prevent="cancelEditField('{{ $dhcpEntry->id }}')"
                                                        class="fa-solid fa-xmark fa-lg opacity-50 hover:cursor-pointer hover:opacity-100 text-red-700"></i>
                                                </div>
                                            @else
                                                <i wire:click.prevent="prepareEditDhcpRow({{ $dhcpEntry }})" class="fa-solid fa-pencil opacity-30 hover:cursor-pointer hover:opacity-50"></i>
                                            @endif

                                        </td>

                                        <td class="whitespace-nowrap px-3 py-4 text-base font-medium text-gray-900 sm:pl-0">
                                            <input
                                                wire:model.live="selected"
                                                type="checkbox"
                                                value="{{ $dhcpEntry->id }}"
                                                @checked(in_array($dhcpEntry->id, $selected))
                                            />
                                        </td>

                                        <td class="whitespace-nowrap px-3 py-4 text-base font-medium text-gray-900 sm:pl-0">
                                            @include('components.layouts.partials.table-inline-input', [
                                                'fieldName' => 'hostname',
                                                'dhcpEntry' => $dhcpEntry,
                                                'errors' => $validationErrors
                                            ])
                                        </td>

                                        <td class="whitespace-nowrap px-3 py-4 text-base text-gray-500">
                                            @include('components.layouts.partials.table-inline-input', [
                                                'fieldName' => 'mac_address',
                                                'dhcpEntry' => $dhcpEntry,
                                                'errors' => $validationErrors
                                            ])
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-base text-gray-500">
                                            @include('components.layouts.partials.table-inline-input', [
                                                'fieldName' => 'ip_address',
                                                'dhcpEntry' => $dhcpEntry,
                                                'errors' => $validationErrors
                                            ])
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-base text-gray-500">
                                            @if ($dhcpEntry->created_at)
                                                {{ $dhcpEntry->created_at->format('d/m/Y H:i') }}
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-base text-gray-500">
                                            @if ($dhcpEntry->added_by)
                                                {{ $dhcpEntry->added_by }}
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-base text-gray-500">
                                            @include('components.layouts.partials.table-inline-input', [
                                                'fieldName' => 'owner',
                                                'dhcpEntry' => $dhcpEntry,
                                                'errors' => $validationErrors
                                            ])
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-base text-gray-500">
                                            {{ $dhcpEntry->notes->sortByDesc('updated_at')->first()->note ?? '' }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-base text-gray-500">
                                            @if (array_key_exists($dhcpEntry->id, $editedEntries))
                                                <input
                                                    wire:model.live="editedEntries.{{ $dhcpEntry->id }}.is_ssd"
                                                    type="checkbox"
                                                    name="is_ssd"
                                                    id="is_ssd"
                                                    @if ($dhcpEntry->is_ssd)
                                                        checked
                                                    @endif
                                                />

                                            @else
                                                @if ($dhcpEntry->is_ssd)
                                                    <i class="fa-solid fa-check"></i>
                                                @else
                                                    <i class="fa-solid fa-times"></i>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-base text-gray-500">
                                            @if (array_key_exists($dhcpEntry->id, $editedEntries))
                                                <input
                                                    wire:model.live="editedEntries.{{ $dhcpEntry->id }}.is_active"
                                                    type="checkbox"
                                                    name="is_active"
                                                    id="is_active"
                                                    @if ($dhcpEntry->is_active)
                                                        checked
                                                    @endif
                                                />

                                            @else
                                                <span
                                                    class="inline-flex items-center rounded-md px-2 py-1 text-sm font-medium ring-1 ring-inset
                                                    {{ $dhcpEntry->is_active ? ' bg-green-50 text-green-700 ring-green-600/20' : ' bg-red-50 text-red-700 ring-red-600/20'}}">
                                                    {{ $dhcpEntry->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-base text-gray-500">
                                            <span>
                                                {{-- TODO placeholder last seen property --}}
                                                @if ($dhcpEntry->last_seen)
                                                @endif
                                                dd/mm/yy H:i
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-base text-gray-500">
                                            <a href="{{ route('dhcp-entry.edit', $dhcpEntry->id) }}" class="text-primary hover:text-primary-900">Edit/View
                                                <span class="sr-only">Edit/View</span>
                                            </a>
                                        </td>

                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-base sm:pr-0">
                                            <button
                                                wire:click="deleteDhcpEntry('{{ $dhcpEntry->id }}')"
                                                wire:confirm="Are you sure you want to delete this entry?"
                                                class="hover:text-primary-900 text-gray-600">
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
            <div class="mt-20">
                {{ $dhcpEntries->links() }}
            </div>
        </div>
    @else
    {{-- No DHCP entries info --}}
        <div class="rounded-md bg-blue-50 mt-8 p-4">
            <div class="flex align-middle">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-circle-info text-blue-400"></i>
                </div>
                <div class="ml-3 flex-1 md:flex md:justify-between">
                    <p class="text-base text-gray-700">No DHCP entries found.</p>
                </div>
            </div>
        </div>
    @endif
</div>
