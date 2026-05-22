<x-flux::card class="hover:shadow-lg transition-shadow">
    <div class="space-y-3">
        @if ($post->featuredImageUrl('card'))
            <img src="{{ $post->featuredImageUrl('card') }}" alt="{{ $post->title }}" class="w-full h-48 object-cover rounded-lg">
        @endif

        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white line-clamp-2">
            {{ $post->title }}
        </h3>

        @if ($post->excerpt)
            <p class="text-sm text-neutral-50 dark:text-neutral-400 line-clamp-2">
                {{ $post->excerpt }}
            </p>
        @endif

        <div class="flex items-center gap-2 text-xs text-neutral-500 dark:text-neutral-500">
            <span>{{ $post->user->name }}</span>
            <span>•</span>
            <span>{{ $post->published_at?->diffForHumans() ?? 'Not published' }}</span>
            <span>•</span>
            <span>{{ $post->view_count }} views</span>
        </div>

        @if ($post->tags->count())
            <div class="flex flex-wrap gap-1">
                @foreach ($post->tags as $tag)
                    <x-flux::badge size="sm" variant="neutral">
                        {{ $tag->name }}
                    </x-flux::badge>
                @endforeach
            </div>
        @endif

        <a href="{{ route('posts.show', $post->slug) }}" class="inline-block mt-2">
            <x-flux::button size="sm">
                Read More
            </x-flux::button>
        </a>
    </div>
</x-flux::card>
