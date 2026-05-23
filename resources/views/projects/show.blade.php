@php
    use App\Models\Project;
    use App\Enums\ProjectStatus;

    if (is_string($project)) {
        $project = Project::findOrFail($project);
    }
    abort_if($project->status === ProjectStatus::Draft, 404);
    $project->load('user', 'testimonials.user', 'teamMembers');
    $relatedProjects = Project::where('status', ProjectStatus::Completed)
        ->where('id', '!=', $project->id)
        ->when($project->category, fn ($q) => $q->where('category', $project->category))
        ->latest()
        ->limit(3)
        ->get();
@endphp

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

        @if ($relatedProjects->isNotEmpty())
            <section class="mt-16 border-t border-neutral-800 pt-12">
                <x-ui.heading level="h2" size="lg" class="mb-6">{{ __('Related Projects') }}</x-ui.heading>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedProjects as $relatedProject)
                        <x-landing.project :project="$relatedProject" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts::main>
