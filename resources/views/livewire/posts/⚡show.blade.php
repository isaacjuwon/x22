<?php

use App\Enums\PostStatus;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new class extends Component {
    public Post $post;

    public function mount(Post $post): void
    {
        abort_unless(
            $post->status === PostStatus::Published
                && $post->published_at !== null
                && $post->published_at->isPast(),
            404
        );

        $post->incrementViewCount();

        $this->post = $post;
    }

    #[Computed]
    public function previousPost(): ?Post
    {
        return Post::published()
            ->where('published_at', '<', $this->post->published_at)
            ->latest('published_at')
            ->first();
    }

    #[Computed]
    public function nextPost(): ?Post
    {
        return Post::published()
            ->where('published_at', '>', $this->post->published_at)
            ->oldest('published_at')
            ->first();
    }

    #[Computed]
    public function relatedPosts(): \Illuminate\Database\Eloquent\Collection
    {
        $tagIds = $this->post->tags->pluck('id');

        $related = Post::published()
            ->whereNot('id', $this->post->id)
            ->when($tagIds->isNotEmpty(), fn ($q) => $q->whereHas(
                'tags',
                fn ($q) => $q->whereIn('tags.id', $tagIds)
            ))
            ->latest('published_at')
            ->limit(2)
            ->get();

        if ($related->isEmpty()) {
            $related = Post::published()
                ->whereNot('id', $this->post->id)
                ->latest('published_at')
                ->limit(2)
                ->get();
        }

        return $related;
    }

};
?>

<article class="mx-auto max-w-2xl py-12">
    {{-- Header --}}
    <header class="mb-8 space-y-4">
        <div class="mb-4">
            <flux:link href="{{ route('posts.index') }}">← {{ __('Back to Posts') }}</flux:link>
        </div>

        @if ($post->featured)
            <flux:badge color="amber">{{ __('Featured') }}</flux:badge>
        @endif

        <h1 class="text-4xl font-bold">{{ $post->title }}</h1>

        {{-- Meta --}}
        <div class="flex items-center justify-between border-b border-gray-200 pb-4 text-gray-600">
            <div class="flex items-center gap-4">
                <img
                    src="https://api.dicebear.com/9.x/avataaars/svg?seed={{ $post->user->id }}"
                    alt="{{ $post->user->name }}"
                    class="h-10 w-10 rounded-full"
                />
                <div>
                    <flux:text class="font-medium text-gray-900">{{ $post->user->name }}</flux:text>
                    <flux:text class="text-sm">{{ $post->published_at->format('M d, Y') }}</flux:text>
                </div>
            </div>
            <flux:text class="text-sm">{{ $post->view_count }} {{ __('views') }}</flux:text>
        </div>
    </header>

    {{-- Featured image --}}
    @if ($post->og_image)
        <figure class="mb-8 overflow-hidden rounded-lg">
            <img
                src="{{ $post->og_image }}"
                alt="{{ $post->title }}"
                class="h-96 w-full object-cover"
            />
        </figure>
    @endif

    {{-- Excerpt --}}
    @if ($post->excerpt)
        <p class="mb-8 border-l-4 border-blue-500 pl-6 text-xl italic text-gray-600">
            {{ $post->excerpt }}
        </p>
    @endif

    {{-- Content --}}
    <div class="prose prose-lg mb-12 max-w-none">
        {!! nl2br(e($post->content)) !!}
    </div>

    {{-- Tags --}}
    @if ($post->tags->isNotEmpty())
        <div class="mb-8 space-y-3 border-t border-gray-200 pt-6">
            <flux:text class="font-semibold">{{ __('Tags') }}</flux:text>
            <div class="flex flex-wrap gap-2">
                @foreach ($post->tags as $tag)
                    <flux:badge wire:key="tag-{{ $tag->id }}" color="gray">
                        {{ $tag->name }}
                    </flux:badge>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Prev / Next navigation --}}
    <nav class="border-t border-gray-200 pt-8">
        <div class="grid grid-cols-2 gap-4">
            <div>
                @if ($this->previousPost)
                    <flux:link href="{{ route('posts.show', $this->previousPost) }}" class="flex flex-col gap-2">
                        <flux:text class="text-sm text-gray-500">← {{ __('Previous') }}</flux:text>
                        <flux:text class="font-semibold hover:text-blue-600">
                            {{ Str::limit($this->previousPost->title, 40) }}
                        </flux:text>
                    </flux:link>
                @else
                    <div class="opacity-50">
                        <flux:text class="text-sm text-gray-500">← {{ __('Previous') }}</flux:text>
                        <flux:text class="text-gray-300">{{ __('No previous post') }}</flux:text>
                    </div>
                @endif
            </div>

            <div class="text-right">
                @if ($this->nextPost)
                    <flux:link href="{{ route('posts.show', $this->nextPost) }}" class="flex flex-col items-end gap-2">
                        <flux:text class="text-sm text-gray-500">{{ __('Next') }} →</flux:text>
                        <flux:text class="font-semibold hover:text-blue-600">
                            {{ Str::limit($this->nextPost->title, 40) }}
                        </flux:text>
                    </flux:link>
                @else
                    <div class="opacity-50">
                        <flux:text class="text-sm text-gray-500">{{ __('Next') }} →</flux:text>
                        <flux:text class="text-gray-300">{{ __('No next post') }}</flux:text>
                    </div>
                @endif
            </div>
        </div>
    </nav>

    {{-- Comments --}}
    <section class="mt-12 border-t border-gray-200 pt-8">
        <flux:heading level="2" class="mb-6">{{ __('Comments') }}</flux:heading>
        <livewire:posts.comments :post="$post" />
    </section>

    {{-- Related posts --}}
    @if ($this->relatedPosts->isNotEmpty())
        <section class="mt-16 border-t border-gray-200 pt-12">
            <flux:heading level="2" class="mb-6">{{ __('Related Posts') }}</flux:heading>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                @foreach ($this->relatedPosts as $relatedPost)
                    <x-posts::card wire:key="related-{{ $relatedPost->id }}" :post="$relatedPost" />
                @endforeach
            </div>
        </section>
    @endif
</article>
