<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
        @livewireStyles
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        @auth
            <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700 print:hidden">
                <flux:sidebar.header>
                    <flux:sidebar.brand
                        href="{{ route('home') }}"
                        name="DHCP DB"
                    />
                    <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
                </flux:sidebar.header>
                <flux:sidebar.nav>
                    <flux:sidebar.item icon="home" href="{{ route('home') }}" wire:navigate>Home</flux:sidebar.item>
                    <flux:sidebar.item icon="plus-circle" href="{{ route('hosts.create') }}" wire:navigate>New Host</flux:sidebar.item>
                    <flux:separator class="my-2" />
                    <flux:sidebar.group expandable icon="arrow-down-tray" heading="Export" class="grid">
                        <flux:sidebar.item href="{{ route('export.csv') }}">CSV</flux:sidebar.item>
                        <flux:sidebar.item href="{{ route('export.json') }}">JSON</flux:sidebar.item>
                    </flux:sidebar.group>
                    <flux:sidebar.item icon="chart-bar" href="{{ route('subnet-usage') }}" wire:navigate>Subnet Usage</flux:sidebar.item>
                    @can('dhcp-admin')
                        <flux:separator class="my-2" />
                        <flux:sidebar.group expandable icon="cog-6-tooth" heading="DHCP Config" class="grid">
                            <flux:sidebar.item href="{{ route('dhcp-sections.edit', 'Header') }}" wire:navigate>Header</flux:sidebar.item>
                            <flux:sidebar.item href="{{ route('dhcp-sections.edit', 'Subnets') }}" wire:navigate>Subnets</flux:sidebar.item>
                            <flux:sidebar.item href="{{ route('dhcp-sections.edit', 'Groups') }}" wire:navigate>Groups</flux:sidebar.item>
                            <flux:sidebar.item href="{{ route('dhcp-sections.edit', 'Footer') }}" wire:navigate>Footer</flux:sidebar.item>
                        </flux:sidebar.group>
                    @endcan
                </flux:sidebar.nav>
                <flux:sidebar.spacer />
                <flux:sidebar.nav>
                    <flux:sidebar.item tooltip="Logout" icon="arrow-right-start-on-rectangle">
                        <form method="post" action="{{ route('auth.logout') }}">
                            @csrf
                            <flux:button class="w-full" type="submit">
                                <span class="hidden sm:block">Logout</span>
                            </flux:button>
                        </form>
                    </flux:sidebar.item>
                </flux:sidebar.nav>
            </flux:sidebar>
        @endauth
        <flux:header class="lg:hidden print:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />
        </flux:header>

        <flux:main>
            @if (session('success'))
                <div class="mb-4">
                    <flux:badge color="green" size="lg">{{ session('success') }}</flux:badge>
                </div>
            @endif
            @if (session('warning'))
                <div class="mb-4">
                    <flux:badge color="amber" size="lg">{{ session('warning') }}</flux:badge>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4">
                    <flux:badge color="red" size="lg">{{ session('error') }}</flux:badge>
                </div>
            @endif

            {{ $slot }}
        </flux:main>

        <flux:toast />
        @fluxScripts
        @stack('scripts')
    </body>
</html>
