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
    <section class="relative border-b border-neutral-200 dark:border-neutral-900 px-6 py-28 overflow-hidden bg-neutral-50 dark:bg-[#050505]">
        <div class="mx-auto max-w-5xl">
            <div class="grid gap-16 lg:grid-cols-2 items-center">
                <div class="space-y-10">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <x-ui.icon name="ps:lightning" class="size-5 text-primary" />
                            <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-neutral-400">{{ __('system_active: true') }}</span>
                        </div>
                        <h1 class="text-5xl font-bold tracking-tighter text-neutral-900 dark:text-neutral-50 sm:text-7xl uppercase leading-[0.9]">
                            {{ $general->hero_title }}
                        </h1>
                    </div>

                    <p class="text-xl leading-relaxed text-neutral-600 dark:text-neutral-500 font-medium">
                        <span class="term-comment">{{ $general->hero_subtitle }}</span>
                    </p>

                    <div class="flex flex-wrap items-center gap-6">
                        @if ($general->show_posts_section)
                            <x-ui.button as="a" href="#posts" variant="primary" size="lg" class="px-8">
                                <x-ui.icon name="ps:article-medium" class="size-4 mr-2" />
                                {{ __('read_articles') }}
                            </x-ui.button>
                        @endif
                        @if ($general->show_projects_section)
                            <x-ui.button as="a" href="#projects" variant="outline" size="lg" class="px-8">
                                <x-ui.icon name="ps:code-block" class="size-4 mr-2" />
                                {{ __('our_work') }}
                            </x-ui.button>
                        @endif
                    </div>
                </div>

                <div class="term-block border-l-4 border-l-primary shadow-2xl scale-110 origin-right">
                    <div class="flex items-center justify-between mb-6 border-b border-neutral-800 pb-4">
                        <div class="flex gap-2">
                            <div class="h-2 w-2 rounded-full bg-neutral-900"></div>
                            <div class="h-2 w-2 rounded-full bg-neutral-900"></div>
                            <div class="h-2 w-2 rounded-full bg-neutral-900"></div>
                        </div>
                        <span class="text-[9px] uppercase tracking-widest opacity-40">{{ __('system@monitor:~$ status') }}</span>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between text-xs">
                            <span class="opacity-50 uppercase tracking-widest">{{ __('CPU Load') }}</span>
                            <span class="text-primary font-bold">12%</span>
                        </div>
                        <div class="h-1 bg-neutral-900">
                            <div class="h-full bg-primary w-[12%] shadow-[0_0_8px_var(--color-primary)]"></div>
                        </div>
                        
                        <div class="flex items-center justify-between text-xs pt-2">
                            <span class="opacity-50 uppercase tracking-widest">{{ __('Memory') }}</span>
                            <span class="text-primary font-bold">4.2GB / 16GB</span>
                        </div>
                        <div class="h-1 bg-neutral-900">
                            <div class="h-full bg-primary w-[26%] shadow-[0_0_8px_var(--color-primary)]"></div>
                        </div>

                        <div class="pt-6 border-t border-neutral-900 space-y-2">
                            <p class="text-[9px] uppercase tracking-widest text-success flex items-center gap-2">
                                <x-ui.icon name="ps:circle-dashed" class="size-3 animate-spin" />
                                {{ __('Database connection optimized') }}
                            </p>
                            <p class="text-[9px] uppercase tracking-widest text-info flex items-center gap-2">
                                <x-ui.icon name="ps:shield-check" class="size-3" />
                                {{ __('Security protocols active') }}
                            </p>
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
