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
    <div class="flex min-h-screen ide-grid transition-colors duration-300">
        
        {{-- ── Left Sidebar: IDE Navigation ── --}}
        <aside class="hidden lg:flex w-16 flex-col items-center py-6 border-r border-neutral-200 dark:border-neutral-200 bg-white dark:bg-[#0a0a0a] gap-8 text-neutral-400">
            <x-ui.icon name="heroicon-o-document-duplicate" class="size-6 hover:text-primary cursor-pointer transition-colors" />
            <x-ui.icon name="heroicon-o-magnifying-glass" class="size-6 hover:text-primary cursor-pointer transition-colors" />
            <x-ui.icon name="heroicon-o-git-branch" class="size-6 hover:text-primary cursor-pointer transition-colors" />
            <x-ui.icon name="heroicon-o-play" class="size-6 hover:text-primary cursor-pointer transition-colors" />
            <div class="mt-auto flex flex-col items-center gap-6">
                <x-ui.theme-switcher variant="inline" />
                <x-ui.icon name="heroicon-o-cog-6-tooth" class="size-6 hover:text-primary cursor-pointer transition-colors" />
            </div>
        </aside>

        {{-- ── Main Workspace ── --}}
        <main class="flex-1 flex flex-col min-w-0 bg-neutral-50 dark:bg-[#0a0a0a]">
            
            {{-- Editor Tabs --}}
            <nav class="flex border-b border-neutral-200 dark:border-neutral-200 bg-white dark:bg-[#141414] overflow-x-auto">
                <div class="flex items-center px-6 py-3 border-r border-neutral-200 dark:border-neutral-200 bg-neutral-50 dark:bg-[#0a0a0a] text-[10px] font-bold text-primary border-t-2 border-t-primary uppercase tracking-widest">
                    <x-ui.icon name="heroicon-o-home" class="size-3 mr-2" />
                    <span>README.md</span>
                </div>
                <a href="{{ route('posts.index') }}" class="flex items-center px-6 py-3 border-r border-neutral-200 dark:border-neutral-200 text-[10px] font-bold text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors uppercase tracking-widest">
                    <x-ui.icon name="heroicon-o-document-text" class="size-3 mr-2" />
                    <span>blog.json</span>
                </a>
                <a href="{{ route('projects.index') }}" class="flex items-center px-6 py-3 border-r border-neutral-200 dark:border-neutral-200 text-[10px] font-bold text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors uppercase tracking-widest">
                    <x-ui.icon name="heroicon-o-folder" class="size-3 mr-2" />
                    <span>portfolio.lock</span>
                </a>
            </nav>

            {{-- Content Area --}}
            <div class="flex-1 overflow-y-auto p-8 lg:p-20 space-y-32">
                
                {{-- Hero Section: Terminal Execution --}}
                <section class="max-w-4xl space-y-10">
                    <div class="flex items-center gap-3 text-primary text-sm font-bold">
                        <span class="opacity-50">sh-v20.5:</span>
                        <span class="text-neutral-400 dark:text-neutral-600">~/masterpiece</span>
                        <span class="animate-pulse">_</span>
                    </div>

                    <div class="space-y-6">
                        <h1 class="text-6xl lg:text-9xl font-bold tracking-tighter text-neutral-900 dark:text-white uppercase leading-[0.85]">
                            {{ $general->hero_title }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-6">
                            <div class="flex items-center gap-2 px-3 py-1 bg-primary text-black text-[10px] font-bold uppercase">
                                <x-ui.icon name="heroicon-o-check-badge" class="size-3" />
                                <span>Senior Architect</span>
                            </div>
                            <div class="flex items-center gap-2 text-neutral-400 text-[10px] font-bold uppercase tracking-widest">
                                <span class="text-primary">uptime:</span>
                                <span>20+ Years</span>
                            </div>
                            <div class="flex items-center gap-2 text-neutral-400 text-[10px] font-bold uppercase tracking-widest">
                                <span class="text-primary">stack:</span>
                                <span>Laravel / Livewire / T-CSS</span>
                            </div>
                        </div>
                    </div>

                    <p class="max-w-2xl text-xl text-neutral-500 dark:text-neutral-400 font-medium leading-relaxed border-l-4 border-primary pl-8">
                        {{ $general->hero_subtitle }}
                    </p>

                    <div class="flex flex-wrap gap-6 pt-6">
                        @if ($general->show_projects_section)
                            <a href="#projects" class="bg-primary text-black px-10 py-4 text-xs font-black uppercase tracking-[0.2em] hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,0.3)] transition-all">
                                ./run --portfolio
                            </a>
                        @endif
                        @if ($general->show_posts_section)
                            <a href="#blog" class="border-2 border-neutral-200 dark:border-neutral-200 px-10 py-4 text-xs font-black uppercase tracking-[0.2em] text-neutral-900 dark:text-white hover:bg-neutral-900 dark:hover:bg-white hover:text-white dark:hover:text-black transition-all">
                                cat insights.log
                            </a>
                        @endif
                    </div>
                </section>

                {{-- Projects Grid --}}
                @if ($general->show_projects_section)
                    <section id="projects" class="space-y-12">
                        <div class="flex items-center gap-6">
                            <span class="text-xs font-black text-primary uppercase tracking-[0.4em]">$ ls -la ./projects</span>
                            <div class="h-px flex-1 bg-neutral-200 dark:bg-neutral-200 opacity-20"></div>
                        </div>

                        <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-3">
                            @forelse ($projects as $project)
                                <x-landing.project :$project />
                            @empty
                                <div class="col-span-full py-20 border-2 border-dashed border-neutral-200 dark:border-neutral-200 text-center uppercase text-[10px] font-bold text-neutral-400 tracking-[0.5em]">
                                    [ no_projects_indexed ]
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endif

                {{-- Blog Section --}}
                @if ($general->show_posts_section)
                    <section id="blog" class="space-y-12">
                        <div class="flex items-center gap-6">
                            <span class="text-xs font-black text-primary uppercase tracking-[0.4em]">$ grep "expertise" ./blog</span>
                            <div class="h-px flex-1 bg-neutral-200 dark:bg-neutral-200 opacity-20"></div>
                        </div>

                        <div class="grid gap-8">
                            <livewire:landing.posts />
                        </div>
                    </section>
                @endif

            </div>

            {{-- Terminal Footer Status Bar --}}
            <footer class="flex items-center justify-between px-6 py-2 bg-neutral-900 dark:bg-primary text-white dark:text-black text-[9px] font-bold uppercase tracking-[0.2em]">
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-2">
                        <span class="size-2 bg-green-500 rounded-full animate-pulse"></span>
                        <span>System Online</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-ui.icon name="heroicon-o-git-branch" class="size-3" />
                        <span>Production v{{ config('app.version', '1.0.0') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-8">
                    <span>{{ config('app.env') }}</span>
                    <span>PHP {{ phpversion() }}</span>
                    <span class="hidden md:inline">{{ date('Y-m-d H:i:s T') }}</span>
                </div>
            </footer>
        </main>
    </div>
</x-layouts::main>
