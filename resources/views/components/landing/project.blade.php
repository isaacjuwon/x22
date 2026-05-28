@props(['project'])

<article data-slot="card" class="group flex flex-col h-full border border-neutral-200 dark:border-neutral-200 bg-white dark:bg-[#141414] transition-all duration-300">
    {{-- Project Header (IDE Style) --}}
    <div class="flex items-center justify-between px-4 py-2 border-b border-neutral-200 dark:border-neutral-200 bg-neutral-50 dark:bg-[#0a0a0a]">
        <div class="flex items-center gap-2">
            <x-ui.icon name="heroicon-o-document" class="size-3 text-neutral-400" />
            <span class="text-[9px] font-bold uppercase tracking-widest text-neutral-400">{{ $project->category ?? 'project' }}.md</span>
        </div>
        <div class="flex gap-1">
            <div class="size-1.5 bg-neutral-200 dark:bg-neutral-800"></div>
            <div class="size-1.5 bg-neutral-200 dark:bg-neutral-800"></div>
        </div>
    </div>

    {{-- Project Image --}}
    @if ($project->featuredImageUrl('card'))
        <div class="relative aspect-video overflow-hidden border-b border-neutral-200 dark:border-neutral-200">
            <img
                src="{{ $project->featuredImageUrl('card') }}"
                alt="{{ $project->title }}"
                class="h-full w-full object-cover transition-all duration-700 group-hover:scale-110 grayscale hover:grayscale-0"
                loading="lazy"
            />
            <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        </div>
    @endif

    <div class="flex flex-col flex-1 p-6 gap-4 font-mono">
        <div class="space-y-1">
            <div class="flex items-center gap-2 text-[10px] text-neutral-400">
                <span class="text-primary">1</span>
                <span class="font-bold">TITLE:</span>
            </div>
            <h3 class="pl-4 text-base font-bold tracking-tight text-neutral-900 dark:text-white group-hover:text-primary transition-colors">
                <a href="{{ route('projects.show', $project) }}" wire:navigate>
                    "{{ $project->title }}"
                </a>
            </h3>
        </div>

        @isset($project->description)
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-[10px] text-neutral-400">
                    <span class="text-primary">2</span>
                    <span class="font-bold">DESC:</span>
                </div>
                <p class="pl-4 text-[11px] leading-relaxed text-neutral-500 dark:text-neutral-400 line-clamp-3 italic">
                    "{{ $project->description }}"
                </p>
            </div>
        @endisset

        <div class="mt-auto pt-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="h-1.5 w-1.5 bg-primary"></span>
                <span class="text-[8px] font-bold uppercase tracking-[0.2em] text-neutral-400">deployed_v1.2.0</span>
            </div>
            <div class="text-[10px] font-black uppercase text-primary opacity-0 group-hover:opacity-100 transition-all translate-x-2 group-hover:translate-x-0">
                [ OPEN_CASE ]
            </div>
        </div>
    </div>
</article>
