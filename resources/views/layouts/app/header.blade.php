<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen">
        <x-ui.layout.header>
            <x-ui.navbar class="flex-1">
                <x-ui.navbar.item icon="home" label="Home" href="/" />
                
                @auth
                    <x-ui.navbar.item icon="folder" label="My Projects" href="/projects" />
                    <x-ui.navbar.item icon="star" label="Favorites" href="/favorites" />
                @endauth
            </x-ui.navbar>

            <div class="flex items-center gap-x-3">
                @auth
                    <x-ui.avatar src="{{ auth()->user()->avatar }}" circle size="sm" />
                @else
                    <x-ui.button variant="primary" href="/login">Sign In</x-ui.button>
                @endauth
            </div>
        </x-ui.layout.header>
        {{ $slot }}

    </body>
</html>
