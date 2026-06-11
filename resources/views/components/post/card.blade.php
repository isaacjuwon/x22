@props(['post'])

<article data-slot="card" class="group flex flex-col h-full border border-neutral-200 dark:border-neutral-200 bg-white dark:bg-[#141414] transition-all duration-300">
    {{-- Editor Tab Header --}}
    <div class="flex items-center justify-between px-4 py-2 border-b border-neutral-200 dark:border-neutral-200 bg-neutral-50 dark:bg-[#0a0a0a]">
        <div class="flex items-center gap-2">
            <x-ui.icon name="document-text" class="size-3 text-neutral-400" />
            <span class="text-[9px] font-bold uppercase tracking-widest text-neutral-400">{{ $post->slug }}.json</span>
        </div>
        <div class="flex gap-1.5">
            <div class="size-1.5 rounded-full bg-red-500/30"></div>
            <div class="size-1.5 rounded-full bg-yellow-500/30"></div>
            <div class="size-1.5 rounded-full bg-green-500/30"></div>
        </div>
    </div>

    <div class="flex flex-col flex-1 p-6 gap-4 font-mono">
        <div class="flex items-center gap-2 text-[10px] text-neutral-400">
            <span class="text-primary font-bold">1</span>
            <span>{</span>
        </div>

        <div class="pl-4 space-y-4">
            <div class="space-y-2">
                <div class="flex items-start gap-2 text-xs">
                    <span class="text-primary font-bold">2</span>
                    <span class="text-accent-purple font-bold">"title"</span>
                    <span class="text-neutral-400">:</span>
                    <h3 class="font-bold tracking-tight text-neutral-900 dark:text-white group-hover:text-primary transition-colors leading-tight">
                        <a href="{{ route('posts.show', $post->slug) }}" wire:navigate>
                            <span aria-hidden="true" class="text-neutral-400">"</span>{{ $post->title }}<span aria-hidden="true" class="text-neutral-400">"</span>
                        </a>
                    </h3>
                </div>

                @if ($post->excerpt)
                    <div class="flex items-start gap-2 text-xs">
                        <span class="text-primary font-bold">3</span>
                        <span class="text-accent-purple font-bold">"summary"</span>
                        <span class="text-neutral-400">:</span>
                        <p class="text-neutral-500 dark:text-neutral-400 leading-relaxed line-clamp-2 italic">
                            "{{ $post->excerpt }}"
                        </p>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2 text-xs">
                <span class="text-primary font-bold">4</span>
                <span class="text-accent-purple font-bold">"stats"</span>
                <span class="text-neutral-400">:</span>
                <span class="text-neutral-400 text-[10px] font-bold uppercase tracking-tighter">
                    [{{ number_format($post->view_count) }} views, {{ $post->reading_time ?? 5 }}m read]
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2 text-[10px] text-neutral-400">
            <span class="text-primary font-bold">5</span>
            <span>}</span>
        </div>

        <div class="mt-auto pt-6 flex items-center justify-between border-t border-neutral-100 dark:border-neutral-200/10">
            <div class="flex items-center gap-3">
                <span class="text-[8px] font-black uppercase tracking-[0.3em] text-neutral-400">last_mod:</span>
                <span class="text-[9px] font-bold text-neutral-500 uppercase">
                    {{ $post->published_at?->format('Y-m-d') ?? 'DRAFT' }}
                </span>
            </div>
            <div class="text-[10px] font-black uppercase text-primary opacity-0 group-hover:opacity-100 transition-all translate-x-2 group-hover:translate-x-0">
                [ RUN_FILE ]
            </div>
        </div>
    </div>
</article>
