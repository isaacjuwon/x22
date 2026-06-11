<?php

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Testimonials'), Layout('layouts::app')] class extends Component {

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
        $testimonials = $this->baseQuery()
            ->when(filled($this->sortBy), fn ($q) => $q->orderBy($this->sortBy, $this->sortDir))
            ->when(filled($this->searchQuery), fn ($q) => $this->applySearch($q))
            ->when(filled($this->positions), fn ($q) => $this->applyPositionSorting($q))
            ->with('user', 'project')
            ->paginate($this->perPage);

        $this->visibleIds = $testimonials->pluck('id')->map(fn ($id) => (string) $id)->toArray();

        return $this->view(['testimonials' => $testimonials]);
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

    public function approve(int $id): void
    {
        Testimonial::findOrFail($id)->update(['status' => TestimonialStatus::Approved]);
        $this->dispatch('notify', type: 'success', content: __('Testimonial approved.'));
    }

    public function reject(int $id): void
    {
        Testimonial::findOrFail($id)->update(['status' => TestimonialStatus::Rejected]);
        $this->dispatch('notify', type: 'success', content: __('Testimonial rejected.'));
    }

    public function deleteSelected(): void
    {
        $this->baseQuery()->whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
    }

    public function delete(int $id): void
    {
        Testimonial::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', content: __('Testimonial deleted.'));
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
        return Testimonial::query();
    }

    protected function applySearch(Builder $query): Builder
    {
        return $query->where('comment', 'like', "%{$this->searchQuery}%")
            ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$this->searchQuery}%"))
            ->orWhereHas('project', fn ($q) => $q->where('title', 'like', "%{$this->searchQuery}%"));
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
    <x-ui.table.container x-data="{ hiddenCols: [] }">

        {{-- Toolbar --}}
        <div class="flex items-center">

            <div>
                <x-ui.heading level="h1" size="lg">{{ __('Testimonials') }}</x-ui.heading>
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
                        <x-ui.dropdown.item icon="arrow-down-on-square" wire:click="toCsv">
                            {{ __('Export CSV') }}
                        </x-ui.dropdown.item>
                        <x-ui.dropdown.item
                            icon="trash"
                            variant="danger"
                            wire:click="deleteSelected"
                            wire:confirm="{{ __('Delete selected testimonials?') }}"
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
                    placeholder="{{ __('Search testimonials...') }}"
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
                    <x-ui.dropdown.item x-model="hiddenCols">{{ __('project') }}</x-ui.dropdown.item>
                    <x-ui.dropdown.item x-model="hiddenCols">{{ __('comment') }}</x-ui.dropdown.item>
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
                    @foreach (\App\Enums\TestimonialStatus::cases() as $case)
                        <x-ui.dropdown.item>{{ ucfirst($case->value) }}</x-ui.dropdown.item>
                    @endforeach
                </x-slot:menu>
            </x-ui.dropdown>
        </div>

        {{-- Table --}}
        <x-ui.table
            :paginator="$testimonials"
            pagination:variant="full"
            wire:loading
            reorderable
            loadOn="pagination, search, sorting"
        >
            <x-ui.table.header sticky class="dark:bg-neutral-900 bg-white">
                <x-ui.table.columns withCheckAll>
                    <x-ui.table.head sticky class="dark:bg-neutral-900 bg-white">#ID</x-ui.table.head>
                    <x-ui.table.head>{{ __('Client') }}</x-ui.table.head>
                    <x-ui.table.head
                        x-show="!hiddenCols.includes('project')"
                        x-cloak
                    >
                        {{ __('Project') }}
                    </x-ui.table.head>
                    <x-ui.table.head>{{ __('Rating') }}</x-ui.table.head>
                    <x-ui.table.head
                        x-show="!hiddenCols.includes('comment')"
                        x-cloak
                    >
                        {{ __('Comment') }}
                    </x-ui.table.head>
                    <x-ui.table.head
                        column="status"
                        sortable
                        :currentSortBy="$sortBy"
                        :currentSortDir="$sortDir"
                    >
                        {{ __('Status') }}
                    </x-ui.table.head>
                    <x-ui.table.head
                        column="created_at"
                        sortable
                        variant="dropdown"
                        :currentSortBy="$sortBy"
                        :currentSortDir="$sortDir"
                    >
                        {{ __('Date') }}
                    </x-ui.table.head>
                    <x-ui.table.head></x-ui.table.head>
                </x-ui.table.columns>
            </x-ui.table.header>

            <x-ui.table.rows>
                @forelse ($testimonials as $testimonial)
                    <x-ui.table.row
                        :checkboxId="$testimonial->id"
                        :order="$testimonial->id"
                        :key="$testimonial->id"
                    >
                        <x-ui.table.cell sticky class="[body:not(.sorting)_&]:dark:bg-neutral-950 [body:not(.sorting)_&]:bg-neutral-50">
                            {{ $testimonial->id }}
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="flex items-center gap-2">
                                <img
                                    src="https://api.dicebear.com/9.x/avataaars/svg?seed={{ $testimonial->user->id }}"
                                    alt="{{ $testimonial->user->name }}"
                                    class="size-7 rounded-full"
                                />
                                <span class="font-medium text-neutral-900 dark:text-neutral-100">
                                    {{ $testimonial->user->name }}
                                </span>
                            </div>
                        </x-ui.table.cell>

                        <x-ui.table.cell x-show="!hiddenCols.includes('project')" x-cloak>
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $testimonial->project?->title ?? '—' }}
                            </span>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="flex items-center gap-0.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg
                                        class="size-3 {{ $i <= $testimonial->rating ? 'text-amber-400' : 'text-neutral-200 dark:text-neutral-700' }}"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                        </x-ui.table.cell>

                        <x-ui.table.cell x-show="!hiddenCols.includes('comment')" x-cloak>
                            <span class="line-clamp-2 max-w-xs text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $testimonial->comment }}
                            </span>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            @php
                                $statusColor = match($testimonial->status) {
                                    TestimonialStatus::Approved => 'green',
                                    TestimonialStatus::Rejected => 'red',
                                    TestimonialStatus::Pending  => 'amber',
                                    default                     => null,
                                };
                            @endphp
                            <x-ui.badge :color="$statusColor" size="sm" variant="outline">
                                {{ ucfirst($testimonial->status->value) }}
                            </x-ui.badge>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <span class="font-mono text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $testimonial->created_at->format('M d, Y') }}
                            </span>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="flex items-center gap-1">
                                @if ($testimonial->status !== TestimonialStatus::Approved)
                                    <x-ui.button
                                        wire:click="approve({{ $testimonial->id }})"
                                        variant="ghost"
                                        size="sm"
                                        icon="check-circle"
                                        color="green"
                                    />
                                @endif
                                @if ($testimonial->status !== TestimonialStatus::Rejected)
                                    <x-ui.button
                                        wire:click="reject({{ $testimonial->id }})"
                                        variant="ghost"
                                        size="sm"
                                        icon="x-circle"
                                        color="red"
                                    />
                                @endif
                                <x-ui.button
                                    wire:click="delete({{ $testimonial->id }})"
                                    wire:confirm="{{ __('Delete this testimonial?') }}"
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
                                <x-ui.icon name="chat-bubble-left-ellipsis" class="size-10" />
                            </x-ui.empty.media>
                            <x-ui.empty.contents>
                                <h3 class="text-lg font-semibold">{{ __('No testimonials found') }}</h3>
                                <p class="text-sm text-neutral-500">{{ __('Testimonials from clients will appear here.') }}</p>
                            </x-ui.empty.contents>
                        </x-ui.empty>
                    </x-ui.table.empty>
                @endforelse
            </x-ui.table.rows>
        </x-ui.table>

    </x-ui.table.container>
</div>
