@props(['post'])

<article data-slot="card" class="group flex flex-col overflow-hidden rounded-[2rem] border border-neutral-900 bg-neutral-900/50 transition-all duration-300 hover:border-primary/50 hover:bg-neutral-900">

    {{-- Featured image --}}
    @if ($post->featuredImageUrl('card'))
        <div class="overflow-hidden border-b border-neutral-900">
            <img
                src="{{ $post->featuredImageUrl('card') }}"
                alt="{{ $post->title }}"
                class="h-48 w-full object-cover transition-transform duration-500 group-hover:scale-110"
            >
        </div>
    @endif

    <div class="flex flex-1 flex-col gap-5 p-8">

        {{-- Tags --}}
        @if ($post->tags->count())
            <div class="flex flex-wrap gap-2">
                @foreach ($post->tags->take(3) as $tag)
                    <x-ui.badge wire:key="tag-{{ $tag->id }}" variant="outline" class="border-primary/30 text-primary bg-primary/5 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-widest">
                        {{ $tag->name }}
                    </x-ui.badge>
                @endforeach
            </div>
        @endif

        {{-- Title --}}
        <h3 class="text-xl font-bold tracking-tight text-white group-hover:text-primary transition-colors line-clamp-2 leading-tight">
            <a href="{{ route('posts.show', $post->slug) }}" wire:navigate class="stretched-link">
                {{ $post->title }}
            </a>
        </h3>

        {{-- Excerpt --}}
        @if ($post->excerpt)
            <p class="line-clamp-2 flex-1 text-sm leading-relaxed text-neutral-400">
                {{ $post->excerpt }}
            </p>
        @endif

        <div class="h-px w-full bg-neutral-900"></div>

        {{-- Meta --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <x-ui.avatar :name="$post->user->name" size="xs" class="ring-1 ring-neutral-800" />
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-white">{{ $post->user->name }}</span>
                    <span class="text-[10px] font-medium text-neutral-500 uppercase tracking-widest">
                        {{ $post->published_at?->diffForHumans() ?? __('Draft') }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-1.5 text-neutral-500">
                <x-ui.icon name="heroicon-o-eye" class="h-4 w-4" />
                <span class="text-xs font-bold">{{ number_format($post->view_count) }}</span>
            </div>
        </div>
    </div>
</article>

