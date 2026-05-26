<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="$theme.currentTheme === 'dark' ? 'dark' : ''">
    <head>
        @include('partials.head')
        {{ $head ?? '' }}
    </head>
    <body class="min-h-screen zicify-frontend">
        @php
            use App\Settings\GeneralSettings;
            $general = app(GeneralSettings::class);
        @endphp
        <x-ui.layout.header>
            {{-- Brand --}}
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-4">
                <span class="text-xl font-bold tracking-tighter text-neutral-950 uppercase">{{ $general->site_name }}</span>
            </a>

            <div class="ml-auto flex items-center gap-x-4">
                <x-ui.button variant="ghost" size="sm" class="h-9 w-9 p-0 rounded-full">
                    <x-ui.icon name="ps:magnifying-glass" class="size-4" />
                </x-ui.button>

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
