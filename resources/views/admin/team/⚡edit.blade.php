<?php

use App\Enums\TeamMemberRole;
use App\Enums\TeamMemberStatus;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Edit Team Member'), Layout('layouts::app')] class extends Component {

    public TeamMember $teamMember;

    #[Validate('required|exists:users,id')]
    public string $userId = '';

    #[Validate('required|exists:projects,id')]
    public string $projectId = '';

    #[Validate('required|in:lead,developer,designer,manager')]
    public string $role = '';

    #[Validate('required|in:active,inactive,pending')]
    public string $status = '';

    #[Computed]
    public function users(): \Illuminate\Database\Eloquent\Collection
    {
        return User::orderBy('name')->get();
    }

    #[Computed]
    public function projects(): \Illuminate\Database\Eloquent\Collection
    {
        return Project::orderBy('title')->get();
    }

    public function mount(TeamMember $teamMember): void
    {
        $this->teamMember = $teamMember;
        $this->userId = (string) $teamMember->user_id;
        $this->projectId = (string) $teamMember->project_id;
        $this->role = $teamMember->role->value;
        $this->status = $teamMember->status->value;
    }

    public function save(): void
    {
        $this->validate();

        $this->teamMember->update([
            'user_id'    => $this->userId,
            'project_id' => $this->projectId,
            'role'       => $this->role,
            'status'     => $this->status,
        ]);

        $this->dispatch('notify', type: 'success', content: __('Team member updated.'));
        $this->redirect(route('admin.team.index'), navigate: true);
    }
};
?>

<div class="mx-auto max-w-3xl space-y-6 p-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <x-ui.button
            as="a"
            href="{{ route('admin.team.index') }}"
            variant="ghost"
            size="sm"
            icon="arrow-left"
        />
        <x-ui.heading level="h1" size="lg">{{ __('Edit Team Member') }}</x-ui.heading>
    </div>

    <x-ui.separator />

    <form wire:submit="save" class="space-y-5">

        {{-- User --}}
        <x-ui.field required>
            <x-ui.label required>{{ __('User') }}</x-ui.label>
            <x-ui.select wire:model="userId" placeholder="{{ __('Select a user...') }}">
                @foreach ($this->users as $user)
                    <x-ui.select.option :value="$user->id">{{ $user->name }} — {{ $user->email }}</x-ui.select.option>
                @endforeach
            </x-ui.select>
            <x-ui.error name="userId" />
        </x-ui.field>

        {{-- Project --}}
        <x-ui.field required>
            <x-ui.label required>{{ __('Project') }}</x-ui.label>
            <x-ui.select wire:model="projectId" placeholder="{{ __('Select a project...') }}">
                @foreach ($this->projects as $project)
                    <x-ui.select.option :value="$project->id">{{ $project->title }}</x-ui.select.option>
                @endforeach
            </x-ui.select>
            <x-ui.error name="projectId" />
        </x-ui.field>

        <x-ui.separator label="{{ __('Details') }}" />

        <div class="grid gap-5 sm:grid-cols-2">

            {{-- Role --}}
            <x-ui.field required>
                <x-ui.label required>{{ __('Role') }}</x-ui.label>
                <x-ui.select wire:model="role" placeholder="{{ __('Select a role...') }}">
                    @foreach (TeamMemberRole::cases() as $case)
                        <x-ui.select.option :value="$case->value">{{ ucfirst($case->value) }}</x-ui.select.option>
                    @endforeach
                </x-ui.select>
                <x-ui.error name="role" />
            </x-ui.field>

            {{-- Status --}}
            <x-ui.field required>
                <x-ui.label required>{{ __('Status') }}</x-ui.label>
                <x-ui.select wire:model="status" placeholder="{{ __('Select a status...') }}">
                    @foreach (TeamMemberStatus::cases() as $case)
                        <x-ui.select.option :value="$case->value">{{ ucfirst($case->value) }}</x-ui.select.option>
                    @endforeach
                </x-ui.select>
                <x-ui.error name="status" />
            </x-ui.field>

        </div>

        <x-ui.separator />

        <div class="flex justify-end gap-3">
            <x-ui.button as="a" href="{{ route('admin.team.index') }}" variant="ghost">
                {{ __('Cancel') }}
            </x-ui.button>
            <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                {{ __('Save Changes') }}
            </x-ui.button>
        </div>

    </form>
</div>
