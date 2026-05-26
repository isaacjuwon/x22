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
                <header class="mb-12 space-y-8">
                    <div class="flex items-center gap-4 text-[10px] uppercase tracking-[0.3em] text-neutral-400">
                        <time datetime="{{ $post->published_at->toIso8601String() }}">
                            {{ $post->published_at->format('Y.m.d') }}
                        </time>
                        <span class="opacity-30">|</span>
                        <span>{{ $post->reading_time }}m read</span>
                        @if ($post->featured)
                            <span class="opacity-30">|</span>
                            <span class="text-primary">{{ __('Featured') }}</span>
                        @endif
                    </div>

                    <h1 class="text-4xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50 lg:text-7xl leading-tight">
                        {{ $post->title }}
                    </h1>

                    <div class="flex items-center justify-between border-y border-neutral-100 dark:border-neutral-900 py-8">
                        <div class="flex items-center gap-4">
                            <x-ui.avatar :name="$post->user->name" size="lg" color="auto" />
                            <div class="space-y-1">
                                <p class="text-sm font-bold text-neutral-900 dark:text-neutral-100 uppercase tracking-widest">{{ $post->user->name }}</p>
                                <p class="text-xs text-neutral-500 lowercase">{{ __('Author / System Admin') }}</p>
                            </div>
                        </div>
                        <div class="text-right space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400">{{ number_format($post->view_count) }} {{ __('Total Views') }}</p>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-green-400">{{ __('Status: Active') }}</p>
                        </div>
                    </div>
                </header>

                {{-- Featured image --}}
                @if ($post->featuredImageUrl('hero'))
                    <figure class="mb-16 border border-neutral-200 dark:border-neutral-800 p-2">
                        <img
                            src="{{ $post->featuredImageUrl('hero') }}"
                            alt="{{ $post->title }}"
                            class="h-[36rem] w-full object-cover"
                        />
                        <figcaption class="mt-4 text-[10px] text-center uppercase tracking-[0.2em] text-neutral-400">
                            {{ __('Fig 1.0 — Primary Featured Asset') }}
                        </figcaption>
                    </figure>
                @endif

                {{-- AI Reading Assistant --}}
                <div class="mb-16 term-block border-l-4 border-l-primary">
                    <livewire:posts.summarizer :post="$post" />
                </div>

                {{-- Excerpt --}}
                @if ($post->excerpt)
                    <div class="mb-16 border-l-2 border-primary pl-8">
                        <p class="text-2xl font-medium leading-relaxed text-neutral-700 dark:text-neutral-200 italic">
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
                    <section class="mb-24 space-y-8 border-t border-neutral-100 dark:border-neutral-900 pt-16">
                        <div class="space-y-1">
                            <p class="term-comment text-[10px] uppercase tracking-[0.2em] text-neutral-400">{{ __('Attached Media') }}</p>
                            <x-ui.heading level="h2" size="md" class="font-bold uppercase tracking-tight">{{ __('Gallery Assets') }}</x-ui.heading>
                        </div>
                        <div class="grid gap-8 sm:grid-cols-2">
                            @foreach ($post->galleryMedia() as $galleryImage)
                                <div class="border border-neutral-200 dark:border-neutral-800 p-2 transition-colors hover:border-primary">
                                    <img
                                        src="{{ $galleryImage->getUrl('card') }}"
                                        alt="{{ $galleryImage->name }}"
                                        class="h-72 w-full object-cover grayscale transition-[filter] hover:grayscale-0"
                                        loading="lazy"
                                    />
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Prev / Next navigation --}}
                <nav class="border-t border-neutral-100 dark:border-neutral-900 pt-16">
                    <div class="grid grid-cols-2 gap-12">
                        <div>
                            @if ($previousPost)
                                <a href="{{ route('posts.show', $previousPost->slug) }}" wire:navigate class="group space-y-4 block">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-neutral-400 group-hover:text-primary transition-colors">{{ __('← Previous Module') }}</p>
                                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100 leading-tight">
                                        {{ $previousPost->title }}
                                    </p>
                                </a>
                            @endif
                        </div>

                        <div class="text-right">
                            @if ($nextPost)
                                <a href="{{ route('posts.show', $nextPost->slug) }}" wire:navigate class="group space-y-4 block">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-neutral-400 group-hover:text-primary transition-colors">{{ __('Next Module →') }}</p>
                                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100 leading-tight">
                                        {{ $nextPost->title }}
                                    </p>
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