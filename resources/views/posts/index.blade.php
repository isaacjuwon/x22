<x-layouts::main title="{{ __('Blog') }}">
    <div class="container mx-auto px-6 py-20 bg-black min-h-screen">
        <div class="mb-16 space-y-6">
            <nav class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-500">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors">{{ __('Home') }}</a>
                <span class="text-neutral-800">/</span>
                <span class="text-white">{{ __('Blog') }}</span>
            </nav>
            <h1 class="text-5xl font-bold tracking-tighter text-white uppercase lg:text-7xl">
                {{ __('Insights & Articles') }}
            </h1>
            <p class="text-lg text-neutral-400 font-medium max-w-2xl leading-relaxed">
                {{ __('A collection of thoughts, tutorials, and insights on design, development, and building digital products.') }}
            </p>
        </div>

        <livewire:posts.list />
    </div>
</x-layouts::main>

