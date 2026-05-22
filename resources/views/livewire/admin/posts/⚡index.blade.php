<?php

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Posts'), Layout('layouts::app')] class extends Component {

    public string $searchQuery = '';
    public string $sortBy = '';
    public string $sortDir = 'asc';
    public int $perPage = 15;
    public array $selectedIds = [];
    public array $visibleIds = [];

    #[Session]
    public array $positions = [];

    public function mount(): void
    {
        if (empty($this->positions)) {
            $this->positions = $this->baseQuery()->pluck('id')->toArray();
        }
    }

    public function render(): \Illuminate\View\View
    {
        $posts = $this->baseQuery()
            ->when(filled($this->sortBy), fn ($q) => $q->orderBy($this->sortBy, $this->sortDir))
            ->when(filled($this->searchQuery), fn ($q) => $this->applySearch($q))
            ->when(filled($this->positions), fn ($q) => $this->applyPositionSorting($q))
            ->with('user', 'tags', 'media')
            ->paginate($this->perPage);

        $this->visibleIds = $posts->pluck('id')->map(fn ($id) => (string) $id)->toArray();

        return $this->view(['posts' => $posts]);
    }

    public function sortByColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function handleReordering(mixed $item, int $position): void
    {
        $itemId = (int) $item;
        $positions = array_values(array_filter($this->positions, fn ($id) => $id !== $itemId));
        array_splice($positions, $position, 0, [$itemId]);
        $this->positions = $positions;
    }

    public function deleteSelected(): void
    {
        $this->baseQuery()->whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
    }

    public function delete(int $id): void
    {
        Post::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', content: __('Post deleted.'));
    }

    public function deselectAll(): void
    {
        $this->selectedIds = [];
    }

    #[Renderless]
    public function toCsv(): void
    {
        // CSV export placeholder
    }

    protected function baseQuery(): Builder
    {
        return Post::query();
    }

    protected function applySearch(Builder $query): Builder
    {
        return $query->where('title', 'like', "%{$this->searchQuery}%")
            ->orWhere('excerpt', 'like', "%{$this->searchQuery}%");
    }

    protected function applyPositionSorting(Builder $query): Builder
    {
        if (empty($this->positions)) {
            return $query;
        }

        $case = 'CASE';
        foreach ($this->positions as $index => $id) {
            $case .= " WHEN id = {$id} THEN {$index}";
        }
        $case .= ' END';

        return $query->orderByRaw($case);
    }
};
?>

