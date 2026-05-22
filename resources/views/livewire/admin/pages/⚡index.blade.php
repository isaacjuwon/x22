<?php

use App\Enums\PageStatus;
use App\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pages'), Layout('layouts::app')] class extends Component {

    public string $searchQuery = '';
    public string $sortBy = '';
    public string $sortDir = 'asc';
    public int $perPage = 15;
    public array $selectedIds = [];

    public function render(): \Illuminate\View\View
    {
        $pages = $this->baseQuery()
            ->when(filled($this->sortBy), fn ($q) => $q->orderBy($this->sortBy, $this->sortDir))
            ->when(filled($this->searchQuery), fn ($q) => $this->applySearch($q))
            ->with('user', 'media')
            ->paginate($this->perPage);

        return $this->view(['pages' => $pages]);
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

    public function deleteSelected(): void
    {
        $this->baseQuery()->whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
    }

    public function delete(int $id): void
    {
        Page::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', content: __('Page deleted.'));
    }

    protected function baseQuery(): Builder
    {
        return Page::query();
    }

    protected function applySearch(Builder $query): Builder
    {
        return $query->where('title', 'like', "%{$this->searchQuery}%")
            ->orWhere('slug', 'like', "%{$this->searchQuery}%");
    }
};
?>

<div class="px-4 py-6">
    <x-ui.table.container>

        {{-- Toolbar --}}
        <div class="flex items-center">

            <div>
                <x-ui.heading level="h1" size="lg">{{ __('Pages') }}</x-ui.heading>
            </div>

            {{-- Bulk actions --}}
            <div style="display:none;" wire:show="selectedIds.length" class="ml-4">
                <x-ui.dropdown position="bottom-start">
                    <x-slot:button>
                        <x-ui.button
                            icon="ellipsis-vertical"
                            variant="soft"
                            size="sm"
                            class="rounded-box outline dark:outline-white/20 outline-neutral-900/10 shadow-sm"
                        >
                            {{ __('Bulk action') }}
                        </x-ui.button>
                    </x-slot:button>
                    <x-slot:menu>
                        <x-ui.dropdown.item
                            icon="trash"
                            variant="danger"
                            wire:click="deleteSelected"
                            wire:confirm="{{ __('Delete selected pages?') }}"
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
                    placeholder="{{ __('Search pages...') }}"
                    leftIcon="magnifying-glass"
                    wire:model.live="searchQuery"
                />
            </div>

            {{-- New page --}}
            <x-ui.button
                as="a"
                href="{{ route('admin.pages.create') }}"
                icon="plus"
                variant="primary"
                size="sm"
                class="ml-2"
            >
                {{ __('New Page') }}
            </x-ui.button>
        </div>

        {{-- Table --}}
        <x-ui.table
            :paginator="$pages"
            pagination:variant="full"
            wire:loading
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
                    <x-ui.table.head
                        column="slug"
                        sortable
                        :currentSortBy="$sortBy"
                        :currentSortDir="$sortDir"
                    >
                        {{ __('Slug') }}
                    </x-ui.table.head>
                    <x-ui.table.head>{{ __('Author') }}</x-ui.table.head>
                    <x-ui.table.head
                        column="status"
                        sortable
                        :currentSortBy="$sortBy"
                        :currentSortDir="$sortDir"
                    >
                        {{ __('Status') }}
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
                    <x-ui.table.head></x-ui.table.head>
                </x-ui.table.columns>
            </x-ui.table.header>

            <x-ui.table.rows>
                @forelse ($pages as $page)
                    <x-ui.table.row
                        :checkboxId="$page->id"
                        :key="$page->id"
                    >
                        <x-ui.table.cell sticky class="[body:not(.sorting)_&]:dark:bg-neutral-950 [body:not(.sorting)_&]:bg-neutral-50">
                            {{ $page->id }}
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="flex max-w-xs items-center gap-3">
                                @if ($page->featuredImageUrl('thumb'))
                                    <img
                                        src="{{ $page->featuredImageUrl('thumb') }}"
                                        alt="{{ $page->title }}"
                                        class="size-11 rounded-lg object-cover"
                                    />
                                @else
                                    <div class="flex size-11 items-center justify-center rounded-lg border border-dashed border-neutral-300 text-neutral-400 dark:border-neutral-700 dark:text-neutral-600">
                                        <x-ui.icon name="photo" class="size-5" />
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <div class="font-medium text-neutral-900 dark:text-neutral-100">
                                        {{ $page->title }}
                                    </div>
                                    @if ($page->excerpt)
                                        <div class="mt-1 line-clamp-1 text-xs text-neutral-500 dark:text-neutral-400">
                                            {{ $page->excerpt }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <span class="font-mono text-sm text-neutral-500 dark:text-neutral-400">
                                /{{ $page->slug }}
                            </span>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="flex items-center gap-2">
                                <x-ui.avatar :name="$page->user->name" size="xs" color="auto" />
                                <span class="text-sm text-neutral-700 dark:text-neutral-300">
                                    {{ $page->user->name }}
                                </span>
                            </div>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            @php
                                $statusColor = match($page->status) {
                                    PageStatus::Published => 'green',
                                    PageStatus::Draft     => 'zinc',
                                    PageStatus::Archived  => 'red',
                                    default               => null,
                                };
                            @endphp
                            <x-ui.badge :color="$statusColor" size="sm" variant="outline">
                                {{ ucfirst($page->status->value) }}
                            </x-ui.badge>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <span class="font-mono text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $page->published_at?->format('M d, Y') ?? '—' }}
                            </span>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="flex items-center gap-1">
                                <x-ui.button
                                    as="a"
                                    href="{{ route('admin.pages.edit', $page) }}"
                                    variant="ghost"
                                    size="sm"
                                    icon="pencil"
                                />
                                <x-ui.button
                                    wire:click="delete({{ $page->id }})"
                                    wire:confirm="{{ __('Delete this page?') }}"
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
                                <x-ui.icon name="document" class="size-10" />
                            </x-ui.empty.media>
                            <x-ui.empty.contents>
                                <h3 class="text-lg font-semibold">{{ __('No pages found') }}</h3>
                                <p class="text-sm text-neutral-500">{{ __('Create your first page to get started.') }}</p>
                            </x-ui.empty.contents>
                        </x-ui.empty>
                    </x-ui.table.empty>
                @endforelse
            </x-ui.table.rows>
        </x-ui.table>

    </x-ui.table.container>
</div>
