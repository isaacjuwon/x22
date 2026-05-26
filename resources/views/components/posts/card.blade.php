@props(['post'])

<article data-slot="card" class="group overflow-hidden transition-all hover:border-primary hover:shadow-lg">
    {{-- Featured image --}}
    @if ($post->featuredImageUrl('card'))
        <div class="aspect-video overflow-hidden bg-neutral-100 dark:bg-neutral-950">
            <img
                src="{{ $post->featuredImageUrl('card') }}"
                alt="{{ $post->title }}"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                loading="lazy"
            />
        </div>
    @else
        <div class="aspect-video flex items-center justify-center bg-neutral-100 dark:bg-neutral-950 border-b border-neutral-200 dark:border-neutral-800">
            <x-ui.icon name="document" class="h-12 w-12 text-neutral-300 dark:text-neutral-700" />
        </div>
    @endif

    {{-- Content --}}
    <div class="space-y-3 p-5">

        {{-- Meta --}}
        <div class="flex items-center justify-between text-xs font-medium uppercase tracking-wider">
            <x-ui.text class="text-neutral-400 dark:text-neutral-500">
                {{ $post->published_at->format('M d, Y') }}
            </x-ui.text>
            @if ($post->featured)
                <x-ui.badge color="primary" variant="soft" size="sm">{{ __('Featured') }}</x-ui.badge>
            @endif
        </div>

        {{-- Title --}}
        <h3 class="line-clamp-2 text-xl font-bold text-neutral-900 dark:text-neutral-50 transition-colors group-hover:text-primary">
            <a href="{{ route('posts.show', $post->slug) }}" wire:navigate>{{ $post->title }}</a>
        </h3>

        {{-- Excerpt --}}
        @if ($post->excerpt)
            <x-ui.text class="line-clamp-2 text-sm leading-relaxed text-neutral-500 dark:text-neutral-400">
                {{ $post->excerpt }}
            </x-ui.text>
        @endif

        {{-- Author & views --}}
        <div class="flex items-center justify-between border-t border-neutral-100 dark:border-neutral-800 pt-4">
            <div class="flex items-center gap-2">
                <x-ui.avatar :name="$post->user->name" size="xs" color="auto" />
                <x-ui.text class="text-xs font-semibold text-neutral-700 dark:text-neutral-300">
                    {{ $post->user->name }}
                </x-ui.text>
            </div>
            <x-ui.text class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-600">
                {{ number_format($post->view_count) }} {{ __('views') }}
            </x-ui.text>
        </div>

        {{-- Tags --}}
        @if ($post->tags->isNotEmpty())
            <div class="flex flex-wrap gap-1.5 pt-1">
                @foreach ($post->tags->take(2) as $tag)
                    <a href="{{ route('tags.show', $tag->slug) }}" wire:navigate>
                        <x-ui.badge size="sm" color="neutral" variant="outline">{{ $tag->name }}</x-ui.badge>
                    </a>
                @endforeach
            </div>
        @endif

    </div>
</article>
