@props(['post'])

<article data-slot="card" class="group relative flex flex-col overflow-hidden rounded-[2.5rem] border border-neutral-200 bg-neutral-100 transition-all duration-500 hover:-translate-y-2 hover:border-primary/50 hover:shadow-[0_20px_50px_-12px_rgba(139,92,246,0.3)]">

    {{-- Featured image --}}
    @if ($post->featuredImageUrl('card'))
        <div class="relative aspect-[16/9] overflow-hidden">
            <img
                src="{{ $post->featuredImageUrl('card') }}"
                alt="{{ $post->title }}"
                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-neutral-100 to-transparent opacity-60"></div>
            
            {{-- Floating Category/Tag --}}
            @if ($post->tags->count())
                <div class="absolute left-6 top-6">
                    <span class="rounded-full bg-black/40 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-white backdrop-blur-md border border-white/10">
                        {{ $post->tags->first()->name }}
                    </span>
                </div>
            @endif
        </div>
    @endif

    <div class="flex flex-1 flex-col gap-6 p-8">
        {{-- Meta Row --}}
        <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-500">
            <div class="flex items-center gap-2">
                <span class="h-1 w-1 rounded-full bg-primary"></span>
                <time datetime="{{ $post->published_at?->toIso8601String() }}">
                    {{ $post->published_at?->format('M d, Y') }}
                </time>
            </div>
            <span>{{ $post->reading_time ?? 5 }} min read</span>
        </div>

        {{-- Title --}}
        <h3 class="text-2xl font-bold tracking-tight text-white transition-colors group-hover:text-primary leading-[1.2]">
            <a href="{{ route('posts.show', $post->slug) }}" wire:navigate>
                {{ $post->title }}
            </a>
        </h3>

        {{-- Excerpt --}}
        @if ($post->excerpt)
            <p class="line-clamp-3 text-sm leading-relaxed text-neutral-400/90">
                {{ $post->excerpt }}
            </p>
        @endif

        <div class="mt-auto flex items-center justify-between pt-4">
            {{-- Author --}}
            <div class="flex items-center gap-3">
                <x-ui.avatar :name="$post->user->name" size="xs" class="ring-2 ring-neutral-200" />
                <span class="text-xs font-bold text-neutral-300">{{ $post->user->name }}</span>
            </div>

            {{-- Action Icon --}}
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-neutral-200 text-neutral-400 transition-all group-hover:bg-primary group-hover:text-white">
                <x-ui.icon name="arrow-right" class="size-5 transition-transform group-hover:translate-x-0.5" />
            </div>
        </div>
    </div>
</article>

