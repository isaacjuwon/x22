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

    <div class="min-h-screen" x-data="readingProgress" @scroll.window="update()" x-init="update()">
        {{-- Reading Progress Bar --}}
        <div class="fixed top-0 left-0 z-50 h-1 bg-primary transition-all duration-150" :style="`width: ${progress}%`" x-show="progress > 0"></div>

        <div class="container mx-auto px-4 py-12" x-data="tableOfContents">
            {{-- Floating Table of Contents --}}
            <aside 
                class="fixed left-8 top-1/2 -translate-y-1/2 z-40 hidden xl:block w-64"
                x-show="visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0"
            >
                <div class="space-y-4 border-l-2 border-neutral-100 dark:border-neutral-900 pl-6">
                    <p class="text-[10px] uppercase tracking-[0.3em] text-neutral-400 mb-4">{{ __('On this page') }}</p>
                    <nav class="flex flex-col gap-3">
                        <template x-for="item in items" :key="item.id">
                            <a 
                                :href="`#${item.id}`"
                                class="text-xs font-bold uppercase tracking-widest transition-all hover:text-primary"
                                :class="activeId === item.id ? 'text-primary translate-x-2' : 'text-neutral-500 dark:text-neutral-400'"
                                x-text="item.text"
                            ></a>
                        </template>
                    </nav>
                </div>
            </aside>

            <article class="mx-auto mb-12 max-w-3xl">

                {{-- Back link --}}
                <div class="mb-6">
                    <x-ui.button as="a" href="{{ route('posts.index') }}" wire:navigate variant="ghost" size="sm" icon="arrow-left">
                        {{ __('Back to Posts') }}
                    </x-ui.button>
                </div>

                {{-- Header --}}
                <header class="mb-16 space-y-10">
                    <div class="flex items-center gap-6 text-[10px] uppercase tracking-[0.4em] text-neutral-400 font-bold">
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="ps:calendar-blank" class="size-3 text-primary" />
                            <time datetime="{{ $post->published_at->toIso8601String() }}">
                                {{ $post->published_at->format('Y.m.d') }}
                            </time>
                        </div>
                        <span class="opacity-20">|</span>
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="ps:clock" class="size-3 text-primary" />
                            <span>{{ $post->reading_time }}m read</span>
                        </div>
                        @if ($post->featured)
                            <span class="opacity-20">|</span>
                            <span class="text-primary">{{ __('Pinned Asset') }}</span>
                        @endif
                    </div>

                    <h1 class="text-5xl font-bold tracking-tighter text-neutral-900 dark:text-neutral-50 lg:text-8xl leading-[0.85] uppercase">
                        {{ $post->title }}
                    </h1>

                    <div class="flex items-center justify-between border-y border-neutral-100 dark:border-neutral-900 py-10">
                        <div class="flex items-center gap-6">
                            <x-ui.avatar :name="$post->user->name" size="xl" color="auto" class="rounded-none border-2 border-primary p-1" />
                            <div class="space-y-1">
                                <p class="text-sm font-bold text-neutral-900 dark:text-neutral-100 uppercase tracking-[0.2em]">{{ $post->user->name }}</p>
                                <p class="text-[10px] text-neutral-500 uppercase tracking-widest">{{ __('System Administrator') }}</p>
                            </div>
                        </div>
                        <div class="text-right space-y-2">
                            <div class="flex items-center justify-end gap-3">
                                <x-ui.icon name="ps:eye" class="size-4 text-neutral-400" />
                                <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-neutral-400">{{ number_format($post->view_count) }}</p>
                            </div>
                            <span class="term-indicator text-success text-[9px] uppercase font-bold tracking-widest">{{ __('Active Module') }}</span>
                        </div>
                    </div>
                </header>

                {{-- Featured image --}}
                @if ($post->featuredImageUrl('hero'))
                    <figure class="mb-20 border border-neutral-200 dark:border-neutral-800 p-3 bg-neutral-50 dark:bg-[#080808]">
                        <img
                            src="{{ $post->featuredImageUrl('hero') }}"
                            alt="{{ $post->title }}"
                            class="h-[40rem] w-full object-cover grayscale opacity-90 transition-all hover:grayscale-0 hover:opacity-100 duration-700"
                        />
                        <figcaption class="mt-6 flex items-center justify-center gap-4 text-[9px] uppercase tracking-[0.4em] text-neutral-400 font-bold">
                            <span class="opacity-20">---</span>
                            {{ __('Fig 1.0 — Primary Media Asset') }}
                            <span class="opacity-20">---</span>
                        </figcaption>
                    </figure>
                @endif

                {{-- AI Reading Assistant --}}
                <div class="mb-20 term-block border-l-4 border-l-primary relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-30 transition-opacity">
                        <x-ui.icon name="ps:brain" class="size-20" />
                    </div>
                    <livewire:posts.summarizer :post="$post" />
                </div>

                {{-- Excerpt --}}
                @if ($post->excerpt)
                    <div class="mb-20 border-l-2 border-neutral-200 dark:border-neutral-800 pl-12">
                        <p class="text-3xl font-medium leading-tight text-neutral-700 dark:text-neutral-300 italic tracking-tight">
                            <span class="text-primary mr-2">//</span> {{ $post->excerpt }}
                        </p>
                    </div>
                @endif

                {{-- Content --}}
                <div class="tiptap-content mb-12 max-w-none">
                    {!! ContentRenderer::render($post->content_json ?: $post->content) !!}
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