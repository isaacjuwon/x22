@php
    use App\Enums\PostStatus;
    use App\Models\Post;

    abort_unless(
        $post->status === PostStatus::Published
            && $post->published_at !== null
            && $post->published_at->isPast(),
        404
    );

    $post->load('tags', 'user', 'media');
    $post->incrementViewCount();

    $tagIds = $post->tags->pluck('id');

    $relatedPosts = Post::published()
        ->whereNot('id', $post->id)
        ->when($tagIds->isNotEmpty(), fn ($q) => $q->whereHas(
            'tags',
            fn ($q) => $q->whereIn('tags.id', $tagIds)
        ))
        ->with('tags', 'user', 'media')
        ->latest('published_at')
        ->limit(2)
        ->get();

    if ($relatedPosts->isEmpty()) {
        $relatedPosts = Post::published()
            ->whereNot('id', $post->id)
            ->with('tags', 'user', 'media')
            ->latest('published_at')
            ->limit(2)
            ->get();
    }

    $previousPost = Post::published()
        ->where('published_at', '<', $post->published_at)
        ->latest('published_at')
        ->first(['id', 'title', 'slug']);

    $nextPost = Post::published()
        ->where('published_at', '>', $post->published_at)
        ->oldest('published_at')
        ->first(['id', 'title', 'slug']);
@endphp

