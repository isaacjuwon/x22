@php
    use App\Enums\ProjectStatus;
    use App\Models\Project;

    $categories = Project::where('status', ProjectStatus::Completed)
        ->whereNotNull('category')
        ->distinct()
        ->pluck('category')
        ->sort()
        ->values();

    $projects = Project::where('status', ProjectStatus::Completed)
        ->when(request('category'), fn ($q) => $q->where('category', request('category')))
        ->when(request('search'), fn ($q) => $q->where(function ($q) {
            $q->where('title', 'like', '%' . request('search') . '%')
              ->orWhere('description', 'like', '%' . request('search') . '%');
        }))
        ->latest()
        ->paginate(12);
@endphp

<x-layouts::main :title="__('Projects')">

    {{-- Header --}}
    <section class="px-6 py-16 text-center">
        <div class="mx-auto max-w-2xl space-y-4">
            <x-ui.heading level="h1" size="xl" class="text-4xl font-bold text-neutral-950">
                {{ __('Projects') }}
            </x-ui.heading>
            <x-ui.text class="text-lg text-neutral-500 font-medium">
                {{ __('A collection of work we\'re proud of.') }}
            </x-ui.text>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-6 py-12 space-y-8">

        {{-- Filters --}}
        <form method="GET" action="{{ route('projects.index') }}" class="flex flex-wrap items-center gap-3">

            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <x-ui.input
                    name="search"
                    :value="request('search')"
                    placeholder="{{ __('Search projects...') }}"
                />
            </div>

            {{-- Category filter --}}
            @if ($categories->isNotEmpty())
                <x-ui.select name="category" class="w-48">
                    <x-ui.select.option value="">{{ __('All categories') }}</x-ui.select.option>
                    @foreach ($categories as $cat)
                        <x-ui.select.option :value="$cat" :selected="request('category') === $cat">
                            {{ $cat }}
                        </x-ui.select.option>
                    @endforeach
                </x-ui.select>
            @endif

            <x-ui.button type="submit" variant="primary">
                {{ __('Filter') }}
            </x-ui.button>

            @if (request('search') || request('category'))
                <x-ui.button as="a" :href="route('projects.index')" variant="ghost">
                    {{ __('Clear') }}
                </x-ui.button>
            @endif

        </form>

        {{-- Results --}}
        @if ($projects->isEmpty())
            <x-ui.empty class="py-16">
                <x-ui.empty.media>
                    <x-ui.icon name="folder-open" class="h-10 w-10 text-neutral-400 dark:text-neutral-600" />
                </x-ui.empty.media>
                <x-ui.empty.contents>
                    <x-ui.text class="text-neutral-500">{{ __('No projects found.') }}</x-ui.text>
                </x-ui.empty.contents>
            </x-ui.empty>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-landing.project :$project />
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($projects->hasPages())
                <div class="flex justify-center pt-4">
                    {{ $projects->withQueryString()->links() }}
                </div>
            @endif
        @endif

    </div>

</x-layouts::main>
