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
            {{-- Brand --}}
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                <x-ui.icon name="command-line" class="h-5 w-5 text-primary" />
                <x-ui.text class="font-semibold tracking-tight">{{ $general->site_name }}</x-ui.text>
            </a>

            <div class="ml-auto flex items-center gap-x-2">
                <x-ui.theme-switcher variant="dropdown" />

                {{-- Nav dropdown --}}
                <x-ui.dropdown position="bottom-end">
                    <x-slot:button>
                        <x-ui.button variant="ghost" size="sm" icon="bars-3">
                            {{ __('Menu') }}
                        </x-ui.button>
                    </x-slot:button>

                    <x-slot:menu class="w-52">
                        <x-ui.dropdown.item :href="route('home')" icon="home" wire:navigate>
                            {{ __('Home') }}
                        </x-ui.dropdown.item>
                        <x-ui.dropdown.item :href="route('posts.index')" icon="document-text" wire:navigate>
                            {{ __('Blog') }}
                        </x-ui.dropdown.item>
                        <x-ui.dropdown.item :href="route('projects.index')" icon="folder-open" wire:navigate>
                            {{ __('Projects') }}
                        </x-ui.dropdown.item>
                    </x-slot:menu>
                </x-ui.dropdown>
            </div>
        </x-ui.layout.header>

        {{ $slot }}
    </body>
</html>
