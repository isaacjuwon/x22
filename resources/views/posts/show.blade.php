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
    <div class="min-h-screen bg-neutral-50" 
         x-data="{ ...readingProgress(), ...tableOfContents() }" 
         @scroll.window="update()" 
         x-init="update(); $nextTick(() => init())">
        
        {{-- Reading Progress Bar --}}
        <div class="fixed top-0 left-0 z-50 h-1 bg-primary transition-all duration-150" :style="`width: ${progress}%`" x-show="progress > 0"></div>

        <div class="container mx-auto px-6 py-20">
            <article class="mx-auto max-w-3xl space-y-16">
                
                {{-- Modernized Meta Section --}}
                <header class="space-y-8 text-center">
                    <div class="flex flex-col items-center gap-6">
                        <x-ui.badge variant="outline" class="rounded-full px-4 py-1 text-[10px] font-bold tracking-widest uppercase border-neutral-200 text-neutral-500">
                            {{ $post->tags->first()?->name ?? __('Article') }}
                        </x-ui.badge>
                    </div>

                    <h1 class="text-4xl font-bold tracking-tight text-neutral-950 sm:text-6xl leading-[1.05]">
                        {{ $post->title }}
                    </h1>

                    <div class="flex flex-col items-center gap-6 pt-4">
                        <div class="flex items-center gap-3">
                            <x-ui.avatar :name="$post->user->name" size="sm" class="rounded-full" />
                            <span class="font-bold text-neutral-900">{{ $post->user->name }}</span>
                        </div>
                        
                        <div class="flex items-center gap-3 text-skeleton-meta">
                            <time datetime="{{ $post->published_at->toIso8601String() }}">
                                {{ $post->published_at->format('F d, Y') }}
                            </time>
                            <span class="text-neutral-300">/</span>
                            <span>{{ $post->reading_time }} min read</span>
                        </div>
                    </div>
                </header>

                {{-- Table of Contents (Subtle Floating) --}}
                <template x-if="visible">
                    <div class="fixed left-8 top-1/2 -translate-y-1/2 hidden xl:block w-64 space-y-4">
                        <p class="text-[10px] font-bold tracking-widest uppercase text-neutral-400">{{ __('On this page') }}</p>
                        <nav class="space-y-2 border-l border-neutral-200">
                            <template x-for="item in items" :key="item.id">
                                <a :href="'#' + item.id" 
                                   class="block pl-4 py-1 text-sm transition-all border-l -ml-px"
                                   :class="activeId === item.id ? 'text-primary border-primary font-medium' : 'text-neutral-500 border-transparent hover:text-neutral-800 hover:border-neutral-300'"
                                   x-text="item.text"></a>
                            </template>
                        </nav>
                    </div>
                </template>

                {{-- Featured Image --}}
                @if ($post->featuredImageUrl('hero'))
                    <figure class="rounded-[2.5rem] overflow-hidden bg-neutral-100 shadow-sm">
                        <img
                            src="{{ $post->featuredImageUrl('hero') }}"
                            alt="{{ $post->title }}"
                            class="aspect-[16/9] w-full object-cover"
                        />
                    </figure>
                @endif

                {{-- Thread Content --}}
                <div class="space-y-12">
                    @if ($post->excerpt)
                        <div class="text-2xl font-medium text-neutral-600 leading-relaxed tracking-tight border-l-2 border-primary/20 pl-8 py-2 italic">
                            {{ $post->excerpt }}
                        </div>
                    @endif

                    <div class="tiptap-content text-xl leading-relaxed text-neutral-800">
                        {!! ContentRenderer::render($post->content_json ?: $post->content) !!}
                    </div>
                </div>

                {{-- Tags --}}
                @if($post->tags->isNotEmpty())
                    <div class="flex flex-wrap gap-2 pt-12 border-t border-neutral-200/50">
                        @foreach($post->tags as $tag)
                            <x-ui.badge variant="outline" class="rounded-full px-5 py-1.5 text-xs font-medium border-neutral-200 text-neutral-500 hover:border-neutral-400 transition-colors">
                                #{{ $tag->name }}
                            </x-ui.badge>
                        @endforeach
                    </div>
                @endif

                {{-- Post Navigation --}}
                <div class="grid grid-cols-2 gap-8 pt-16 border-t border-neutral-200">
                    <div>
                        @if ($previousPost)
                            <a href="{{ route('posts.show', $previousPost->slug) }}" wire:navigate class="group space-y-3 block">
                                <span class="text-skeleton-meta group-hover:text-primary transition-colors flex items-center gap-2">
                                    <x-ui.icon name="ps:arrow-left" class="size-3" />
                                    {{ __('Previous') }}
                                </span>
                                <span class="block text-lg font-bold text-neutral-900 line-clamp-2 group-hover:underline">{{ $previousPost->title }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="text-right">
                        @if ($nextPost)
                            <a href="{{ route('posts.show', $nextPost->slug) }}" wire:navigate class="group space-y-3 block">
                                <span class="text-skeleton-meta group-hover:text-primary transition-colors flex items-center justify-end gap-2">
                                    {{ __('Next') }}
                                    <x-ui.icon name="ps:arrow-right" class="size-3" />
                                </span>
                                <span class="block text-lg font-bold text-neutral-900 line-clamp-2 group-hover:underline">{{ $nextPost->title }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Discussion --}}
                <section class="pt-24 space-y-12">
                    <div class="flex items-center justify-between">
                        <h2 class="text-3xl font-bold tracking-tight text-neutral-950">{{ __('Comments') }}</h2>
                        <div class="h-px flex-1 mx-8 bg-neutral-200/50"></div>
                        <span class="text-skeleton-meta">{{ __('Join the conversation') }}</span>
                    </div>
                    
                    <livewire:posts.comments :post="$post" defer />
                </section>

            </article>
        </div>
    </div>
</x-layouts::main>
