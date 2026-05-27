@props(['post'])

<article data-slot="card" class="group flex flex-col gap-6 border-none bg-transparent">
    {{-- Featured image --}}
    @if ($post->featuredImageUrl('card'))
        <div class="aspect-[16/10] overflow-hidden rounded-[2rem] bg-neutral-900 p-1 transition-all">
            <img
                src="{{ $post->featuredImageUrl('card') }}"
                alt="{{ $post->title }}"
                class="h-full w-full object-cover rounded-[1.8rem] transition-all duration-500 group-hover:scale-105"
                loading="lazy"
            />
        </div>
    @endif

    {{-- Content --}}
    <div class="space-y-4 px-1">
        {{-- Meta --}}
        <div class="flex items-center gap-4 text-xs font-bold uppercase tracking-widest text-neutral-500">
            <time datetime="{{ $post->published_at->toIso8601String() }}" class="text-primary/70">
                {{ $post->published_at->format('M d, Y') }}
            </time>
            <span class="text-neutral-800">•</span>
            <span>{{ $post->reading_time }} min read</span>
        </div>

        {{-- Title --}}
        <h3 class="text-2xl font-bold tracking-tight text-white transition-colors group-hover:text-primary leading-tight">
            <a href="{{ route('posts.show', $post->slug) }}" wire:navigate>
                {{ $post->title }}
            </a>
        </h3>

        {{-- Excerpt --}}
        @if ($post->excerpt)
            <p class="text-sm leading-relaxed text-neutral-400 line-clamp-2">
                {{ $post->excerpt }}
            </p>
        @endif
    </div>

    {{-- Footer --}}
    <div class="flex items-center justify-between pt-2 px-1">
        <div class="flex items-center gap-4 text-[10px] font-bold uppercase tracking-widest text-neutral-600">
            <div class="flex items-center gap-1.5">
                <x-ui.icon name="eye" class="size-3.5" />
                <span>{{ number_format($post->view_count) }}</span>
            </div>
            <span class="text-neutral-800">/</span>
            <span>{{ $post->published_at->diffForHumans() }}</span>
        </div>
        
        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-neutral-900 text-neutral-500 transition-all group-hover:bg-primary group-hover:text-white">
            <x-ui.icon name="arrow-up-right" class="size-4 transition-all group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
        </div>
    </div>
</article>

