<x-layouts::main title="{{ __('Blog') }}">
    <div class="container mx-auto px-6 py-20">
        <div class="mb-16 space-y-4">
            <nav class="flex items-center gap-2 text-skeleton-meta">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-primary">{{ __('Home') }}</a>
                <span class="text-neutral-300">/</span>
                <span class="text-neutral-900 font-bold">{{ __('Blog') }}</span>
            </nav>
            <x-ui.heading level="h1" size="2xl" class="text-3xl font-bold tracking-tight text-neutral-950 leading-tight">
                {{ __('Insights & Articles') }}
            </x-ui.heading>
            <p class="text-lg text-neutral-500 font-medium max-w-2xl">
                {{ __('A collection of thoughts, tutorials, and insights on design, development, and building digital products.') }}
            </p>
        </div>

        <livewire:posts.list />
    </div>
</x-layouts::main>