<x-layouts::main :title="$post->title">
    <x-slot:head>
        @if ($post->excerpt)
            <meta name="description" content="{{ Str::limit(strip_tags($post->excerpt), 160) }}" />
            <meta property="og:description" content="{{ Str::limit(strip_tags($post->excerpt), 160) }}" />
            <meta name="twitter:description" content="{{ Str::limit(strip_tags($post->excerpt), 160) }}" />
        @endif

        {{-- Use featured image for OG (SEO OG image upload removed) --}}
        @if ($post->featuredImageUrl())
            <meta property="og:image" content="{{ $post->featuredImageUrl() }}" />
            <meta name="twitter:image" content="{{ $post->featuredImageUrl() }}" />
        @endif

        <meta property="og:type" content="article" />
        <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}" />
        @foreach ($post->tags as $tag)
            <meta property="article:tag" content="{{ $tag->name }}" />
        @endforeach

        <link rel="canonical" href="{{ route('posts.show', $post->slug) }}" />
    </x-slot:head>

    <div class="min-h-screen">
        <div class="container mx-auto px-4 py-12">

            <article class="mx-auto mb-12 max-w-3xl">

                {{-- Back link --}}
                <div class="mb-6">
                    <x-ui.button as="a" href="{{ route('posts.index') }}" wire:navigate variant="ghost" size="sm" icon="arrow-left">
                        {{ __('Back to Posts') }}
                    </x-ui.button>
                </div>

                {{-- Header --}}
                <header class="mb-8 space-y-4">
                    @if ($post->featured)
                        <x-ui.badge color="neutral">{{ __('Featured') }}</x-ui.badge>
                    @endif

                    <h1 class="text-4xl font-bold text-neutral-500">{{ $post->title }}</h1>

                    <div class="flex items-center justify-between border-b border-neutral-800 pb-4 text-sm text-neutral-400">
                        <div class="flex items-center gap-3">
                            <x-ui.avatar :name="$post->user->name" size="sm" color="auto" />
                            <div>
                                <p class="font-medium text-neutral-400">{{ $post->user->name }}</p>
                                <time datetime="{{ $post->published_at->toIso8601String() }}">
                                    {{ $post->published_at->format('M d, Y') }}
                                </time>
                            </div>
                        </div>
                        <x-ui.text class="text-sm">{{ number_format($post->view_count) }} {{ __('views') }}</x-ui.text>
                    </div>
                </header>

                {{-- Featured image --}}
                @if ($post->featuredImageUrl('hero'))
                    <figure class="mb-8 overflow-hidden rounded-xl">
                        <img
                            src="{{ $post->featuredImageUrl('hero') }}"
                            alt="{{ $post->title }}"
                            class="h-96 w-full object-cover"
                        />
                    </figure>
                @endif

                {{-- AI Reading Assistant --}}
                <livewire:posts.summarizer :post="$post" />

                {{-- Excerpt --}}
                @if ($post->excerpt)
                    <p class="mb-8 border-l-4 border-blue-500 pl-6 text-xl italic text-neutral-400">
                        {{ $post->excerpt }}
                    </p>
                @endif

                {{-- Content --}}
                <div class="prose prose-lg mb-12 max-w-none dark:prose-invert">
                    {!! $post->content !!}
                </div>

                {{-- Gallery --}}
                @if ($post->galleryMedia()->isNotEmpty())
                    <section class="mb-12 space-y-4 border-t border-neutral-800 pt-6">
                        <x-ui.heading level="h2" size="md">{{ __('Gallery') }}</x-ui.heading>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($post->galleryMedia() as $galleryImage)
                                <div class="overflow-hidden rounded-xl border border-neutral-800">
                                    <img
                                        src="{{ $galleryImage->getUrl('card') }}"
                                        alt="{{ $galleryImage->name }}"
                                        class="h-56 w-full object-cover"
                                        loading="lazy"
                                    />
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Tags --}}
                @if ($post->tags->isNotEmpty())
                    <div class="mb-12 space-y-3 border-t border-neutral-800 pt-6">
                        <x-ui.text class="font-semibold">{{ __('Tags') }}</x-ui.text>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($post->tags as $tag)
                                <x-ui.badge color="neutral" variant="outline">{{ $tag->name }}</x-ui.badge>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Prev / Next navigation --}}
                <nav class="border-t border-neutral-800 pt-8">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            @if ($previousPost)
                                <a href="{{ route('posts.show', $previousPost->slug) }}" wire:navigate class="flex flex-col gap-1">
                                    <x-ui.text class="text-sm text-neutral-500">← {{ __('Previous') }}</x-ui.text>
                                    <x-ui.text class="font-semibold hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ Str::limit($previousPost->title, 40) }}
                                    </x-ui.text>
                                </a>
                            @else
                                <div class="opacity-40">
                                    <x-ui.text class="text-sm text-neutral-500">← {{ __('Previous') }}</x-ui.text>
                                    <x-ui.text>{{ __('No previous post') }}</x-ui.text>
                                </div>
                            @endif
                        </div>

                        <div class="text-right">
                            @if ($nextPost)
                                <a href="{{ route('posts.show', $nextPost->slug) }}" wire:navigate class="flex flex-col items-end gap-1">
                                    <x-ui.text class="text-sm text-neutral-500">{{ __('Next') }} →</x-ui.text>
                                    <x-ui.text class="font-semibold hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ Str::limit($nextPost->title, 40) }}
                                    </x-ui.text>
                                </a>
                            @else
                                <div class="text-right opacity-40">
                                    <x-ui.text class="text-sm text-neutral-500">{{ __('Next') }} →</x-ui.text>
                                    <x-ui.text>{{ __('No next post') }}</x-ui.text>
                                </div>
                            @endif
                        </div>
                    </div>
                </nav>

            </article>

            {{-- Comments --}}
            <section class="mx-auto max-w-3xl border-t border-neutral-800 pt-12">
                <x-ui.heading level="h2" size="lg" class="mb-6">{{ __('Comments') }}</x-ui.heading>
                <livewire:posts.comments :post="$post" defer />
            </section>

            {{-- Related posts --}}
            @if ($relatedPosts->isNotEmpty())
                <section class="mx-auto mt-16 max-w-3xl border-t border-neutral-800 pt-12">
                    <x-ui.heading level="h2" size="lg" class="mb-6">{{ __('Related Posts') }}</x-ui.heading>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        @foreach ($relatedPosts as $relatedPost)
                            <x-posts.card :post="$relatedPost" />
                        @endforeach
                    </div>
                </section>
            @endif

        </div>
    </div>
</x-layouts::main>