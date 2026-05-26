@props(['post'])

<article data-slot="card" class="group flex flex-col gap-6">
    {{-- Featured image --}}
    @if ($post->featuredImageUrl('card'))
        <div class="aspect-[16/10] overflow-hidden rounded-[1.2rem] bg-neutral-100 p-1 transition-all">
            <img
                src="{{ $post->featuredImageUrl('card') }}"
                alt="{{ $post->title }}"
                class="h-full w-full object-cover rounded-[0.9rem] grayscale group-hover:grayscale-0 transition-all duration-500"
                loading="lazy"
            />
        </div>
    @endif

    {{-- Content --}}
    <div class="space-y-4 px-1">
        {{-- Meta --}}
        <div class="flex items-center gap-4 text-skeleton-meta">
            <time datetime="{{ $post->published_at->toIso8601String() }}">
                {{ $post->published_at->format('M d, Y') }}
            </time>
            <span class="text-neutral-300">•</span>
            <span>{{ $post->reading_time }} min read</span>
        </div>

        {{-- Title --}}
        <h3 class="text-skeleton-title transition-colors group-hover:text-primary leading-tight">
            <a href="{{ route('posts.show', $post->slug) }}" wire:navigate class="hover:underline decoration-1 underline-offset-4">
                {{ $post->title }}
            </a>
        </h3>

        {{-- Excerpt --}}
        @if ($post->excerpt)
            <p class="text-skeleton-body line-clamp-2">
                {{ $post->excerpt }}
            </p>
        @endif
    </div>

    {{-- Footer --}}
    <div class="flex items-center justify-between pt-2 px-1">
        <div class="flex items-center gap-4 text-skeleton-meta">
            <div class="flex items-center gap-1.5">
                <x-ui.icon name="ps:eye" class="size-3.5" />
                <span>{{ number_format($post->view_count) }}</span>
            </div>
            <span class="text-neutral-300">/</span>
            <span>{{ $post->published_at->diffForHumans() }}</span>
        </div>
        
        <x-ui.icon name="ps:arrow-up-right" class="size-4 text-neutral-300 group-hover:text-primary transition-all group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
    </div>
</article>
