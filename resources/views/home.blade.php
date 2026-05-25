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
    <section class="relative overflow-hidden border-b border-neutral-200 dark:border-neutral-800 px-6 py-28 text-center">
        {{-- subtle grid background --}}
        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,#16a34a08_1px,transparent_1px),linear-gradient(to_bottom,#16a34a08_1px,transparent_1px)] bg-[size:32px_32px] dark:bg-[linear-gradient(to_right,#4ade8008_1px,transparent_1px),linear-gradient(to_bottom,#4ade8008_1px,transparent_1px)]"></div>

        <div class="relative mx-auto max-w-3xl space-y-6">
            <x-ui.badge color="green" variant="outline" class="mx-auto">{{ __('System::Online') }}</x-ui.badge>

            <h1 class="term-prompt text-4xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50 sm:text-5xl">
                {{ $general->hero_title }}
            </h1>

            <p class="mx-auto max-w-xl text-lg leading-relaxed text-neutral-500 dark:text-neutral-400">
                <span class="term-comment">{{ $general->hero_subtitle }}</span>
            </p>

            <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                @if ($general->show_posts_section)
                    <x-ui.button as="a" href="#posts" variant="primary" size="lg">
                        {{ __('Run Blog.exe') }}
                    </x-ui.button>
                @endif
                @if ($general->show_projects_section)
                    <x-ui.button as="a" href="#projects" variant="outline" size="lg">
                        {{ __('List Projects') }}
                    </x-ui.button>
                @endif
            </div>

            {{-- terminal status line --}}
            <div class="mx-auto max-w-md">
                <p class="term-block text-left text-xs text-neutral-400 dark:text-neutral-600">
{{ __('Status: System Ready') }}
{{ __('Version: 2.0.4-stable') }}
{{ __('Uptime: 99.9%') }}
{{ __('Time: ') }} {{ now()->format('Y-m-d H:i') }}
                </p>
            </div>
        </div>
    </section>

    {{-- ─── Latest Posts ───────────────────────────────────────────────────── --}}
    @if ($general->show_posts_section)
        <section id="posts" class="border-b border-neutral-800 dark:border-neutral-800 px-6 py-20">
            <div class="mx-auto max-w-6xl">
                <div class="mb-12 flex items-end justify-between">
                    <div>
                        <x-ui.heading level="h2" size="xl" class="text-3xl font-bold text-neutral-700 dark:text-neutral-200">{{ __('Latest Posts') }}</x-ui.heading>
                        <x-ui.text class="term-comment mt-2 text-neutral-600 dark:text-neutral-400">
                            {{ __('Thoughts, tutorials, and updates from the team.') }}
                        </x-ui.text>
                    </div>
                    <x-ui.button as="a" href="{{ route('posts.index') }}" wire:navigate variant="ghost" size="sm">
                        {{ __('All posts') }} →
                    </x-ui.button>
                </div>

                <livewire:landing.posts />
            </div>
        </section>
    @endif

    {{-- ─── Projects ───────────────────────────────────────────────────────── --}}
    @if ($general->show_projects_section)
        <section id="projects" class="border-b border-neutral-800 dark:border-neutral-800 px-6 py-20">
            <div class="mx-auto max-w-6xl">
                <div class="mb-12 text-center">
                    <x-ui.heading level="h2" size="xl" class="text-3xl font-bold text-neutral-700 dark:text-neutral-200">{{ __('Our Projects') }}</x-ui.heading>
                    <x-ui.text class="term-comment mt-2 text-neutral-600 dark:text-neutral-400">
                        {{ __("A selection of work we're proud of.") }}
                    </x-ui.text>
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
