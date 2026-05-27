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

    <div class="flex min-h-screen bg-neutral-50 dark:bg-[#0a0a0a] font-mono transition-colors duration-300">
        
        {{-- IDE Sidebar Simulation --}}
        <aside class="hidden lg:flex w-12 flex-col items-center py-4 border-r border-neutral-200 dark:border-neutral-800 gap-6 text-neutral-400">
            <x-ui.icon name="heroicon-o-files" class="size-6 hover:text-primary cursor-pointer" />
            <x-ui.icon name="heroicon-o-magnifying-glass" class="size-6 hover:text-primary cursor-pointer" />
            <x-ui.icon name="heroicon-o-git-branch" class="size-6 hover:text-primary cursor-pointer" />
            <x-ui.icon name="heroicon-o-play" class="size-6 hover:text-primary cursor-pointer" />
            <div class="mt-auto pb-4">
                <x-ui.icon name="heroicon-o-cog-6-tooth" class="size-6 hover:text-primary cursor-pointer" />
            </div>
        </aside>

        <main class="flex-1 flex flex-col min-w-0">
            {{-- Editor Tabs --}}
            <nav class="flex border-b border-neutral-200 dark:border-neutral-800 bg-neutral-100 dark:bg-[#141414] overflow-x-auto">
                <div class="flex items-center px-4 py-2 border-r border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-[#0a0a0a] text-xs font-bold text-primary border-t-2 border-t-primary">
                    <x-ui.icon name="heroicon-o-home" class="size-3 mr-2" />
                    <span>index.blade.php</span>
                    <x-ui.icon name="heroicon-o-x-mark" class="size-3 ml-4 opacity-50 hover:opacity-100 cursor-pointer" />
                </div>
                <a href="{{ route('posts.index') }}" class="flex items-center px-4 py-2 border-r border-neutral-200 dark:border-neutral-800 text-xs font-medium text-neutral-500 hover:bg-neutral-200 dark:hover:bg-neutral-800 transition-colors">
                    <x-ui.icon name="heroicon-o-document-text" class="size-3 mr-2" />
                    <span>blog.json</span>
                </a>
                <a href="{{ route('projects.index') }}" class="flex items-center px-4 py-2 border-r border-neutral-200 dark:border-neutral-800 text-xs font-medium text-neutral-500 hover:bg-neutral-200 dark:hover:bg-neutral-800 transition-colors">
                    <x-ui.icon name="heroicon-o-folder" class="size-3 mr-2" />
                    <span>portfolio.lock</span>
                </a>
            </nav>

            {{-- Terminal Content Area --}}
            <div class="flex-1 overflow-y-auto p-8 lg:p-12 space-y-16">
                
                {{-- Hero: Terminal Prompt Style --}}
                <section class="space-y-6">
                    <div class="flex items-center gap-2 text-primary text-sm font-bold">
                        <span class="opacity-50">root@sheaf:</span>
                        <span class="text-neutral-400 dark:text-neutral-500">~</span>
                        <span class="animate-pulse">_</span>
                    </div>

                    <div class="space-y-2">
                        <h1 class="text-4xl lg:text-7xl font-bold tracking-tighter text-neutral-900 dark:text-white uppercase leading-none">
                            {{ $general->hero_title }}
                        </h1>
                        <div class="flex items-center gap-4 text-neutral-400 dark:text-neutral-600 text-sm">
                            <span class="text-primary font-bold">status:</span>
                            <span>READY_FOR_DEPLOYMENT</span>
                            <span class="opacity-30">|</span>
                            <span>v4.2.0-STABLE</span>
                        </div>
                    </div>

                    <p class="max-w-2xl text-lg text-neutral-500 dark:text-neutral-400 leading-relaxed border-l-2 border-primary/20 pl-6">
                        {{ $general->hero_subtitle }}
                    </p>

                    <div class="flex flex-wrap gap-4 pt-4">
                        @if ($general->show_projects_section)
                            <a href="#projects" class="bg-primary text-black px-6 py-2 text-sm font-bold uppercase hover:bg-primary-content transition-colors">
                                ./view_portfolio.sh
                            </a>
                        @endif
                        @if ($general->show_posts_section)
                            <a href="#blog" class="border border-neutral-200 dark:border-neutral-800 px-6 py-2 text-sm font-bold uppercase text-neutral-900 dark:text-white hover:border-primary transition-colors">
                                cat blog_insights.txt
                            </a>
                        @endif
                    </div>
                </section>

                {{-- Projects Grid: Terminal Blocks --}}
                @if ($general->show_projects_section)
                    <section id="projects" class="space-y-8">
                        <div class="flex items-center gap-4 border-b border-neutral-200 dark:border-neutral-800 pb-4">
                            <span class="text-xs font-bold text-primary uppercase tracking-[0.3em]">$ ls ./projects</span>
                            <div class="h-px flex-1 bg-neutral-200 dark:bg-neutral-800 opacity-30"></div>
                        </div>

                        @if ($projects->isEmpty())
                            <div class="py-12 border-2 border-dashed border-neutral-200 dark:border-neutral-800 text-center text-neutral-500">
                                [EMPTY_DIRECTORY]
                            </div>
                        @else
                            <div class="grid gap-px bg-neutral-200 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-800 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($projects as $project)
                                    <div class="bg-neutral-50 dark:bg-[#0a0a0a] p-px">
                                        <x-landing.project :$project />
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endif

                {{-- Blog Grid: Terminal Output --}}
                @if ($general->show_posts_section)
                    <section id="blog" class="space-y-8">
                        <div class="flex items-center gap-4 border-b border-neutral-200 dark:border-neutral-800 pb-4">
                            <span class="text-xs font-bold text-primary uppercase tracking-[0.3em]">$ grep -r "insights" ./blog</span>
                            <div class="h-px flex-1 bg-neutral-200 dark:bg-neutral-800 opacity-30"></div>
                        </div>

                        <div class="grid gap-px bg-neutral-200 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-800">
                            <livewire:landing.posts />
                        </div>
                    </section>
                @endif

            </div>

            {{-- Terminal Footer Status Bar --}}
            <footer class="flex items-center justify-between px-4 py-1 bg-primary text-black text-[10px] font-bold uppercase tracking-widest">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-1">
                        <x-ui.icon name="heroicon-o-bolt" class="size-3" />
                        <span>Connected</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <x-ui.icon name="heroicon-o-git-branch" class="size-3" />
                        <span>Main*</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span>UTF-8</span>
                    <span>Laravel v11.x</span>
                    <span>{{ date('H:i:s') }}</span>
                </div>
            </footer>
        </main>
    </div>
</x-layouts::main>
