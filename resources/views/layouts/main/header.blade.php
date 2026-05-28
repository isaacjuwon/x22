<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="$theme.currentTheme === 'dark' ? 'dark' : ''">
    <head>
        @include('partials.head')
        {{ $head ?? '' }}
    </head>
    <body class="min-h-screen bg-neutral-50 text-neutral-900 dark:text-neutral-500 antialiased transition-colors duration-300">
        @php
            use App\Settings\GeneralSettings;
            $general = app(GeneralSettings::class);
        @endphp
        
        <x-ui.layout.header data-slot="header" class="border-b bg-white/80 dark:bg-[#141414]/80 backdrop-blur-xl sticky top-0 z-50 transition-colors duration-300">
            {{-- Brand --}}
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-4 transition-opacity hover:opacity-80">
                <span class="text-xl font-black tracking-tighter text-neutral-900 dark:text-neutral-50 uppercase">{{ $general->site_name }}</span>
            </a>

            <div class="ml-auto flex items-center gap-x-8">
                <nav class="hidden md:flex items-center gap-x-10 text-[10px] font-black uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-400">
                    <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors {{ request()->routeIs('home') ? 'text-primary' : '' }}">{{ __('Home') }}</a>
                    <a href="{{ route('posts.index') }}" wire:navigate class="hover:text-primary transition-colors {{ request()->routeIs('posts.*') ? 'text-primary' : '' }}">{{ __('Blog') }}</a>
                    <a href="{{ route('projects.index') }}" wire:navigate class="hover:text-primary transition-colors {{ request()->routeIs('projects.*') ? 'text-primary' : '' }}">{{ __('Projects') }}</a>
                </nav>

                <div class="h-4 w-px bg-neutral-200 dark:bg-neutral-800 hidden md:block"></div>

                <div class="flex items-center gap-x-4">
                    <x-ui.theme-switcher variant="inline" />

                    {{-- Nav dropdown --}}
                    <x-ui.dropdown position="bottom-end">
                        <x-slot:button>
                            <x-ui.button variant="primary" size="sm" class="rounded-full px-6 uppercase tracking-widest text-[10px] font-black">
                                {{ __('Menu') }}
                            </x-ui.button>
                        </x-slot:button>

                        <x-slot:menu class="w-64 bg-white dark:bg-neutral-900 border-neutral-200 dark:border-neutral-800">
                            <x-ui.dropdown.item :href="route('home')" icon="home" wire:navigate class="text-xs uppercase font-bold tracking-widest">
                                {{ __('Home') }}
                            </x-ui.dropdown.item>
                            <x-ui.dropdown.item :href="route('posts.index')" icon="document-text" wire:navigate class="text-xs uppercase font-bold tracking-widest">
                                {{ __('Blog') }}
                            </x-ui.dropdown.item>
                            <x-ui.dropdown.item :href="route('projects.index')" icon="folder" wire:navigate class="text-xs uppercase font-bold tracking-widest">
                                {{ __('Projects') }}
                            </x-ui.dropdown.item>
                            <x-ui.dropdown.separator class="bg-neutral-100 dark:bg-neutral-800" />
                            @auth
                                <x-ui.dropdown.item :href="route('dashboard')" icon="squares-2x2" wire:navigate class="text-xs uppercase font-bold tracking-widest">
                                    {{ __('Dashboard') }}
                                </x-ui.dropdown.item>
                            @else
                                <x-ui.dropdown.item :href="route('login')" icon="arrow-right-on-rectangle" class="text-xs uppercase font-bold tracking-widest">
                                    {{ __('Sign In') }}
                                </x-ui.dropdown.item>
                            @endauth
                        </x-slot:menu>
                    </x-ui.dropdown>
                </div>
            </div>
        </x-ui.layout.header>

        {{ $slot }}
