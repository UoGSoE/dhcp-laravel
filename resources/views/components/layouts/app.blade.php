<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="h-full"
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

    <body class="w-full h-full max-w-full max-h-full">
        <div id="app" class="{{ auth()->guest() ? '' : 'flex flex-row w-full h-full' }}">
            @auth
                <div class="h-full">
                    @include('components.layouts.navigation')
                </div>
            @endauth

            <div class="w-full mt-12 px-4 sm:px-6 lg:px-8">
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
