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
    <div class="min-h-screen bg-neutral-50 dark:bg-[#0a0a0a] transition-colors duration-300 font-mono" 
         x-data="{ ...readingProgress(), ...tableOfContents() }" 
         @scroll.window="update()" 
         x-init="update(); $nextTick(() => init())">
        
        {{-- Reading Progress Bar --}}
        <div class="fixed top-0 left-0 z-[60] h-1 bg-primary transition-all duration-150" :style="`width: ${progress}%`" x-show="progress > 0"></div>

        <div class="container mx-auto px-6 py-20">
            <article class="mx-auto max-w-4xl space-y-16">
                
                {{-- Modernized Meta Section --}}
                <header class="space-y-8">
                    <nav class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-[0.3em] text-neutral-400">
                        <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors">~/root</a>
                        <span class="opacity-30">/</span>
                        <a href="{{ route('posts.index') }}" wire:navigate class="hover:text-primary transition-colors">blog</a>
                        <span class="opacity-30">/</span>
                        <span class="text-primary">{{ $post->slug }}.md</span>
                    </nav>

                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="px-3 py-1 bg-primary text-black text-[10px] font-bold uppercase">
                                {{ $post->tags->first()?->name ?? __('Article') }}
                            </div>
                            <div class="text-[10px] font-bold uppercase tracking-widest text-neutral-400">
                                status: <span class="text-primary">published</span>
                            </div>
                        </div>

                        <h1 class="text-4xl font-black tracking-tighter text-neutral-900 dark:text-white sm:text-7xl leading-none uppercase">
                            {{ $post->title }}
                        </h1>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-neutral-200 dark:border-neutral-800">
                        <div class="flex items-center gap-4">
                            <x-ui.avatar :name="$post->user->name" size="xs" class="ring-2 ring-neutral-100 dark:ring-neutral-800" />
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-neutral-900 dark:text-white uppercase tracking-widest">{{ $post->user->name }}</span>
                                <span class="text-[9px] font-bold text-neutral-400 uppercase tracking-[0.2em]">Author_Index</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-6 text-[10px] font-bold uppercase tracking-widest text-neutral-400">
                            <div class="flex items-center gap-2">
                                <x-ui.icon name="calendar" class="size-3" />
                                <time datetime="{{ $post->published_at->toIso8601String() }}">
                                    {{ $post->published_at->format('Y.m.d') }}
                                </time>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-ui.icon name="clock" class="size-3" />
                                <span>{{ $post->reading_time }}m read</span>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Table of Contents (IDE Sidebar Style) --}}
                <template x-if="visible">
                    <div class="fixed left-12 top-40 hidden xl:block w-72 space-y-6">
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="list-bullet" class="size-4 text-primary" />
                            <p class="text-[10px] font-black tracking-[0.3em] uppercase text-neutral-500">{{ __('Outline.structure') }}</p>
                        </div>
                        <nav class="space-y-1 border-l border-neutral-200 dark:border-neutral-800">
                            <template x-for="item in items" :key="item.id">
                                <a :href="'#' + item.id" 
                                   class="block pl-6 py-2 text-[11px] font-bold tracking-tight transition-all border-l-2 -ml-px uppercase"
                                   :class="activeId === item.id ? 'text-primary border-primary bg-primary/5' : 'text-neutral-500 border-transparent hover:text-neutral-900 dark:hover:text-white hover:border-neutral-300 dark:hover:border-neutral-700'"
                                   x-text="item.text"></a>
                            </template>
                        </nav>
                    </div>
                </template>

                {{-- Content Block --}}
                <div class="space-y-16">
                    {{-- Featured Image --}}
                    @if ($post->featuredImageUrl('hero'))
                        <figure class="border border-neutral-200 dark:border-neutral-800 p-2 bg-white dark:bg-[#141414]">
                            <img
                                src="{{ $post->featuredImageUrl('hero') }}"
                                alt="{{ $post->title }}"
                                class="aspect-[21/9] w-full object-cover grayscale hover:grayscale-0 transition-all duration-700"
                            />
                        </figure>
                    @endif

                    <div class="grid gap-12 lg:grid-cols-[1fr_200px]">
                        {{-- Body --}}
                        <div class="space-y-12">
                            @if ($post->excerpt)
                                <div class="text-xl font-bold text-neutral-500 dark:text-neutral-400 leading-relaxed tracking-tight border-l-4 border-primary/20 pl-8 py-2 italic font-sans">
                                    # {{ $post->excerpt }}
                                </div>
                            @endif

                            <div class="tiptap-content text-lg leading-relaxed text-neutral-900 dark:text-neutral-600 font-sans">
                                {!! ContentRenderer::render($post->content_json ?: $post->content) !!}
                            </div>
                        </div>

                        {{-- Right Rail Meta --}}
                        <aside class="hidden lg:block space-y-10">
                            <div class="space-y-4">
                                <p class="text-[9px] font-black uppercase tracking-[0.3em] text-neutral-400"># metadata</p>
                                <div class="space-y-4 border-t border-neutral-100 dark:border-neutral-800/50 pt-4">
                                    <div class="space-y-1">
                                        <p class="text-[8px] font-bold text-primary uppercase">Views</p>
                                        <p class="text-xs font-bold text-neutral-900 dark:text-white">{{ number_format($post->view_count) }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-[8px] font-bold text-primary uppercase">Format</p>
                                        <p class="text-xs font-bold text-neutral-900 dark:text-white">Markdown</p>
                                    </div>
                                </div>
                            </div>

                            @if($post->tags->isNotEmpty())
                                <div class="space-y-4">
                                    <p class="text-[9px] font-black uppercase tracking-[0.3em] text-neutral-400"># identifiers</p>
                                    <div class="flex flex-wrap gap-2 border-t border-neutral-100 dark:border-neutral-800/50 pt-4">
                                        @foreach($post->tags as $tag)
                                            <span class="text-[10px] font-bold text-primary hover:underline cursor-pointer">
                                                .{{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </aside>
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="grid grid-cols-2 gap-px bg-neutral-200 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-800">
                    <div class="bg-neutral-50 dark:bg-[#0a0a0a] p-8 group">
                        @if ($previousPost)
                            <a href="{{ route('posts.show', $previousPost->slug) }}" wire:navigate class="space-y-4 block">
                                <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary transition-colors flex items-center gap-2">
                                    &lt;-- PREV_LOG
                                </span>
                                <span class="block text-sm font-bold text-neutral-900 dark:text-white group-hover:text-primary transition-colors line-clamp-1 italic">"{{ $previousPost->title }}"</span>
                            </a>
                        @endif
                    </div>
                    <div class="bg-neutral-50 dark:bg-[#0a0a0a] p-8 text-right group">
                        @if ($nextPost)
                            <a href="{{ route('posts.show', $nextPost->slug) }}" wire:navigate class="space-y-4 block">
                                <span class="text-[10px] font-black uppercase tracking-[0.4em] text-primary transition-colors flex items-center justify-end gap-2">
                                    NEXT_LOG --&gt;
                                </span>
                                <span class="block text-sm font-bold text-neutral-900 dark:text-white group-hover:text-primary transition-colors line-clamp-1 italic">"{{ $nextPost->title }}"</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Comments Section --}}
                <section class="pt-20 space-y-12">
                    <div class="flex items-center gap-6">
                        <h2 class="text-xl font-black text-neutral-900 dark:text-white uppercase tracking-tighter">{{ __('Comments.thread') }}</h2>
                        <div class="h-px flex-1 bg-neutral-200 dark:bg-neutral-800 opacity-20"></div>
                    </div>
                    
                    <div class="bg-white dark:bg-[#141414] border border-neutral-200 dark:border-neutral-800 p-8">
                        <livewire:posts.comments :post="$post" defer />
                    </div>
                </section>

            </article>
        </div>
    </div>
</x-layouts::main>
