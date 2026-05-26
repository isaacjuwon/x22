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
    <section class="relative border-b border-neutral-200 dark:border-neutral-800 px-6 py-28 overflow-hidden">
        <div class="mx-auto max-w-5xl">
            <div class="grid gap-12 lg:grid-cols-2 items-center">
                <div class="space-y-8">
                    <div class="space-y-2">
                        <x-ui.badge color="primary" variant="outline">{{ __('system stable') }}</x-ui.badge>
                        <h1 class="text-4xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50 sm:text-6xl">
                            {{ $general->hero_title }}
                        </h1>
                    </div>

                    <p class="text-lg leading-relaxed text-neutral-600 dark:text-neutral-400">
                        {{ $general->hero_subtitle }}
                    </p>

                    <div class="flex flex-wrap items-center gap-4">
                        @if ($general->show_posts_section)
                            <x-ui.button as="a" href="#posts" variant="primary" size="lg">
                                {{ __('view articles') }}
                            </x-ui.button>
                        @endif
                        @if ($general->show_projects_section)
                            <x-ui.button as="a" href="#projects" variant="outline" size="lg">
                                {{ __('our projects') }}
                            </x-ui.button>
                        @endif
                    </div>
                </div>

                <div class="term-block font-mono shadow-2xl scale-105 lg:scale-110">
                    <div class="flex items-center gap-2 mb-4 border-b border-neutral-800 pb-2">
                        <div class="h-2 w-2 rounded-full bg-neutral-800"></div>
                        <div class="h-2 w-2 rounded-full bg-neutral-800"></div>
                        <div class="h-2 w-2 rounded-full bg-neutral-800"></div>
                        <span class="ml-2 text-[10px] uppercase opacity-50">{{ __('Status Dashboard') }}</span>
                    </div>
                    <div class="space-y-3 text-sm">
                        <p class="term-prompt">{{ __('system --health') }}</p>
                        <p class="term-arrow text-green-400">{{ __('Uptime: 99.99%') }}</p>
                        <p class="term-arrow text-green-400">{{ __('Deployments: 1,240') }}</p>
                        <p class="term-arrow text-amber-400">{{ __('Pending Tasks: 2') }}</p>
                        <div class="pt-2 border-t border-neutral-800">
                            <p class="text-[10px] uppercase opacity-40 mb-1">{{ __('Latest Logs') }}</p>
                            <p class="text-xs text-neutral-500">2026-05-26 14:20:01 [INFO] Cache cleared</p>
                            <p class="text-xs text-neutral-500">2026-05-26 14:22:15 [SUCCESS] Build completed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── Latest Posts ───────────────────────────────────────────────────── --}}
    @if ($general->show_posts_section)
        <section id="posts" class="border-b border-neutral-200 dark:border-neutral-800 px-6 py-20">
            <div class="mx-auto max-w-6xl">
                <div class="mb-12 flex items-end justify-between">
                    <div class="space-y-1">
                        <p class="term-comment text-xs uppercase tracking-widest">{{ __('Recent Activity') }}</p>
                        <x-ui.heading level="h2" size="xl" class="text-3xl font-bold text-neutral-900 dark:text-neutral-50">{{ __('Latest Posts') }}</x-ui.heading>
                    </div>
                    <x-ui.button as="a" href="{{ route('posts.index') }}" wire:navigate variant="ghost" size="sm">
                        {{ __('all posts') }}
                    </x-ui.button>
                </div>

                <livewire:landing.posts />
            </div>
        </section>
    @endif

    {{-- ─── Projects ───────────────────────────────────────────────────────── --}}
    @if ($general->show_projects_section)
        <section id="projects" class="border-b border-neutral-200 dark:border-neutral-800 px-6 py-20">
            <div class="mx-auto max-w-6xl">
                <div class="mb-12 text-center space-y-2">
                    <p class="term-comment text-xs uppercase tracking-widest">{{ __('Case Studies') }}</p>
                    <x-ui.heading level="h2" size="xl" class="text-3xl font-bold text-neutral-900 dark:text-neutral-50">{{ __('Our Projects') }}</x-ui.heading>
                </div>

                @if ($projects->isEmpty())
                    <x-ui.empty class="term-block border-dashed py-16">
                        <x-ui.empty.media>
                            <x-ui.icon name="folder-open" class="h-10 w-10 text-neutral-400 dark:text-neutral-600" />
                        </x-ui.empty.media>
                        <x-ui.empty.contents>
                            <x-ui.text class="text-neutral-500 dark:text-neutral-500">{{ __('No projects yet.') }}</x-ui.text>
                        </x-ui.empty.contents>
                    </x-ui.empty>
                @else
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($projects as $project)
                            <x-landing.project :$project />
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ─── Testimonials ───────────────────────────────────────────────────── --}}
    @if ($general->show_testimonials_section)
        <section id="testimonials" class="px-6 py-20">
            <div class="mx-auto max-w-6xl">
                <div class="mb-12 text-center">
                    <x-ui.heading level="h2" size="xl" class="text-3xl font-bold text-neutral-700 dark:text-neutral-200">{{ __('What Clients Say') }}</x-ui.heading>
                    <x-ui.text class="term-comment mt-2 text-neutral-600 dark:text-neutral-400">
                        {{ __("Honest feedback from the people we've worked with.") }}
                    </x-ui.text>
                </div>

                @if ($testimonials->isEmpty())
                    <x-ui.empty class="term-block border-dashed py-16">
                        <x-ui.empty.media>
                            <x-ui.icon name="chat-bubble-left-ellipsis" class="h-10 w-10 text-neutral-400 dark:text-neutral-600" />
                        </x-ui.empty.media>
                        <x-ui.empty.contents>
                            <x-ui.text class="text-neutral-500 dark:text-neutral-500">{{ __('No testimonials yet.') }}</x-ui.text>
                        </x-ui.empty.contents>
                    </x-ui.empty>
                @else
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($testimonials as $testimonial)
                            <x-landing.testimonial :$testimonial />
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

</x-layouts::main>
