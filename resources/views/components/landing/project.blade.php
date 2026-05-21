@props(['project'])

<article data-slot="card" class="group flex flex-col p-6 transition hover:border-green-800">
    <div class="mb-4 flex items-start justify-between gap-2">
        <x-ui.badge color="green">{{ __('Completed') }}</x-ui.badge>
        @isset($project->category)
            <x-ui.badge color="neutral" variant="outline">{{ $project->category }}</x-ui.badge>
        @endisset
    </div>

    <x-ui.heading level="h3" size="md" class="mb-2 text-neutral-700 dark:text-neutral-100 group-hover:text-green-400">
        {{ $project->title }}
    </x-ui.heading>

    @isset($project->description)
        <x-ui.text class="line-clamp-3 flex-1 text-sm text-neutral-600 dark:text-neutral-500">
            {{ $project->description }}
        </x-ui.text>
    @endisset
</article>
