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
    <section class="px-6 py-24 text-center bg-black">
        <div class="mx-auto max-w-4xl space-y-6">
            <h1 class="text-6xl font-bold tracking-tighter text-white uppercase lg:text-8xl">
                {{ __('Portfolio') }}
            </h1>
            <p class="text-xl text-neutral-400 font-medium leading-relaxed max-w-2xl mx-auto">
                {{ __('A curated collection of digital experiences, crafted with precision and engineered for performance.') }}
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-6 py-12 space-y-12 bg-black min-h-screen">

        {{-- Filters --}}
        <form method="GET" action="{{ route('projects.index') }}" class="flex flex-wrap items-center gap-4 p-4 rounded-3xl bg-neutral-900/50 border border-neutral-800">

            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <x-ui.input
                    name="search"
                    :value="request('search')"
                    placeholder="{{ __('Search projects...') }}"
                    class="bg-black border-neutral-800 rounded-full px-6 text-white"
                />
            </div>

            {{-- Category filter --}}
            @if ($categories->isNotEmpty())
                <x-ui.select name="category" class="w-48 bg-black border-neutral-800 rounded-full text-white">
                    <x-ui.select.option value="">{{ __('All Categories') }}</x-ui.select.option>
                    @foreach ($categories as $cat)
                        <x-ui.select.option :value="$cat" :selected="request('category') === $cat">
                            {{ $cat }}
                        </x-ui.select.option>
                    @endforeach
                </x-ui.select>
            @endif

            <x-ui.button type="submit" variant="primary" class="rounded-full px-8">
                {{ __('Filter') }}
            </x-ui.button>

            @if (request('search') || request('category'))
                <x-ui.button as="a" :href="route('projects.index')" variant="ghost" class="text-neutral-500 hover:text-white">
                    {{ __('Clear') }}
                </x-ui.button>
            @endif

        </form>

        {{-- Results --}}
        @if ($projects->isEmpty())
            <div class="py-32 border border-dashed border-neutral-800 rounded-[3rem] text-center space-y-4">
                <x-ui.icon name="folder-open" class="h-12 w-12 mx-auto text-neutral-800" />
                <p class="text-neutral-600 font-bold uppercase tracking-widest text-sm">{{ __('No projects found.') }}</p>
            </div>
        @else
            <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-landing.project :$project />
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($projects->hasPages())
                <div class="flex justify-center pt-12">
                    {{ $projects->withQueryString()->links() }}
                </div>
            @endif
        @endif

    </div>


</x-layouts::main>
