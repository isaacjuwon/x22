@props(['project'])

<article data-slot="card" class="group relative flex flex-col border border-neutral-200 dark:border-neutral-800 transition-all hover:border-primary pt-6">

    {{-- Content --}}
    <div class="flex flex-1 flex-col gap-5 p-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <x-ui.icon name="ps:browser" class="size-4 text-primary" />
                <span class="text-[9px] uppercase tracking-[0.2em] text-neutral-400 font-bold">{{ $project->category ?? __('Production') }}</span>
            </div>
            <span class="term-indicator text-success text-[8px] uppercase font-bold tracking-widest">{{ __('Live') }}</span>
        </div>

        <x-ui.heading level="h3" size="md" class="group-hover:text-primary transition-colors font-bold text-2xl leading-tight uppercase tracking-tighter">
            <a href="{{ route('projects.show', $project) }}" wire:navigate class="stretched-link">
                {{ $project->title }}
            </a>
        </x-ui.heading>

        @isset($project->description)
            <p class="line-clamp-3 flex-1 text-xs leading-relaxed text-neutral-500 dark:text-neutral-500 font-medium">
                {{ $project->description }}
            </p>
        @endisset

        {{-- Modern footer --}}
        <div class="mt-6 pt-6 border-t border-neutral-100 dark:border-neutral-900 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-[9px] font-bold uppercase tracking-[0.3em] text-primary transition-all group-hover:tracking-[0.4em]">
                    {{ __('Initialize Project') }}
                </span>
            </div>
            <x-ui.icon name="ps:terminal" class="size-4 text-neutral-300 group-hover:text-primary transition-all" />
        </div>
    </div>
</article>
