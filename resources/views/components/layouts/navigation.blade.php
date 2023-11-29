@props([
    'items' => [
        'Home' => [
            'icon' => 'fa-house',
            'route' => 'dhcp-entries',
        ],
        'Edit DHCP config' => [
            'icon' => '',
            'route' => 'dhcp-config',
        ],
        'Import CSV' => [
            'icon' => '',
            'route' => 'import-csv.index',
        ],
        'Export' => [
            'icon' => '',
            'route' => 'export.index'
        ],
        'Subnet usage' => [
            'icon' => '',
            'route' => 'dhcp-entry.create',
        ],
        'Logout' => [
            'icon' => 'fa-right-from-bracket',
            'route' => 'logout',
        ],
        'Documentation' => [
            'icon' => 'fa-book',
            'route' => 'documentation'
        ],
    ]
])

<div class="border-gray-200 bg-white sm:h-full sm:flex sm:flex-col sm:gap-y-5 sm:border-r w-full h-max border-b">

    <nav class="pt-12 flex flex-col h-full">
        <ul role="list" class="flex flex-col gap-y-7 h-full">
            <li class="h-full">
                <ul role="list" class="flex flex-col h-full">
                    @foreach ($items as $key => $item)

                        @if ($key == 'Documentation')
                            <li class="flex justify-self-end">
                                <a href="{{ route($item['route']) }}" class="{{ (request()->routeIs($item['route'])) ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}
                                    group flex gap-x-3 rounded-md p-2 text-base leading-6 font-semibold w-full pr-3">
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
                        @else
                            <li class="w-full px-3">
                                <a href="{{ route($item['route']) }}" class="{{ (request()->routeIs($item['route'])) ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}
                                    group flex gap-x-3 rounded-md p-2 text-base leading-6 font-semibold w-full pr-3">
                                    <div class="grid" style="grid-template-columns: 2rem 100%">
                                        <span class="pl-2">
                                            @if ($item['icon'])
                                            <i class="fa-solid {{ $item['icon'] }}"></i>
                                            @endif
                                        </span>
                                        <span class="pr-2">
                                            {{ $key }}
                                        </span>
                                    </div>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>
        </ul>
    </nav>
</div>
