@props(['post'])

<article data-slot="card" class="group flex flex-col h-full border border-neutral-200 dark:border-neutral-200 bg-white dark:bg-[#141414] transition-all duration-300">
    {{-- Terminal Window Header --}}
    <div class="flex items-center justify-between px-4 py-1.5 border-b border-neutral-200 dark:border-neutral-200 bg-neutral-100 dark:bg-[#0a0a0a]">
        <div class="flex gap-1.5">
            <div class="size-1.5 rounded-full bg-red-500/30"></div>
            <div class="size-1.5 rounded-full bg-yellow-500/30"></div>
            <div class="size-1.5 rounded-full bg-green-500/30"></div>
        </div>
        <span class="text-[9px] font-mono text-neutral-400 uppercase tracking-tighter">{{ $post->slug }}.sh</span>
    </div>

    <div class="flex flex-col flex-1 p-6 gap-4 font-mono">
        {{-- Terminal Output Style --}}
        <div class="space-y-4">
            <div class="flex items-start gap-2 text-xs">
                <span class="text-primary font-bold shrink-0">$</span>
                <h3 class="font-bold text-neutral-900 dark:text-white group-hover:text-primary transition-colors leading-relaxed">
                    <a href="{{ route('posts.show', $post->slug) }}" wire:navigate>
                        cat "{{ $post->title }}"
                    </a>
                </h3>
            </div>

            @if ($post->excerpt)
                <div class="pl-4 border-l border-neutral-200 dark:border-neutral-200">
                    <p class="text-[11px] leading-relaxed text-neutral-500 dark:text-neutral-400 line-clamp-3 italic">
                        # {{ $post->excerpt }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Meta Stats --}}
        <div class="mt-auto pt-6 flex items-center justify-between text-[9px] font-bold uppercase tracking-widest text-neutral-400 border-t border-neutral-100 dark:border-neutral-200/10">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1.5">
                    <x-ui.icon name="heroicon-o-eye" class="size-3" />
                    <span>{{ number_format($post->view_count) }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <x-ui.icon name="heroicon-o-clock" class="size-3" />
                    <span>{{ $post->reading_time ?? 5 }}m</span>
                </div>
            </div>
            <span class="text-primary opacity-0 group-hover:opacity-100 transition-all translate-x-2 group-hover:translate-x-0">
                [ EXECUTE ]
            </span>
        </div>
    </div>
</article>
