<?php

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Edit Project'), Layout('layouts::app')] class extends Component {

    public Project $project;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:500')]
    public string $description = '';

    #[Validate('required|in:draft,in_progress,completed,archived')]
    public string $status = '';

    #[Validate('nullable|string|max:100')]
    public string $category = '';

    #[Validate('required|exists:users,id')]
    public string $userId = '';

    #[Computed]
    public function users(): \Illuminate\Database\Eloquent\Collection
    {
        return User::orderBy('name')->get();
    }

    public function mount(Project $project): void
    {
        $this->project = $project;
        $this->title = $project->title;
        $this->description = $project->description ?? '';
        $this->status = $project->status->value;
        $this->category = $project->category ?? '';
        $this->userId = (string) $project->user_id;
    }

    public function save(): void
    {
        $this->validate();

        $this->project->update([
            'user_id'     => $this->userId,
            'title'       => $this->title,
            'description' => $this->description ?: null,
            'status'      => $this->status,
            'category'    => $this->category ?: null,
        ]);

        $this->dispatch('notify', type: 'success', content: __('Project updated.'));
        $this->redirect(route('admin.projects.index'), navigate: true);
    }
};
?>

<div class="mx-auto max-w-3xl space-y-6 p-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <x-ui.button
            as="a"
            href="{{ route('admin.projects.index') }}"
            variant="ghost"
            size="sm"
            icon="arrow-left"
        />
        <x-ui.heading level="h1" size="lg">{{ __('Edit Project') }}</x-ui.heading>
    </div>

    <x-ui.separator />

    <form wire:submit="save" class="space-y-5">

        {{-- Title --}}
        <x-ui.field required>
            <x-ui.label required>{{ __('Title') }}</x-ui.label>
            <x-ui.input wire:model="title" placeholder="{{ __('Project title') }}" />
            <x-ui.error name="title" />
        </x-ui.field>

        {{-- Description --}}
        <x-ui.field>
            <x-ui.label>{{ __('Description') }}</x-ui.label>
            <x-ui.textarea wire:model="description" rows="4" placeholder="{{ __('Describe the project...') }}" />
            <x-ui.error name="description" />
        </x-ui.field>

        <x-ui.separator label="{{ __('Settings') }}" />

        <div class="grid gap-5 sm:grid-cols-2">

            {{-- Status --}}
            <x-ui.field required>
                <x-ui.label required>{{ __('Status') }}</x-ui.label>
                <x-ui.select wire:model="status" placeholder="{{ __('Select a status...') }}">
                    @foreach (ProjectStatus::cases() as $case)
                        <x-ui.select.option :value="$case->value">
                            {{ ucfirst(str_replace('_', ' ', $case->value)) }}
                        </x-ui.select.option>
                    @endforeach
                </x-ui.select>
                <x-ui.error name="status" />
            </x-ui.field>

            {{-- Category --}}
            <x-ui.field>
                <x-ui.label>{{ __('Category') }}</x-ui.label>
                <x-ui.input wire:model="category" placeholder="{{ __('e.g. Web, Mobile, Design') }}" />
                <x-ui.error name="category" />
            </x-ui.field>

            {{-- Owner --}}
            <x-ui.field required class="sm:col-span-2">
                <x-ui.label required>{{ __('Owner') }}</x-ui.label>
                <x-ui.select wire:model="userId" placeholder="{{ __('Select an owner...') }}">
                    @foreach ($this->users as $user)
                        <x-ui.select.option :value="$user->id">{{ $user->name }}</x-ui.select.option>
                    @endforeach
                </x-ui.select>
                <x-ui.error name="userId" />
            </x-ui.field>

        </div>

        <x-ui.separator />

        <div class="flex justify-end gap-3">
            <x-ui.button as="a" href="{{ route('admin.projects.index') }}" variant="ghost">
                {{ __('Cancel') }}
            </x-ui.button>
            <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                {{ __('Save Changes') }}
            </x-ui.button>
        </div>

    </form>
</div>
