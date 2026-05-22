<x-ui.dropdown position="bottom" align="start">
    <x-ui.sidebar.profile
        :name="auth()->user()->name"
        :initials="auth()->user()->initials()"
        icon:trailing="chevrons-up-down"
        data-test="sidebar-menu-button"
    />

    <x-ui.menu>
        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
            <x-ui.avatar
                :name="auth()->user()->name"
                :initials="auth()->user()->initials()"
            />
            <div class="grid flex-1 text-start text-sm leading-tight">
                <x-ui.heading class="truncate">{{ auth()->user()->name }}</x-ui.heading>
                <x-ui.text class="truncate">{{ auth()->user()->email }}</x-ui.text>
            </div>
        </div>
        <x-ui.menu.separator />
        <x-ui.menu.radio.group>
            <x-ui.menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                {{ __('Settings') }}
            </x-ui.menu.item>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <x-ui.menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer"
                    data-test="logout-button"
                >
                    {{ __('Log out') }}
                </x-ui.menu.item>
            </form>
        </x-ui.menu.radio.group>
    </x-ui.menu>
</x-ui.dropdown>
