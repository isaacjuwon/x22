@props(['post'])

<article data-slot="card" class="group border border-neutral-200 dark:border-neutral-800 transition-colors hover:border-primary">
    {{-- Featured image --}}
    @if ($post->featuredImageUrl('card'))
        <div class="aspect-video overflow-hidden bg-neutral-100 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-800">
            <img
                src="{{ $post->featuredImageUrl('card') }}"
                alt="{{ $post->title }}"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
            />
        </div>
    @endif

    {{-- Content --}}
    <div class="p-6 space-y-4">
        {{-- Meta --}}
        <div class="flex items-center gap-4 text-[10px] uppercase tracking-[0.2em] text-neutral-400">
            <time datetime="{{ $post->published_at->toIso8601String() }}">
                {{ $post->published_at->format('Y.m.d') }}
            </time>
            <span class="opacity-30">|</span>
            <span>{{ $post->reading_time }}m read</span>
        </div>

        {{-- Title --}}
        <h3 class="line-clamp-2 text-xl font-bold text-neutral-900 dark:text-neutral-100 group-hover:text-primary transition-colors leading-tight">
            <a href="{{ route('posts.show', $post->slug) }}" wire:navigate>{{ $post->title }}</a>
        </h3>

        {{-- Excerpt --}}
        @if ($post->excerpt)
            <p class="line-clamp-2 text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                {{ $post->excerpt }}
            </p>
        @endif

        {{-- Footer --}}
        <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="term-dot term-dot-success text-[10px] uppercase tracking-widest text-neutral-400">{{ __('Live') }}</span>
            </div>
            <x-ui.icon name="arrow-right" class="size-4 text-neutral-300 group-hover:text-primary transition-colors group-hover:translate-x-1" />
        </div>
    </div>
</article>
