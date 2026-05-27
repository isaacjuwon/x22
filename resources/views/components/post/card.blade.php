@props(['post'])

<article data-slot="card" class="group flex flex-col h-full border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#141414] transition-all duration-300 hover:border-primary">
    {{-- Editor Tab Header --}}
    <div class="flex items-center justify-between px-4 py-2 border-b border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-[#0a0a0a]">
        <div class="flex items-center gap-2">
            <x-ui.icon name="document-text" class="size-3 text-neutral-400" />
            <span class="text-[10px] font-bold uppercase tracking-widest text-neutral-500">{{ $post->slug }}.json</span>
        </div>
        <div class="flex gap-1">
            <div class="size-2 rounded-full bg-neutral-200 dark:bg-neutral-800"></div>
            <div class="size-2 rounded-full bg-neutral-200 dark:bg-neutral-800"></div>
        </div>
    </div>

    <div class="flex flex-col flex-1 p-6 gap-4 font-mono">
        {{-- Code Block Meta --}}
        <div class="flex items-center gap-2 text-[10px] text-neutral-400">
            <span class="text-primary font-bold">1</span>
            <span>{</span>
        </div>

        <div class="pl-4 space-y-4">
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-primary font-bold">2</span>
                    <span class="text-blue-500">"title"</span>
                    <span>:</span>
                    <h3 class="text-sm font-bold tracking-tight text-neutral-900 dark:text-white group-hover:text-primary transition-colors">
                        <a href="{{ route('posts.show', $post->slug) }}" wire:navigate>
                            "{{ $post->title }}"
                        </a>
                    </h3>
                </div>

                @if ($post->excerpt)
                    <div class="flex gap-2 text-xs">
                        <span class="text-primary font-bold">3</span>
                        <span class="text-blue-500">"summary"</span>
                        <span>:</span>
                        <p class="text-neutral-500 dark:text-neutral-400 leading-relaxed line-clamp-2 italic">
                            "{{ $post->excerpt }}"
                        </p>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2 text-xs">
                <span class="text-primary font-bold">4</span>
                <span class="text-blue-500">"author"</span>
                <span>:</span>
                <span class="text-neutral-300">"{{ $post->user->name }}"</span>
            </div>
        </div>

        <div class="flex items-center gap-2 text-[10px] text-neutral-400">
            <span class="text-primary font-bold">5</span>
            <span>}</span>
        </div>

        <div class="mt-auto pt-4 flex items-center justify-between border-t border-neutral-100 dark:border-neutral-800/50">
            <div class="flex items-center gap-3">
                <x-ui.icon name="clock" class="size-3 text-neutral-400" />
                <span class="text-[9px] font-bold uppercase tracking-widest text-neutral-500">
                    {{ $post->published_at?->diffForHumans() ?? __('Draft') }}
                </span>
            </div>
            <div class="flex items-center gap-1.5 text-neutral-500 text-[10px]">
                <x-ui.icon name="eye" class="size-3" />
                <span>{{ number_format($post->view_count) }}</span>
            </div>
        </div>
    </div>
</article>

