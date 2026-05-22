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
    <section class="border-b border-neutral-800 dark:border-neutral-800 px-6 py-24 text-center">
        <div class="mx-auto max-w-3xl space-y-6">
            <x-ui.badge color="green" class="mx-auto">{{ __('Welcome') }}</x-ui.badge>

            <x-ui.heading level="h1" size="xl" class="term-prompt text-5xl font-bold tracking-tight text-green-600 dark:text-green-400">
                {{ $general->hero_title }}
            </x-ui.heading>

            <x-ui.text class="mx-auto max-w-xl text-lg text-neutral-600 dark:text-neutral-400">
                {{ $general->hero_subtitle }}
            </x-ui.text>

            <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                @if ($general->show_posts_section)
                    <x-ui.button as="a" href="#posts" variant="primary" size="lg">
                        {{ __('Read the Blog') }}
                    </x-ui.button>
                @endif
                @if ($general->show_projects_section)
                    <x-ui.button as="a" href="#projects" variant="ghost" size="lg">
                        {{ __('View Projects') }}
                    </x-ui.button>
                @endif
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
