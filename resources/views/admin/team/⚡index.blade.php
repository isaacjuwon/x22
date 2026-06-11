<?php

use App\Enums\TeamMemberRole;
use App\Enums\TeamMemberStatus;
use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Session;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Team'), Layout('layouts::app')] class extends Component {

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
        $members = $this->baseQuery()
            ->when(filled($this->sortBy), fn ($q) => $q->orderBy($this->sortBy, $this->sortDir))
            ->when(filled($this->searchQuery), fn ($q) => $this->applySearch($q))
            ->when(filled($this->positions), fn ($q) => $this->applyPositionSorting($q))
            ->with('user', 'project')
            ->paginate($this->perPage);

        $this->visibleIds = $members->pluck('id')->map(fn ($id) => (string) $id)->toArray();

        return $this->view(['members' => $members]);
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
        TeamMember::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', content: __('Team member removed.'));
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
        return TeamMember::query();
    }

    protected function applySearch(Builder $query): Builder
    {
        return $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$this->searchQuery}%"))
            ->orWhereHas('project', fn ($q) => $q->where('title', 'like', "%{$this->searchQuery}%"))
            ->orWhere('role', 'like', "%{$this->searchQuery}%");
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
                <x-ui.heading level="h1" size="lg">{{ __('Team Members') }}</x-ui.heading>
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
                            wire:confirm="{{ __('Remove selected team members?') }}"
                        >
                            {{ __('Remove selected') }}
                        </x-ui.dropdown.item>
                    </x-slot:menu>
                </x-ui.dropdown>
            </div>

            {{-- Search --}}
            <div class="ml-auto">
                <x-ui.input
                    class="[&_input]:bg-transparent"
                    placeholder="{{ __('Search team...') }}"
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
                    <x-ui.dropdown.item x-model="hiddenCols">{{ __('status') }}</x-ui.dropdown.item>
                </x-slot:menu>
            </x-ui.dropdown>

            {{-- Filter --}}
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
                    <x-ui.dropdown.item readOnly>{{ __('Role') }}</x-ui.dropdown.item>
                    <x-ui.dropdown.separator />
                    @foreach (TeamMemberRole::cases() as $case)
                        <x-ui.dropdown.item>{{ ucfirst($case->value) }}</x-ui.dropdown.item>
                    @endforeach
                </x-slot:menu>
            </x-ui.dropdown>

            {{-- Add member --}}
            <x-ui.button
                as="a"
                href="{{ route('admin.team.create') }}"
                icon="plus"
                variant="primary"
                size="sm"
                class="ml-2"
            >
                {{ __('Add Member') }}
            </x-ui.button>
        </div>

        {{-- Table --}}
        <x-ui.table
            :paginator="$members"
            pagination:variant="full"
            wire:loading
            reorderable
            loadOn="pagination, search, sorting"
        >
            <x-ui.table.header sticky class="dark:bg-neutral-900 bg-white">
                <x-ui.table.columns withCheckAll>
                    <x-ui.table.head sticky class="dark:bg-neutral-900 bg-white">#ID</x-ui.table.head>
                    <x-ui.table.head>{{ __('Member') }}</x-ui.table.head>
                    <x-ui.table.head
                        column="role"
                        sortable
                        :currentSortBy="$sortBy"
                        :currentSortDir="$sortDir"
                    >
                        {{ __('Role') }}
                    </x-ui.table.head>
                    <x-ui.table.head
                        x-show="!hiddenCols.includes('project')"
                        x-cloak
                    >
                        {{ __('Project') }}
                    </x-ui.table.head>
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
                        column="created_at"
                        sortable
                        variant="dropdown"
                        :currentSortBy="$sortBy"
                        :currentSortDir="$sortDir"
                    >
                        {{ __('Joined') }}
                    </x-ui.table.head>
                    <x-ui.table.head></x-ui.table.head>
                </x-ui.table.columns>
            </x-ui.table.header>

            <x-ui.table.rows>
                @forelse ($members as $member)
                    <x-ui.table.row
                        :checkboxId="$member->id"
                        :order="$member->id"
                        :key="$member->id"
                    >
                        <x-ui.table.cell sticky class="[body:not(.sorting)_&]:dark:bg-neutral-950 [body:not(.sorting)_&]:bg-neutral-50">
                            {{ $member->id }}
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="flex items-center gap-2">
                                <img
                                    src="https://api.dicebear.com/9.x/avataaars/svg?seed={{ $member->user->id }}"
                                    alt="{{ $member->user->name }}"
                                    class="size-7 rounded-full"
                                />
                                <div>
                                    <div class="font-medium text-neutral-900 dark:text-neutral-100">
                                        {{ $member->user->name }}
                                    </div>
                                    <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                        {{ $member->user->email }}
                                    </div>
                                </div>
                            </div>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            @php
                                $roleColor = match($member->role) {
                                    TeamMemberRole::Lead      => 'purple',
                                    TeamMemberRole::Developer => 'blue',
                                    TeamMemberRole::Designer  => 'pink',
                                    TeamMemberRole::Manager   => 'amber',
                                    default                   => null,
                                };
                            @endphp
                            <x-ui.badge :color="$roleColor" size="sm" variant="outline">
                                {{ ucfirst($member->role->value) }}
                            </x-ui.badge>
                        </x-ui.table.cell>

                        <x-ui.table.cell x-show="!hiddenCols.includes('project')" x-cloak>
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $member->project?->title ?? '—' }}
                            </span>
                        </x-ui.table.cell>

                        <x-ui.table.cell x-show="!hiddenCols.includes('status')" x-cloak>
                            @php
                                $statusColor = match($member->status) {
                                    TeamMemberStatus::Active   => 'green',
                                    TeamMemberStatus::Inactive => 'red',
                                    TeamMemberStatus::Pending  => 'amber',
                                    default                    => null,
                                };
                            @endphp
                            <x-ui.badge :color="$statusColor" size="sm" variant="outline">
                                {{ ucfirst($member->status->value) }}
                            </x-ui.badge>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <span class="font-mono text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $member->created_at->format('M d, Y') }}
                            </span>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="flex items-center gap-1">
                                <x-ui.button
                                    as="a"
                                    href="{{ route('admin.team.edit', $member) }}"
                                    variant="ghost"
                                    size="sm"
                                    icon="pencil"
                                />
                                <x-ui.button
                                    wire:click="delete({{ $member->id }})"
                                    wire:confirm="{{ __('Remove this team member?') }}"
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
                                <x-ui.icon name="users" class="size-10" />
                            </x-ui.empty.media>
                            <x-ui.empty.contents>
                                <h3 class="text-lg font-semibold">{{ __('No team members found') }}</h3>
                                <p class="text-sm text-neutral-500">{{ __('Add team members to your projects.') }}</p>
                            </x-ui.empty.contents>
                        </x-ui.empty>
                    </x-ui.table.empty>
                @endforelse
            </x-ui.table.rows>
        </x-ui.table>

    </x-ui.table.container>
</div>
