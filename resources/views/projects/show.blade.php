<x-layouts::main :title="$project->title">
    <div class="mx-auto max-w-4xl px-6 py-16">
        <div class="mb-8 space-y-4">
            <x-ui.link href="{{ route('projects.index') }}" wire:navigate class="text-sm text-neutral-600 dark:text-neutral-500 hover:text-green-600 dark:hover:text-green-400">
                &larr; {{ __('Back to Projects') }}
            </x-ui.link>

            <div class="flex items-center gap-2">
                <x-ui.badge color="green">{{ __('Completed') }}</x-ui.badge>
                @isset($project->category)
                    <x-ui.badge color="neutral" variant="outline">{{ $project->category }}</x-ui.badge>
                @endisset
            </div>

            <x-ui.heading level="h1" size="2xl" class="text-neutral-800 dark:text-neutral-100">
                {{ $project->title }}
            </x-ui.heading>
        </div>

        <div class="prose prose-neutral dark:prose-invert max-w-none">
            {{ $project->description }}
        </div>
    </div>
</x-layouts::main>
