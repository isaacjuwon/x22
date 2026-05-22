<?php

use App\Models\Post;
use App\Models\Tag;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Posts')] class extends Component {
    use WithPagination;

    public int $perPage = 12;

    #[Url(as: 'tag')]
    public ?string $activeTag = null;

    #[Computed]
    public function tags(): \Illuminate\Database\Eloquent\Collection
    {
        return Tag::orderBy('name')->get();
    }

    #[Computed]
    public function posts(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Post::query()
            ->published()
            ->when($this->activeTag, fn ($query) => $query->whereHas(
                'tags',
                fn ($q) => $q->where('slug', $this->activeTag)
            ))
            ->with('user', 'tags', 'media')
            ->latest('published_at')
            ->paginate($this->perPage);
    }

    public function filterByTag(string $slug): void
    {
        $this->activeTag = $slug;
        $this->resetPage();
    }

    public function clearFilter(): void
    {
        $this->activeTag = null;
        $this->resetPage();
    }
};
?>

<div class="w-full">
    <x-ui.heading level="h1" size="xl" class="mb-4">{{ __('Latest Posts') }}</x-ui.heading>

    {{-- Tag filters --}}
    @if ($this->tags->isNotEmpty())
        <div class="mb-8 flex flex-wrap gap-2">
            <x-ui.button
                wire:click="clearFilter"
                size="sm"
                :variant="$activeTag === null ? 'primary' : 'ghost'"
            >
                {{ __('All') }}
            </x-ui.button>

            @foreach ($this->tags as $tag)
                <x-ui.button
                    wire:click="filterByTag('{{ $tag->slug }}')"
                    wire:key="tag-{{ $tag->id }}"
                    size="sm"
                    :variant="$activeTag === $tag->slug ? 'primary' : 'ghost'"
                >
                    {{ $tag->name }}
                </x-ui.button>
            @endforeach
        </div>
    @endif

    {{-- Posts grid --}}
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($this->posts as $post)
            <x-posts.card wire:key="post-{{ $post->id }}" :$post />
        @empty
            <div class="col-span-full py-12 text-center">
                <x-ui.empty>
                    <x-ui.empty.media>
                        <x-ui.icon name="document-text" class="h-10 w-10 text-neutral-300 dark:text-neutral-600" />
                    </x-ui.empty.media>
                    <x-ui.empty.contents>
                        <x-ui.text class="text-neutral-400">{{ __('No posts found.') }}</x-ui.text>
                    </x-ui.empty.contents>
                </x-ui.empty>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $this->posts->links() }}
    </div>
</div>
