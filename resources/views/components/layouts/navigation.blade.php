@props([
    'items' => [
        'DHCP DB' => [
            'route' => 'dhcp-entries',
        ],
        'Edit DHCP config' => [
            'route' => 'dhcp-config',
        ],
        'Import CSV' => [
            'route' => 'import-csv.index',
        ],
        'Export' => [
            'route' => 'export.index'
        ],
        'Subnet usage' => [
            'route' => 'dhcp-entry.create',
        ],
        'Logout' => [
            'route' => 'logout',
        ],
        'Documentation' => [
            'route' => 'documentation'
        ],
    ]
])

<div class="sm:flex sm:flex-row sm:gap-y-5 w-full bg-primary text-white h-16 items-center px-8">

    <nav class="flex flex-col w-full">
        <ul role="list" class="flex flex-row items-baseline">
            @foreach ($items as $key => $item)

                @if ($key == 'DHCP DB')
                    <li class="mr-4">
                        <a href="{{ route($item['route']) }}" class="{{ (request()->routeIs($item['route'])) ? 'text-white-600' : 'text-white-600 hover:text-white-600' }}
                            group flex gap-x-3 text-xl leading-6 w-full pr-3">
                            {{ $key }}
                        </a>
                    </li>
                @else
                    <li class="">
                        <a href="{{ route($item['route']) }}" class="{{ (request()->routeIs($item['route'])) ? 'text-white-600' : 'text-white-600 hover:text-white-600' }}
                            group flex gap-x-3 text-base leading-6 w-full pr-3">
                            {{ $key }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>
</div>
