@props([
    'items' => [
        'Home' => [
            'icon' => 'fa-house',
            'route' => 'index',
        ],
        'Import CSV' => [
            'icon' => '',
            'route' => 'dhcp-entries',
        ],
        'Export CSV' => [
            'icon' => '',
            'route' => 'dhcp-entry.create',
        ],
        'Export JSON' => [
            'icon' => '',
            'route' => 'dhcp-entry.create',
        ],
        'Subnet usage' => [
            'icon' => '',
            'route' => 'dhcp-entry.create',
        ],
        'Edit DHCP config' => [
            'icon' => '',
            'route' => 'dhcp-config',
        ],
        'Logout' => [
            'icon' => 'fa-right-from-bracket',
            'route' => 'dhcp-entry.create',
        ],
    ]
])

<div class="h-full flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6">

    <nav class="pt-12 flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    @foreach ($items as $key => $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                                class="{{ (request()->routeIs($item['route'])) ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}
                                group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <div class="grid" style="grid-template-columns: 2rem 100%">
                                    <span>
                                        @if ($item['icon'])
                                        <i class="fa-solid {{ $item['icon'] }}"></i>
                                        @endif
                                    </span>
                                    <span>
                                        {{ $key }}
                                    </span>
                                </div>
                            </a>
                        </li>
                    @endforeach

                </ul>
            </li>
        </ul>
    </nav>
</div>
