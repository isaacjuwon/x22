@props(['post'])

<article data-slot="card" class="group border border-neutral-200 dark:border-neutral-800 transition-all hover:border-primary pt-6">
    {{-- Featured image --}}
    @if ($post->featuredImageUrl('card'))
        <div class="aspect-video overflow-hidden bg-neutral-100 dark:bg-neutral-900 border-y border-neutral-200 dark:border-neutral-800 relative">
            <img
                src="{{ $post->featuredImageUrl('card') }}"
                alt="{{ $post->title }}"
                class="h-full w-full object-cover grayscale opacity-80 transition-all duration-500 group-hover:grayscale-0 group-hover:opacity-100"
                loading="lazy"
            />
            <div class="absolute top-2 right-2 flex gap-1">
                @if ($post->featured)
                    <span class="bg-primary/90 text-primary-fg px-1 text-[8px] font-bold uppercase tracking-widest">{{ __('Pinned') }}</span>
                @endif
            </div>
        </div>
    @endif

    {{-- Content --}}
    <div class="p-6 space-y-4">
        {{-- Meta --}}
        <div class="flex items-center justify-between text-[9px] uppercase tracking-[0.2em] text-neutral-400">
            <div class="flex items-center gap-3">
                <x-ui.icon name="ps:calendar-blank" class="size-3" />
                <time datetime="{{ $post->published_at->toIso8601String() }}">
                    {{ $post->published_at->format('Y.m.d') }}
                </time>
            </div>
            <div class="flex items-center gap-3">
                <x-ui.icon name="ps:clock" class="size-3" />
                <span>{{ $post->reading_time }}m</span>
            </div>
        </div>

        {{-- Title --}}
        <h3 class="line-clamp-2 text-xl font-bold text-neutral-900 dark:text-neutral-100 group-hover:text-primary transition-colors leading-tight uppercase tracking-tight">
            <a href="{{ route('posts.show', $post->slug) }}" wire:navigate>{{ $post->title }}</a>
        </h3>

        {{-- Excerpt --}}
        @if ($post->excerpt)
            <p class="line-clamp-2 text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed font-medium">
                {{ $post->excerpt }}
            </p>
        @endif

        {{-- Footer --}}
        <div class="pt-4 border-t border-neutral-100 dark:border-neutral-900 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1.5">
                    <x-ui.icon name="ps:eye" class="size-3 text-neutral-400" />
                    <span class="text-[9px] font-bold text-neutral-400">{{ number_format($post->view_count) }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <x-ui.icon name="ps:tag" class="size-3 text-neutral-400" />
                    <span class="text-[9px] font-bold text-neutral-400 uppercase tracking-widest">{{ $post->tags->first()?->name ?? 'System' }}</span>
                </div>
            </div>
            <x-ui.icon name="ps:arrow-up-right" class="size-4 text-neutral-300 group-hover:text-primary transition-all group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
        </div>
    </div>
</article>
