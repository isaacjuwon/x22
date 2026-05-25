@props(['project'])

<article data-slot="card" class="group relative flex flex-col overflow-hidden p-0 transition-all duration-200 hover:border-green-700 dark:hover:border-green-600">

    {{-- Content --}}
    <div class="flex flex-1 flex-col gap-3 p-5">
        <div class="flex items-center gap-2 mb-1">
            <x-ui.badge color="green" size="sm" variant="outline">{{ __('Live') }}</x-ui.badge>
            @isset($project->category)
                <x-ui.badge color="neutral" variant="outline" size="sm">{{ $project->category }}</x-ui.badge>
            @endisset
        </div>

        <x-ui.heading level="h3" size="md" class="term-prompt group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
            <a href="{{ route('projects.show', $project) }}" wire:navigate class="stretched-link">
                {{ $project->title }}
            </a>
        </x-ui.heading>

        @isset($project->description)
            <x-ui.text class="line-clamp-3 flex-1 text-sm leading-relaxed">
                {{ $project->description }}
            </x-ui.text>
        @endisset

        {{-- Prompt-style footer --}}
        <div class="mt-2 flex items-center gap-1.5">
            <x-ui.icon name="chevron-right" class="h-3.5 w-3.5 text-green-500" />
            <x-ui.text class="text-xs font-medium text-green-600 dark:text-green-400">
                {{ __('view project') }}
            </x-ui.text>
        </div>
    </div>
</article>
