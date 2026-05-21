<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="$theme.currentTheme === 'dark' ? 'dark' : ''">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen">
        <x-ui.layout>
            <x-ui.sidebar>
                <x-slot:brand>
                    <x-ui.brand name="{{ config('app.name') }}" :href="route('dashboard')" wire:navigate>
                        <x-slot:logo>
                            <x-app-logo-icon class="size-5" />
                        </x-slot:logo>
                    </x-ui.brand>
                </x-slot:brand>

                <x-ui.navlist>

                    {{-- Platform --}}
                    <x-ui.navlist.group label="{{ __('Platform') }}">
                        <x-ui.navlist.item
                            label="{{ __('Dashboard') }}"
                            icon="home"
                            :href="route('dashboard')"
                            :active="request()->routeIs('dashboard')"
                            wire:navigate
                        />
                    </x-ui.navlist.group>

                    {{-- Content --}}
                    <x-ui.navlist.group label="{{ __('Content') }}">
                        <x-ui.navlist.item
                            label="{{ __('Posts') }}"
                            icon="document-text"
                            :href="route('admin.posts.index')"
                            :active="request()->routeIs('admin.posts.*')"
                            wire:navigate
                        />
                        <x-ui.navlist.item
                            label="{{ __('Pages') }}"
                            icon="document"
                            :href="route('admin.pages.index')"
                            :active="request()->routeIs('admin.pages.*')"
                            wire:navigate
                        />
                    </x-ui.navlist.group>

                    {{-- Portfolio --}}
                    <x-ui.navlist.group label="{{ __('Portfolio') }}">
                        <x-ui.navlist.item
                            label="{{ __('Projects') }}"
                            icon="folder-open"
                            :href="route('admin.projects.index')"
                            :active="request()->routeIs('admin.projects.*')"
                            wire:navigate
                        />
                        <x-ui.navlist.item
                            label="{{ __('Team') }}"
                            icon="users"
                            :href="route('admin.team.index')"
                            :active="request()->routeIs('admin.team.*')"
                            wire:navigate
                        />
                        <x-ui.navlist.item
                            label="{{ __('Testimonials') }}"
                            icon="star"
                            :href="route('admin.testimonials.index')"
                            :active="request()->routeIs('admin.testimonials.*')"
                            wire:navigate
                        />
                    </x-ui.navlist.group>

                    {{-- Settings --}}
                    <x-ui.navlist.group label="{{ __('Settings') }}">
                        <x-ui.navlist.item
                            label="{{ __('Site Settings') }}"
                            icon="cog-6-tooth"
                            :href="route('admin.settings.general')"
                            :active="request()->routeIs('admin.settings.*')"
                            wire:navigate
                        />
                        <x-ui.navlist.item
                            label="{{ __('Profile') }}"
                            icon="user"
                            :href="route('profile.edit')"
                            :active="request()->routeIs('profile.edit')"
                            wire:navigate
                        />
                        <x-ui.navlist.item
                            label="{{ __('Security') }}"
                            icon="lock-closed"
                            :href="route('security.edit')"
                            :active="request()->routeIs('security.edit')"
                            wire:navigate
                        />
                        <x-ui.navlist.item
                            label="{{ __('Appearance') }}"
                            icon="paint-brush"
                            :href="route('appearance.edit')"
                            :active="request()->routeIs('appearance.edit')"
                            wire:navigate
                        />
                    </x-ui.navlist.group>

                </x-ui.navlist>

                <x-ui.sidebar.push />

                {{-- User menu --}}
                <x-ui.dropdown portal>
                    <x-slot:button>
                        <x-ui.navlist.item
                            :label="auth()->user()->name"
                            icon="user-circle"
                            class="w-full"
                        />
                    </x-slot:button>

                    <x-slot:menu class="!w-[14rem]">
                        <x-ui.dropdown.group :label="auth()->user()->email" />

                        <x-ui.dropdown.separator />

                        <x-ui.dropdown.item :href="route('profile.edit')" icon="user" wire:navigate>
                            {{ __('Profile') }}
                        </x-ui.dropdown.item>
                        <x-ui.dropdown.item :href="route('security.edit')" icon="lock-closed" wire:navigate>
                            {{ __('Security') }}
                        </x-ui.dropdown.item>
                        <x-ui.dropdown.item :href="route('appearance.edit')" icon="paint-brush" wire:navigate>
                            {{ __('Appearance') }}
                        </x-ui.dropdown.item>

                        <x-ui.dropdown.separator />

                        <form method="POST" action="{{ route('logout') }}" class="contents">
                            @csrf
                            <x-ui.dropdown.item as="button" type="submit" icon="arrow-right-start-on-rectangle">
                                {{ __('Sign Out') }}
                            </x-ui.dropdown.item>
                        </form>
                    </x-slot:menu>
                </x-ui.dropdown>

            </x-ui.sidebar>

            <x-ui.layout.main>
                <x-ui.layout.header>
                    <x-ui.sidebar.toggle class="md:hidden" />

                    <x-ui.navbar class="flex-1 hidden lg:flex">
                        <x-ui.navbar.item icon="home" label="{{ __('Home') }}" :href="route('home')" wire:navigate />
                        <x-ui.navbar.item icon="globe-alt" label="{{ __('View Site') }}" :href="route('home')" target="_blank" />
                    </x-ui.navbar>

                    <div class="flex ml-auto gap-x-3 items-center">
                        <x-ui.theme-switcher variant="dropdown" />
                    </div>
                </x-ui.layout.header>

                {{ $slot }}
            </x-ui.layout.main>
        </x-ui.layout>

        @persist('toast')
            <x-ui.toast />
        @endpersist

    </body>
</html>
