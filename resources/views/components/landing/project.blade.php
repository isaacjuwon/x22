@props(['project'])

<article data-slot="card" class="group relative flex flex-col overflow-hidden p-0 transition-all duration-300 hover:border-primary hover:shadow-xl">

    {{-- Content --}}
    <div class="flex flex-1 flex-col gap-4 p-6">
        <div class="flex items-center gap-2 mb-1">
            <x-ui.badge color="primary" size="sm" variant="soft">{{ __('Project') }}</x-ui.badge>
            @isset($project->category)
                <x-ui.badge color="neutral" variant="outline" size="sm">{{ $project->category }}</x-ui.badge>
            @endisset
        </div>

        <x-ui.heading level="h3" size="md" class="group-hover:text-primary transition-colors font-bold">
            <a href="{{ route('projects.show', $project) }}" wire:navigate class="stretched-link">
                {{ $project->title }}
            </a>
        </x-ui.heading>

        @isset($project->description)
            <x-ui.text class="line-clamp-3 flex-1 text-sm leading-relaxed text-neutral-500 dark:text-neutral-400">
                {{ $project->description }}
            </x-ui.text>
        @endisset

        {{-- Modern footer --}}
        <div class="mt-2 flex items-center justify-between">
            <x-ui.text class="text-xs font-bold uppercase tracking-widest text-primary">
                {{ __('Explore') }}
            </x-ui.text>
            <x-ui.icon name="arrow-right" class="h-4 w-4 text-primary transition-transform group-hover:translate-x-1" />
        </div>
    </div>
</article>
