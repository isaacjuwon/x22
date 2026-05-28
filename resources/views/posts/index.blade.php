<x-layouts::main title="{{ __('Blog') }}">
    <div class="flex min-h-screen ide-grid font-mono transition-colors duration-300 bg-neutral-50 dark:bg-[#0a0a0a]">
        <div class="container mx-auto px-6 py-20 space-y-16">
            
            {{-- Header: Breadcrumbs & Title --}}
            <div class="space-y-6">
                <nav class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-[0.3em] text-neutral-400">
                    <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors">~/root</a>
                    <span class="opacity-30">/</span>
                    <span class="text-primary">insights</span>
                </nav>
                
                <div class="space-y-2">
                    <h1 class="text-5xl lg:text-8xl font-black tracking-tighter text-neutral-900 dark:text-white uppercase leading-none">
                        LOG<span class="text-primary">.</span>DUMP
                    </h1>
                    <p class="text-neutral-500 dark:text-neutral-400 text-sm font-bold uppercase tracking-widest max-w-xl border-l-2 border-primary/20 pl-4">
                        // Decrypting 20+ years of architectural patterns, technical debt, and engineering excellence.
                    </p>
                </div>
            </div>

            <div class="h-px w-full bg-neutral-200 dark:bg-neutral-800"></div>

            {{-- Livewire List --}}
            <div class="grid gap-px bg-neutral-200 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-800">
                <livewire:posts.list />
            </div>

        </div>
    </div>
</x-layouts::main>
