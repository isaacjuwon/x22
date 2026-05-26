@php
    use App\Enums\ProjectStatus;
    use App\Enums\TestimonialStatus;
    use App\Models\Project;
    use App\Models\Testimonial;
    use App\Settings\GeneralSettings;

    $general = app(GeneralSettings::class);

    $projects = Project::where('status', ProjectStatus::Completed)
        ->latest()
        ->limit(6)
        ->get();

    $testimonials = Testimonial::where('status', TestimonialStatus::Approved)
        ->with('user', 'project')
        ->latest()
        ->limit(6)
        ->get();
@endphp

<x-layouts::main :title="$general->site_name">

    {{-- ─── Hero ──────────────────────────────────────────────────────────── --}}
    {{-- <section class="px-6 py-40 border-b border-neutral-200 dark:border-neutral-200 bg-neutral-50">
        <div class="mx-auto max-w-5xl text-center space-y-12">
            <div class="space-y-8">
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full border border-neutral-200 bg-neutral-100 text-skeleton-meta">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    {{ __('Available for new projects') }}
                </div>
                
                <h1 class="text-7xl font-bold tracking-tight text-neutral-950 sm:text-9xl leading-[1.05]">
                    {{ $general->hero_title }}
                </h1>
            </div>

            <p class="mx-auto max-w-2xl text-2xl text-neutral-500 font-medium leading-relaxed tracking-tight">
                {{ $general->hero_subtitle }}
            </p>

            <div class="flex flex-wrap items-center justify-center gap-6 pt-4">
                @if ($general->show_projects_section)
                    <x-ui.button as="a" href="#projects" variant="primary" size="lg" class="px-10 py-4 text-base">
                        {{ __('View Portfolio') }}
                    </x-ui.button>
                @endif
                @if ($general->show_posts_section)
                    <x-ui.button as="a" href="#blog" variant="outline" size="lg" class="px-10 py-4 text-base">
                        {{ __('Read Blog') }}
                    </x-ui.button>
                @endif
            </div>
        </div>
    </section> --}}

    {{-- ─── Selected Projects ────────────────────────────────────────────── --}}
    @if ($general->show_projects_section)
        <section id="projects" class="px-6 py-32 bg-neutral-50">
            <div class="mx-auto max-w-6xl">
                <div class="mb-20 flex items-end justify-between pb-10">
                    <div class="space-y-3">
                        <p class="text-skeleton-meta">{{ __('Selected Work') }}</p>
                        <x-ui.heading level="h2" size="xl" class="text-3xl font-bold tracking-tight text-neutral-950">{{ __('Portfolio') }}</x-ui.heading>
                    </div>
                    <x-ui.button as="a" href="{{ route('projects.index') }}" wire:navigate variant="outline" size="sm" class="rounded-full">
                        {{ __('View All Projects') }}
                    </x-ui.button>
                </div>

                @if ($projects->isEmpty())
                    <div class="py-32 border border-dashed border-neutral-200 rounded-[3rem] text-center space-y-4">
                        <x-ui.icon name="ps:folder-open" class="h-12 w-12 mx-auto text-neutral-200" />
                        <p class="text-neutral-400 font-medium text-lg">{{ __('No projects shared yet.') }}</p>
                    </div>
                @else
                    <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($projects as $project)
                            <x-landing.project :$project />
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ─── Latest Insights ──────────────────────────────────────────────── --}}
    @if ($general->show_posts_section)
        <section id="blog" class="px-6 py-32 bg-neutral-50">
            <div class="mx-auto max-w-6xl">
                <div class="mb-20 flex items-end justify-between pb-10">
                    <div class="space-y-3">
                        <p class="text-skeleton-meta">{{ __('Latest Insights') }}</p>
                        <x-ui.heading level="h2" size="xl" class="text-3xl font-bold tracking-tight text-neutral-950">{{ __('Blog & Articles') }}</x-ui.heading>
                    </div>
                    <x-ui.button as="a" href="{{ route('posts.index') }}" wire:navigate variant="outline" size="sm" class="rounded-full">
                        {{ __('Read All Articles') }}
                    </x-ui.button>
                </div>

                <div class="grid gap-12">
                    <livewire:landing.posts />
                </div>
            </div>
        </section>
    @endif

</x-layouts::main>
