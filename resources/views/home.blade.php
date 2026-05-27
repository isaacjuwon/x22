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
    <section class="px-6 py-40 bg-neutral-50 dark:bg-black transition-colors duration-300">
        <div class="mx-auto max-w-6xl flex flex-col items-center text-center gap-12">
            <div class="space-y-8 flex flex-col items-center">
                <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full border border-primary/30 bg-primary/5 text-[10px] font-bold uppercase tracking-widest text-primary">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    {{ __('Available for new projects') }}
                </div>
                
                <h1 class="text-6xl font-bold tracking-tighter text-neutral-950 dark:text-white sm:text-8xl lg:text-9xl leading-[0.9] uppercase">
                    {{ $general->hero_title }}
                </h1>
            </div>

            <p class="mx-auto max-w-2xl text-xl text-neutral-500 dark:text-neutral-400 font-medium leading-relaxed tracking-tight">
                {{ $general->hero_subtitle }}
            </p>

            <div class="flex flex-wrap items-center justify-center gap-6 pt-4">
                @if ($general->show_projects_section)
                    <x-ui.button as="a" href="#projects" variant="primary" size="lg" class="px-12 py-5 text-sm uppercase tracking-widest font-bold rounded-full">
                        {{ __('View Portfolio') }}
                    </x-ui.button>
                @endif
                @if ($general->show_posts_section)
                    <x-ui.button as="a" href="#blog" variant="outline" size="lg" class="px-12 py-5 text-sm uppercase tracking-widest font-bold rounded-full border-neutral-200 dark:border-neutral-800 text-neutral-950 dark:text-white hover:border-primary">
                        {{ __('Read Blog') }}
                    </x-ui.button>
                @endif
            </div>
        </div>
    </section>

    {{-- ─── Selected Projects ────────────────────────────────────────────── --}}
    @if ($general->show_projects_section)
        <section id="projects" class="px-6 py-32 bg-neutral-50 dark:bg-black transition-colors duration-300">
            <div class="mx-auto max-w-6xl">
                <div class="mb-20 flex items-end justify-between border-b border-neutral-200 dark:border-neutral-900 pb-10">
                    <div class="space-y-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary/70">{{ __('Selected Work') }}</p>
                        <h2 class="text-4xl font-bold tracking-tighter text-neutral-950 dark:text-white uppercase">{{ __('Portfolio') }}</h2>
                    </div>
                    <x-ui.button as="a" href="{{ route('projects.index') }}" wire:navigate variant="outline" size="sm" class="rounded-full border-neutral-200 dark:border-neutral-800 text-neutral-500 hover:text-neutral-950 dark:hover:text-white hover:border-primary">
                        {{ __('View All Projects') }}
                    </x-ui.button>
                </div>

                @if ($projects->isEmpty())
                    <div class="py-32 border border-dashed border-neutral-200 dark:border-neutral-800 rounded-[3rem] text-center space-y-4">
                        <x-ui.icon name="folder-open" class="h-12 w-12 mx-auto text-neutral-200 dark:text-neutral-800" />
                        <p class="text-neutral-400 dark:text-neutral-600 font-bold uppercase tracking-widest text-sm">{{ __('No projects shared yet.') }}</p>
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
        <section id="blog" class="px-6 py-32 bg-neutral-50 dark:bg-black transition-colors duration-300">
            <div class="mx-auto max-w-6xl">
                <div class="mb-20 flex items-end justify-between border-b border-neutral-200 dark:border-neutral-900 pb-10">
                    <div class="space-y-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary/70">{{ __('Latest Insights') }}</p>
                        <h2 class="text-4xl font-bold tracking-tighter text-neutral-950 dark:text-white uppercase">{{ __('Blog & Articles') }}</h2>
                    </div>
                    <x-ui.button as="a" href="{{ route('posts.index') }}" wire:navigate variant="outline" size="sm" class="rounded-full border-neutral-200 dark:border-neutral-800 text-neutral-500 hover:text-neutral-950 dark:hover:text-white hover:border-primary">
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