<div class="px-4 py-6">
    <x-ui.table.container x-data="{ hiddenCols: ['status', 'tags'] }">

        {{-- Toolbar --}}
        <div class="flex items-center">

            <div>
                <x-ui.heading level="h1" size="lg">{{ __('Posts') }}</x-ui.heading>
            </div>

            {{-- Bulk actions --}}
            <div style="display:none;" wire:show="selectedIds.length" class="ml-4">
                <x-ui.dropdown position="bottom-start">
                    <x-slot:button>
                        <x-ui.button
                            icon="ellipsis-vertical"
                            variant="soft"
                            size="sm"
                            class="[@media(width<40rem)]:hidden rounded-box outline dark:outline-white/20 outline-neutral-900/10 shadow-sm"
                        >
                            {{ __('Bulk action') }}
                        </x-ui.button>
                        <x-ui.button
                            icon="ellipsis-vertical"
                            variant="soft"
                            size="sm"
                            class="sm:hidden rounded-box outline dark:outline-white/20 outline-neutral-900/10 shadow-sm"
                        />
                    </x-slot:button>
                    <x-slot:menu>
                        <x-ui.dropdown.item icon="arrow-down-on-square" wire:click="toCsv">
                            {{ __('Export CSV') }}
                        </x-ui.dropdown.item>
                        <x-ui.dropdown.item
                            icon="trash"
                            variant="danger"
                            wire:click="deleteSelected"
                            wire:confirm="{{ __('Delete selected posts?') }}"
                        >
                            {{ __('Delete selected') }}
                        </x-ui.dropdown.item>
                    </x-slot:menu>
                </x-ui.dropdown>
            </div>

            {{-- Search --}}
            <div class="ml-auto">
                <x-ui.input
                    class="[&_input]:bg-transparent"
                    placeholder="{{ __('Search posts...') }}"
                    leftIcon="magnifying-glass"
                    wire:model.live="searchQuery"
                />
            </div>

            {{-- Column visibility --}}
            <x-ui.dropdown checkbox checkboxVariant position="bottom-end">
                <x-slot:button>
                    <x-ui.button
                        icon="view-columns"
                        variant="soft"
                        size="sm"
                        class="rounded-box ml-2 outline dark:outline-white/20 outline-neutral-900/10 shadow-sm"
                    />
                </x-slot:button>
                <x-slot:menu>
                    <x-ui.dropdown.item readOnly>{{ __('Hidden columns') }}</x-ui.dropdown.item>
                    <x-ui.dropdown.separator />
                    <x-ui.dropdown.item x-model="hiddenCols">{{ __('status') }}</x-ui.dropdown.item>
                    <x-ui.dropdown.item x-model="hiddenCols">{{ __('tags') }}</x-ui.dropdown.item>
                </x-slot:menu>
            </x-ui.dropdown>

            {{-- Filter by status --}}
            <x-ui.dropdown checkbox checkboxVariant position="bottom-end">
                <x-slot:button>
                    <x-ui.button
                        icon="funnel"
                        variant="soft"
                        size="sm"
                        class="rounded-box ml-2 outline dark:outline-white/20 outline-neutral-900/10 shadow-sm"
                    />
                </x-slot:button>
                <x-slot:menu>
                    <x-ui.dropdown.item readOnly>{{ __('Status') }}</x-ui.dropdown.item>
                    <x-ui.dropdown.separator />
                    @foreach (PostStatus::cases() as $case)
                        <x-ui.dropdown.item>{{ ucfirst($case->value) }}</x-ui.dropdown.item>
                    @endforeach
                </x-slot:menu>
            </x-ui.dropdown>

            {{-- New post --}}
            <x-ui.button
                as="a"
                href="{{ route('admin.posts.create') }}"
                icon="plus"
                variant="primary"
                size="sm"
                class="ml-2"
            >
                {{ __('New Post') }}
            </x-ui.button>
        </div>

        {{-- Table --}}
        <x-ui.table
            :paginator="$posts"
            pagination:variant="full"
            wire:loading
            reorderable
            loadOn="pagination, search, sorting"
        >
            <x-ui.table.header sticky class="dark:bg-neutral-900 bg-white">
                <x-ui.table.columns withCheckAll>
                    <x-ui.table.head sticky class="dark:bg-neutral-900 bg-white">#ID</x-ui.table.head>
                    <x-ui.table.head
                        column="title"
                        sortable
                        :currentSortBy="$sortBy"
                        :currentSortDir="$sortDir"
                    >
                        {{ __('Title') }}
                    </x-ui.table.head>
                    <x-ui.table.head>{{ __('Author') }}</x-ui.table.head>
                    <x-ui.table.head
                        column="status"
                        sortable
                        :currentSortBy="$sortBy"
                        :currentSortDir="$sortDir"
                        x-show="!hiddenCols.includes('status')"
                        x-cloak
                    >
                        {{ __('Status') }}
                    </x-ui.table.head>
                    <x-ui.table.head
                        x-show="!hiddenCols.includes('tags')"
                        x-cloak
                    >
                        {{ __('Tags') }}
                    </x-ui.table.head>
                    <x-ui.table.head
                        column="published_at"
                        sortable
                        variant="dropdown"
                        :currentSortBy="$sortBy"
                        :currentSortDir="$sortDir"
                    >
                        {{ __('Published') }}
                    </x-ui.table.head>
                    <x-ui.table.head>{{ __('Views') }}</x-ui.table.head>
                    <x-ui.table.head></x-ui.table.head>
                </x-ui.table.columns>
            </x-ui.table.header>

            <x-ui.table.rows>
                @forelse ($posts as $post)
                    <x-ui.table.row
                        :checkboxId="$post->id"
                        :order="$post->id"
                        :key="$post->id"
                    >
                        <x-ui.table.cell sticky class="[body:not(.sorting)_&]:dark:bg-neutral-950 [body:not(.sorting)_&]:bg-neutral-50">
                            {{ $post->id }}
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="flex max-w-xs items-center gap-3">
                                @if ($post->featuredImageUrl('thumb'))
                                    <img
                                        src="{{ $post->featuredImageUrl('thumb') }}"
                                        alt="{{ $post->title }}"
                                        class="size-11 rounded-lg object-cover"
                                    />
                                @else
                                    <div class="flex size-11 items-center justify-center rounded-lg border border-dashed border-neutral-300 text-neutral-400 dark:border-neutral-700 dark:text-neutral-600">
                                        <x-ui.icon name="photo" class="size-5" />
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <div class="font-medium text-neutral-900 dark:text-neutral-100">
                                        {{ $post->title }}
                                    </div>
                                    @if ($post->excerpt)
                                        <div class="mt-1 line-clamp-1 text-xs text-neutral-500 dark:text-neutral-400">
                                            {{ $post->excerpt }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="flex items-center gap-2">
                                <x-ui.avatar :name="$post->user->name" size="xs" color="auto" />
                                <span class="text-sm text-neutral-700 dark:text-neutral-300">
                                    {{ $post->user->name }}
                                </span>
                            </div>
                        </x-ui.table.cell>

                        <x-ui.table.cell x-show="!hiddenCols.includes('status')" x-cloak>
                            @php
                                $statusColor = match($post->status) {
                                    PostStatus::Published => 'green',
                                    PostStatus::Draft     => 'zinc',
                                    PostStatus::Archived  => 'red',
                                    default               => null,
                                };
                            @endphp
                            <x-ui.badge :color="$statusColor" size="sm" variant="outline">
                                {{ ucfirst($post->status->value) }}
                            </x-ui.badge>
                        </x-ui.table.cell>

                        <x-ui.table.cell x-show="!hiddenCols.includes('tags')" x-cloak>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($post->tags->take(3) as $tag)
                                    <x-ui.badge size="sm" color="zinc" variant="outline">
                                        {{ $tag->name }}
                                    </x-ui.badge>
                                @endforeach
                            </div>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <span class="font-mono text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $post->published_at?->format('M d, Y') ?? '—' }}
                            </span>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">
                                {{ number_format($post->view_count) }}
                            </span>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="flex items-center gap-1">
                                <x-ui.button
                                    as="a"
                                    href="{{ route('admin.posts.edit', $post) }}"
                                    variant="ghost"
                                    size="sm"
                                    icon="pencil"
                                />
                                <x-ui.button
                                    wire:click="delete({{ $post->id }})"
                                    wire:confirm="{{ __('Delete this post?') }}"
                                    variant="ghost"
                                    size="sm"
                                    icon="trash"
                                    color="red"
                                />
                            </div>
                        </x-ui.table.cell>
                    </x-ui.table.row>
                @empty
                    <x-ui.table.empty>
                        <x-ui.empty>
                            <x-ui.empty.media>
                                <x-ui.icon name="document-text" class="size-10" />
                            </x-ui.empty.media>
                            <x-ui.empty.contents>
                                <h3 class="text-lg font-semibold">{{ __('No posts found') }}</h3>
                                <p class="text-sm text-neutral-500">{{ __('Create your first post to get started.') }}</p>
                            </x-ui.empty.contents>
                        </x-ui.empty>
                    </x-ui.table.empty>
                @endforelse
            </x-ui.table.rows>
        </x-ui.table>

    </x-ui.table.container>
</div>
