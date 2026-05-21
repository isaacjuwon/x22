<div class="flex items-start max-md:flex-col">
    {{-- Sidebar nav --}}
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <x-ui.navlist>
            <x-ui.navlist.item
                label="General"
                icon="cog-6-tooth"
                :href="route('admin.settings.general')"
                :active="request()->routeIs('admin.settings.general')"
                wire:navigate
            />
            <x-ui.navlist.item
                label="SEO"
                icon="magnifying-glass"
                :href="route('admin.settings.seo')"
                :active="request()->routeIs('admin.settings.seo')"
                wire:navigate
            />
            <x-ui.navlist.item
                label="Social"
                icon="share"
                :href="route('admin.settings.social')"
                :active="request()->routeIs('admin.settings.social')"
                wire:navigate
            />
        </x-ui.navlist>
    </div>

    <x-ui.separator class="md:hidden" />

    {{-- Content --}}
    <div class="flex-1 self-stretch max-md:pt-6">
        <x-ui.heading level="h2" size="lg">{{ $heading ?? '' }}</x-ui.heading>
        <x-ui.text class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $subheading ?? '' }}</x-ui.text>

        <div class="mt-6 w-full max-w-2xl">
            {{ $slot }}
        </div>
    </div>
</div>
