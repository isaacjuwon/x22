@props(['project'])

<article data-slot="card" class="group flex flex-col h-full border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#141414] transition-all duration-300 hover:border-primary">
    {{-- Project Header (IDE Style) --}}
    <div class="flex items-center justify-between px-4 py-2 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-[#0a0a0a]">
        <div class="flex items-center gap-2">
            <x-ui.icon name="heroicon-o-document" class="size-3 text-neutral-400" />
            <span class="text-[10px] font-bold uppercase tracking-widest text-neutral-500">{{ $project->category ?? __('project') }}.md</span>
        </div>
        <x-ui.icon name="heroicon-o-ellipsis-horizontal" class="size-3 text-neutral-400" />
    </div>

    {{-- Project Image (Terminal Style) --}}
    @if ($project->featuredImageUrl('card'))
        <div class="relative aspect-video overflow-hidden border-b border-neutral-200 dark:border-neutral-800">
            <img
                src="{{ $project->featuredImageUrl('card') }}"
                alt="{{ $project->title }}"
                class="h-full w-full object-cover transition-all duration-500 group-hover:scale-105"
                loading="lazy"
            />
            <div class="absolute inset-0 bg-primary/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        </div>
    @endif

    <div class="flex flex-col flex-1 p-6 gap-4 font-mono">
        <div class="flex items-center gap-2">
            <span class="text-primary font-bold">#</span>
            <h3 class="text-lg font-bold tracking-tight text-neutral-900 dark:text-white group-hover:text-primary transition-colors">
                <a href="{{ route('projects.show', $project) }}" wire:navigate>
                    {{ $project->title }}
                </a>
            </h3>
        </div>

        @isset($project->description)
            <p class="text-neutral-500 dark:text-neutral-400 text-xs leading-relaxed line-clamp-3">
                {{ $project->description }}
            </p>
        @endisset

        <div class="mt-auto pt-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                <span class="text-[9px] font-bold uppercase tracking-widest text-neutral-400">deployed_v1.0</span>
            </div>
            <div class="text-primary opacity-0 group-hover:opacity-100 transition-opacity text-xs font-bold">
                [ READ_MORE ]
            </div>
        </div>
    </div>
</article>

