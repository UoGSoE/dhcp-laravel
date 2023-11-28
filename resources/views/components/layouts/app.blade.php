<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="h-full overflow-x-hidden"
>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- CSRF --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/js/app.js'])
        @livewireStyles

    </head>

    <body class="sm:w-full sm:h-full sm:max-w-full sm:max-h-full flex flex-col">
        <div id="app"
            class="{{ auth()->guest() ? '' : 'flex sm:flex-row sm:h-full' }}
            flex-col h-max">

            {{-- Navigation --}}
            @auth
                <div class="sm:flex sm:h-full sm:w-2/12">
                    @include('components.layouts.navigation')
                </div>
            @endauth

            {{-- Main content --}}
            <div class="sm:w-10/12 mt-12 px-4 sm:px-6 lg:px-8 w-full">
                @isset ($slot)
                    {{ $slot }}
                @endisset
            </div>
        </div>

        <!-- Scripts -->
        @livewire('wire-elements-modal')
        @livewireScripts
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
