@php
    use App\Enums\ProjectStatus;
    use App\Models\Project;
    use App\Settings\GeneralSettings;

    $general = app(GeneralSettings::class);

    $projects = Project::where('status', ProjectStatus::Completed)
        ->latest()
        ->limit(6)
        ->get();
@endphp

<x-layouts::main :title="$general->site_name">
    <div class="min-h-screen transition-colors duration-300">
        
        {{-- Hero Section --}}
        <section class="relative overflow-hidden px-6 py-24 lg:py-32">
            <div class="mx-auto max-w-5xl space-y-8">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-xs font-bold text-primary">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-primary"></span>
                        </span>
                        {{ __('Available for new projects') }}
                    </span>
                    <span class="text-xs font-bold text-neutral-400 uppercase tracking-widest">v20.5.0-stable</span>
                </div>

                <div class="space-y-6">
                    <h1 class="text-5xl lg:text-8xl font-black tracking-tight text-neutral-900 dark:text-white uppercase leading-none">
                        {{ $general->hero_title }}
                    </h1>
                    <p class="max-w-2xl text-xl lg:text-2xl text-neutral-500 dark:text-neutral-400 font-medium leading-relaxed">
                        {{ $general->hero_subtitle }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-4 pt-4">
                    @if ($general->show_projects_section)
                        <x-ui.button as="a" href="#projects" variant="primary" size="lg" class="px-8 py-4 uppercase tracking-widest">
                            {{ __('Explore Projects') }}
                        </x-ui.button>
                    @endif
                    @if ($general->show_posts_section)
                        <x-ui.button as="a" href="#blog" variant="outline" size="lg" class="px-8 py-4 uppercase tracking-widest border-neutral-200 dark:border-neutral-800">
                            {{ __('Read Insights') }}
                        </x-ui.button>
                    @endif
                </div>
            </div>
        </section>

        {{-- Projects Section --}}
        @if ($general->show_projects_section)
            <section id="projects" class="px-6 py-24 bg-white dark:bg-[#0a0a0a]">
                <div class="mx-auto max-w-6xl">
                    <div class="mb-16 flex items-end justify-between border-b border-neutral-100 dark:border-neutral-200 pb-8">
                        <div class="space-y-2">
                            <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-primary">{{ __('Featured Work') }}</p>
                            <h2 class="text-3xl font-black text-neutral-900 dark:text-white uppercase">{{ __('Selected Projects') }}</h2>
                        </div>
                        <a href="{{ route('projects.index') }}" wire:navigate class="text-xs font-bold text-neutral-400 hover:text-primary transition-colors uppercase tracking-widest">
                            {{ __('View All') }} &rarr;
                        </a>
                    </div>

                    @if ($projects->isEmpty())
                        <div class="py-20 border-2 border-dashed border-neutral-100 dark:border-neutral-900 rounded-3xl text-center">
                            <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">{{ __('No projects indexed.') }}</p>
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

        {{-- Blog Section --}}
        @if ($general->show_posts_section)
            <section id="blog" class="px-6 py-24">
                <div class="mx-auto max-w-6xl">
                    <div class="mb-16 flex items-end justify-between border-b border-neutral-100 dark:border-neutral-200 pb-8">
                        <div class="space-y-2">
                            <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-primary">{{ __('Technical Log') }}</p>
                            <h2 class="text-3xl font-black text-neutral-900 dark:text-white uppercase">{{ __('Recent Insights') }}</h2>
                        </div>
                        <a href="{{ route('posts.index') }}" wire:navigate class="text-xs font-bold text-neutral-400 hover:text-primary transition-colors uppercase tracking-widest">
                            {{ __('Read Log') }} &rarr;
                        </a>
                    </div>

                    <div class="grid gap-8">
                        <livewire:landing.posts />
                    </div>
                </div>
            </section>
        @endif

    </div>
</x-layouts::main>
