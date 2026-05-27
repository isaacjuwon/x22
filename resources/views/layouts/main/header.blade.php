<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="$theme.currentTheme === 'dark' ? 'dark' : ''">
    <head>
        @include('partials.head')
        {{ $head ?? '' }}
    </head>
    <body class="min-h-screen bg-neutral-50 dark:bg-black text-neutral-950 dark:text-white antialiased transition-colors duration-300">
        @php
            use App\Settings\GeneralSettings;
            $general = app(GeneralSettings::class);
        @endphp
        <x-ui.layout.header class="border-b border-neutral-200 dark:border-neutral-900 bg-white/80 dark:bg-black/50 backdrop-blur-xl transition-colors duration-300">
            {{-- Brand --}}
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-4 transition-opacity hover:opacity-80">
                <span class="text-xl font-bold tracking-tighter text-neutral-950 dark:text-white uppercase">{{ $general->site_name }}</span>
            </a>

            <div class="ml-auto flex items-center gap-x-6">
                <nav class="hidden md:flex items-center gap-x-8 text-sm font-bold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">
                    <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors">{{ __('Home') }}</a>
                    <a href="{{ route('posts.index') }}" wire:navigate class="hover:text-primary transition-colors">{{ __('Blog') }}</a>
                    <a href="{{ route('projects.index') }}" wire:navigate class="hover:text-primary transition-colors">{{ __('Projects') }}</a>
                </nav>

                <div class="h-4 w-px bg-neutral-200 dark:bg-neutral-800 hidden md:block"></div>

                <div class="flex items-center gap-x-2">
                    <x-ui.button variant="ghost" size="sm" class="h-9 w-9 p-0 rounded-full hover:bg-neutral-200 dark:hover:bg-neutral-900 text-neutral-500 dark:text-neutral-400">
                        <x-ui.icon name="magnifying-glass" class="size-4" />
                    </x-ui.button>

                    <x-ui.theme-switcher variant="inline" />
                </div>

                {{-- Nav dropdown --}}
                <x-ui.dropdown position="bottom-end">
                    <x-slot:button>
                        <x-ui.button variant="primary" size="sm" class="rounded-full px-6">
                            {{ __('Menu') }}
                        </x-ui.button>
                    </x-slot:button>

                    <x-slot:menu class="w-52 bg-white dark:bg-neutral-900 border-neutral-200 dark:border-neutral-800 text-neutral-950 dark:text-white">
                        <x-ui.dropdown.item :href="route('home')" icon="home" wire:navigate>
                            {{ __('Home') }}
                        </x-ui.dropdown.item>
                        <x-ui.dropdown.item :href="route('posts.index')" icon="document-text" wire:navigate>
                            {{ __('Blog') }}
                        </x-ui.dropdown.item>
                        <x-ui.dropdown.item :href="route('projects.index')" icon="folder" wire:navigate>
                            {{ __('Projects') }}
                        </x-ui.dropdown.item>
                        <x-ui.dropdown.separator class="bg-neutral-200 dark:bg-neutral-800" />
                        @auth
                            <x-ui.dropdown.item :href="route('dashboard')" icon="squares-2x2" wire:navigate>
                                {{ __('Dashboard') }}
                            </x-ui.dropdown.item>
                        @else
                            <x-ui.dropdown.item :href="route('login')" icon="arrow-right-on-rectangle">
                                {{ __('Sign In') }}
                            </x-ui.dropdown.item>
                        @endauth
                    </x-slot:menu>
                </x-ui.dropdown>
            </div>
        </x-ui.layout.header>

        {{ $slot }}
    </body>
</html>

