@props(['post'])

<article data-slot="card" class="group flex flex-col overflow-hidden transition-all duration-200 hover:border-green-700 dark:hover:border-green-600">

    {{-- Featured image --}}
    @if ($post->featuredImageUrl('card'))
        <div class="overflow-hidden border-b border-neutral-200 dark:border-neutral-800">
            <img
                src="{{ $post->featuredImageUrl('card') }}"
                alt="{{ $post->title }}"
                class="h-44 w-full object-cover transition-transform duration-500 group-hover:scale-105"
            >
        </div>
    @endif

    <div class="flex flex-1 flex-col gap-3 p-5">

        {{-- Tags --}}
        @if ($post->tags->count())
            <div class="flex flex-wrap gap-1.5">
                @foreach ($post->tags->take(3) as $tag)
                    <x-ui.badge wire:key="tag-{{ $tag->id }}" color="green" variant="outline" size="sm">
                        {{ $tag->name }}
                    </x-ui.badge>
                @endforeach
            </div>
        @endif

        {{-- Title --}}
        <x-ui.heading level="h3" size="md" class="line-clamp-2 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
            <a href="{{ route('posts.show', $post->slug) }}" wire:navigate class="stretched-link">
                {{ $post->title }}
            </a>
        </x-ui.heading>

        {{-- Excerpt --}}
        @if ($post->excerpt)
            <x-ui.text class="line-clamp-2 flex-1 text-sm leading-relaxed">
                {{ $post->excerpt }}
            </x-ui.text>
        @endif

        <x-ui.separator />

        {{-- Meta --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-ui.avatar :name="$post->user->name" size="xs" color="auto" />
                <div class="flex flex-col">
                    <x-ui.text class="text-xs font-medium">{{ $post->user->name }}</x-ui.text>
                    <x-ui.text class="text-xs text-neutral-500 dark:text-neutral-500">
                        {{ $post->published_at?->diffForHumans() ?? __('Draft') }}
                    </x-ui.text>
                </div>
            </div>

            <div class="flex items-center gap-1 text-neutral-500">
                <x-ui.icon name="eye" class="h-3.5 w-3.5" />
                <x-ui.text class="text-xs">{{ number_format($post->view_count) }}</x-ui.text>
            </div>
        </div>
    </div>
</article>
