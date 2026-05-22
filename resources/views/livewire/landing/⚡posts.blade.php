<?php

use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public int $perPage = 5;
    public int $page = 1;
    public bool $hasMore = true;

    #[Computed]
    public function initialPosts(): \Illuminate\Database\Eloquent\Collection
    {
        return Post::published()
            ->with('user', 'tags', 'media')
            ->latest('published_at')
            ->limit($this->perPage)
            ->get();
    }

    public function loadMore(): void
    {
        $this->page++;
    }

    #[Computed]
    public function morePosts(): \Illuminate\Database\Eloquent\Collection
    {
        if ($this->page <= 1) {
            return Post::newModelInstance()->newCollection();
        }

        $posts = Post::published()
            ->with('user', 'tags', 'media')
            ->latest('published_at')
            ->skip($this->perPage * ($this->page - 1))
            ->take($this->perPage)
            ->get();

        if ($posts->count() < $this->perPage) {
            $this->hasMore = false;
        }

        return $posts;
    }
};
?>

<div>
    {{-- Initial 5 posts --}}
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($this->initialPosts as $post)
            <x-posts.card wire:key="post-initial-{{ $post->id }}" :$post />
        @empty
            <div class="col-span-full">
                <x-ui.empty class="rounded-xl border border-dashed border-neutral-300 py-16 dark:border-neutral-700">
                    <x-ui.empty.media>
                        <x-ui.icon name="document-text" class="h-10 w-10 text-neutral-300 dark:text-neutral-600" />
                    </x-ui.empty.media>
                    <x-ui.empty.contents>
                        <x-ui.text class="text-neutral-400">{{ __('No posts yet.') }}</x-ui.text>
                    </x-ui.empty.contents>
                </x-ui.empty>
            </div>
        @endforelse
    </div>

    {{-- Additional posts appended via island --}}
    @island(name: 'more-posts')
        @if ($this->morePosts->isNotEmpty())
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->morePosts as $post)
                    <x-posts.card wire:key="post-more-{{ $post->id }}-{{ $page }}" :$post />
                @endforeach
            </div>
        @endif
    @endisland

    {{-- Sentinel: triggers loadMore when scrolled into view --}}
    @if ($this->initialPosts->isNotEmpty() && $hasMore)
        <div
            wire:intersect.once="loadMore"
            wire:island.append="more-posts"
            class="mt-8 flex justify-center py-4"
        >
            <x-ui.icon.loading class="h-5 w-5 text-neutral-400" />
        </div>
    @endif
</div>
