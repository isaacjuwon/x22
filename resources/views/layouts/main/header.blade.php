<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="$theme.currentTheme === 'dark' ? 'dark' : ''">
    <head>
        @include('partials.head')
        {{ $head ?? '' }}
    </head>
    <body class="min-h-screen">
        @php
            use App\Settings\GeneralSettings;
            $general = app(GeneralSettings::class);
        @endphp
        <x-ui.layout.header>
            <x-ui.navbar class="flex-1">
                <x-ui.navbar.item icon="home" :label="$general->site_name" href="/" />
            </x-ui.navbar>

            <div class="flex items-center gap-x-3">
                <x-ui.theme-switcher variant="dropdown" />
                @auth
                    <x-ui.button as="a" variant="ghost" size="sm" :href="route('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-ui.button>
                @else
                    <x-ui.button variant="primary" :href="route('login')" wire:navigate>{{ __('Sign In') }}</x-ui.button>
                @endauth
            </div>
        </x-ui.layout.header>
        {{ $slot }}
    </body>
</html>
