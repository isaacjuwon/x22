@props(['project'])

<article data-slot="card" class="group relative flex flex-col border border-neutral-200 dark:border-neutral-800 transition-colors hover:border-primary">

    {{-- Content --}}
    <div class="flex flex-1 flex-col gap-4 p-8">
        <div class="flex items-center gap-3">
            <span class="term-dot term-dot-success text-[10px] uppercase tracking-[0.2em] text-neutral-400">{{ __('Deployed') }}</span>
            @isset($project->category)
                <span class="opacity-30 text-neutral-400">|</span>
                <span class="text-[10px] uppercase tracking-[0.2em] text-neutral-400">{{ $project->category }}</span>
            @endisset
        </div>

        <x-ui.heading level="h3" size="md" class="group-hover:text-primary transition-colors font-bold text-2xl leading-tight">
            <a href="{{ route('projects.show', $project) }}" wire:navigate class="stretched-link">
                {{ $project->title }}
            </a>
        </x-ui.heading>

        @isset($project->description)
            <p class="line-clamp-3 flex-1 text-sm leading-relaxed text-neutral-500 dark:text-neutral-400">
                {{ $project->description }}
            </p>
        @endisset

        {{-- Modern footer --}}
        <div class="mt-6 pt-6 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
            <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-primary">
                {{ __('Explore Project') }}
            </span>
            <x-ui.icon name="arrow-right" class="size-4 text-primary transition-transform group-hover:translate-x-1" />
        </div>
    </div>
</article>
