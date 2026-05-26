@php
    use App\Enums\PostStatus;
    use App\Models\Post;
    use App\Support\ContentRenderer;

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
                <header class="mb-10 space-y-6">
                    <div class="flex items-center gap-3">
                        @if ($post->featured)
                            <x-ui.badge color="primary" variant="soft">{{ __('Featured') }}</x-ui.badge>
                        @endif
                        @foreach ($post->tags as $tag)
                            <x-ui.badge color="neutral" variant="outline" size="sm">{{ $tag->name }}</x-ui.badge>
                        @endforeach
                    </div>

                    <h1 class="text-4xl font-extrabold tracking-tight text-neutral-900 dark:text-neutral-50 lg:text-6xl">{{ $post->title }}</h1>

                    <div class="flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 pb-6 text-sm">
                        <div class="flex items-center gap-3">
                            <x-ui.avatar :name="$post->user->name" size="md" color="auto" />
                            <div>
                                <p class="font-bold text-neutral-900 dark:text-neutral-100">{{ $post->user->name }}</p>
                                <div class="flex items-center gap-2 text-neutral-500 dark:text-neutral-400">
                                    <time datetime="{{ $post->published_at->toIso8601String() }}">
                                        {{ $post->published_at->format('M d, Y') }}
                                    </time>
                                    <span>&middot;</span>
                                    <span>{{ $post->reading_time }} {{ trans_choice('min read|mins read', $post->reading_time) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <x-ui.text class="text-xs font-bold uppercase tracking-widest text-neutral-400">{{ number_format($post->view_count) }} {{ __('views') }}</x-ui.text>
                        </div>
                    </div>
                </header>

                {{-- Featured image --}}
                @if ($post->featuredImageUrl('hero'))
                    <figure class="mb-12 overflow-hidden rounded-3xl shadow-2xl">
                        <img
                            src="{{ $post->featuredImageUrl('hero') }}"
                            alt="{{ $post->title }}"
                            class="h-[32rem] w-full object-cover"
                        />
                    </figure>
                @endif

                {{-- AI Reading Assistant --}}
                <div class="mb-12">
                    <livewire:posts.summarizer :post="$post" />
                </div>

                {{-- Excerpt --}}
                @if ($post->excerpt)
                    <div class="mb-12 border-l-4 border-primary bg-primary/5 p-8 rounded-r-3xl">
                        <p class="text-2xl font-medium leading-relaxed text-neutral-700 dark:text-neutral-200">
                            {{ $post->excerpt }}
                        </p>
                    </div>
                @endif

                {{-- Content --}}
                <div class="tiptap-content mb-12 max-w-none">
                    {!! ContentRenderer::render($post->content) !!}
                </div>

                {{-- Gallery --}}
                @if ($post->galleryMedia()->isNotEmpty())
                    <section class="mb-16 space-y-6 border-t border-neutral-100 dark:border-neutral-800 pt-10">
                        <x-ui.heading level="h2" size="md" class="font-bold">{{ __('Gallery') }}</x-ui.heading>
                        <div class="grid gap-6 sm:grid-cols-2">
                            @foreach ($post->galleryMedia() as $galleryImage)
                                <div class="overflow-hidden rounded-2xl shadow-md transition-transform hover:scale-[1.02]">
                                    <img
                                        src="{{ $galleryImage->getUrl('card') }}"
                                        alt="{{ $galleryImage->name }}"
                                        class="h-64 w-full object-cover"
                                        loading="lazy"
                                    />
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Prev / Next navigation --}}
                <nav class="border-t border-neutral-100 dark:border-neutral-800 pt-12">
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            @if ($previousPost)
                                <a href="{{ route('posts.show', $previousPost->slug) }}" wire:navigate class="group flex flex-col gap-2">
                                    <x-ui.text class="text-xs font-bold uppercase tracking-widest text-neutral-400 group-hover:text-primary transition-colors">← {{ __('Previous') }}</x-ui.text>
                                    <x-ui.text class="text-lg font-bold text-neutral-900 dark:text-neutral-100 group-hover:text-primary transition-colors">
                                        {{ Str::limit($previousPost->title, 40) }}
                                    </x-ui.text>
                                </a>
                            @endif
                        </div>

                        <div class="text-right">
                            @if ($nextPost)
                                <a href="{{ route('posts.show', $nextPost->slug) }}" wire:navigate class="group flex flex-col items-end gap-2">
                                    <x-ui.text class="text-xs font-bold uppercase tracking-widest text-neutral-400 group-hover:text-primary transition-colors">{{ __('Next') }} →</x-ui.text>
                                    <x-ui.text class="text-lg font-bold text-neutral-900 dark:text-neutral-100 group-hover:text-primary transition-colors">
                                        {{ Str::limit($nextPost->title, 40) }}
                                    </x-ui.text>
                                </a>
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