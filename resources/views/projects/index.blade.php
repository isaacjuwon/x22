<x-layouts::main :title="__('Projects')">

    <div class="flex min-h-screen ide-grid font-mono transition-colors duration-300 bg-neutral-50 dark:bg-[#0a0a0a]">
        <div class="container mx-auto px-6 py-20 space-y-16">
            
            {{-- Header: Breadcrumbs & Title --}}
            <div class="space-y-6">
                <nav class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-[0.3em] text-neutral-400">
                    <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors">~/root</a>
                    <span class="opacity-30">/</span>
                    <span class="text-primary">portfolio</span>
                </nav>
                
                <div class="space-y-2">
                    <h1 class="text-5xl lg:text-8xl font-black tracking-tighter text-neutral-900 dark:text-white uppercase leading-none">
                        Project<span class="text-primary">_</span>Indexed
                    </h1>
                    <p class="text-neutral-500 dark:text-neutral-400 text-sm font-bold uppercase tracking-widest max-w-xl border-l-2 border-primary/20 pl-4">
                        // A strictly curated repository of digital solutions, engineered with 20+ years of technical precision.
                    </p>
                </div>
            </div>

            {{-- Filters Bar (IDE Search Style) --}}
            <form method="GET" action="{{ route('projects.index') }}" class="flex flex-wrap items-center gap-px bg-neutral-200 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-800">
                <div class="flex-1 min-w-[300px] flex items-center bg-white dark:bg-[#141414] px-6 py-4">
                    <x-ui.icon name="heroicon-o-magnifying-glass" class="size-4 text-neutral-400 mr-4" />
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="grep -i 'project_name'..."
                        class="w-full bg-transparent border-none focus:ring-0 text-sm font-bold text-neutral-900 dark:text-white placeholder:text-neutral-400"
                    />
                </div>

                @if ($categories->isNotEmpty())
                    <div class="bg-white dark:bg-[#141414] px-6 py-4 border-l border-neutral-200 dark:border-neutral-800">
                        <select name="category" class="bg-transparent border-none focus:ring-0 text-[10px] font-black uppercase tracking-widest text-neutral-500 cursor-pointer">
                            <option value="">-- ALL_CATEGORIES --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                    {{ strtoupper($cat) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button type="submit" class="bg-primary text-black px-10 py-4 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-primary-content transition-colors">
                    Filter.exec
                </button>

                @if (request('search') || request('category'))
                    <a href="{{ route('projects.index') }}" class="bg-neutral-900 dark:bg-white text-white dark:text-black px-6 py-4 text-[10px] font-black uppercase tracking-widest">
                        Reset
                    </a>
                @endif
            </form>

            {{-- Results Grid --}}
            @if ($projects->isEmpty())
                <div class="py-40 border-2 border-dashed border-neutral-200 dark:border-neutral-800 text-center space-y-4">
                    <div class="text-xs font-black text-neutral-400 uppercase tracking-[0.5em]">
                        [ 404: NO_PROJECTS_FOUND ]
                    </div>
                </div>
            @else
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <x-landing.project :$project />
                    @endforeach
                </div>

                {{-- Pagination (IDE Style) --}}
                @if ($projects->hasPages())
                    <div class="flex justify-center pt-12">
                        <div class="bg-neutral-100 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 p-1">
                            {{ $projects->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts::main>
