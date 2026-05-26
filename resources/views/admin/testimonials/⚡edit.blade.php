<?php

use App\Enums\TestimonialStatus;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Edit Testimonial'), Layout('layouts::app')] class extends Component {

    public Testimonial $testimonial;

    #[Validate('required|exists:users,id')]
    public string $userId = '';

    #[Validate('nullable|exists:projects,id')]
    public string $projectId = '';

    #[Validate('required|integer|min:1|max:5')]
    public int $rating = 5;

    #[Validate('required|string|min:10')]
    public string $comment = '';

    #[Validate('required|in:pending,approved,rejected')]
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

    public function mount(Testimonial $testimonial): void
    {
        $this->testimonial = $testimonial;
        $this->userId = (string) $testimonial->user_id;
        $this->projectId = $testimonial->project_id ? (string) $testimonial->project_id : '';
        $this->rating = $testimonial->rating;
        $this->comment = $testimonial->comment;
        $this->status = $testimonial->status->value;
    }

    public function save(): void
    {
        $this->validate();

        $this->testimonial->update([
            'user_id'    => $this->userId,
            'project_id' => $this->projectId ?: null,
            'rating'     => $this->rating,
            'comment'    => $this->comment,
            'status'     => $this->status,
        ]);

        $this->dispatch('notify', type: 'success', content: __('Testimonial updated.'));
        $this->redirect(route('admin.testimonials.index'), navigate: true);
    }
};
?>

<div class="mx-auto max-w-3xl space-y-6 p-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <x-ui.button
            as="a"
            href="{{ route('admin.testimonials.index') }}"
            variant="ghost"
            size="sm"
            icon="arrow-left"
        />
        <x-ui.heading level="h1" size="lg">{{ __('Edit Testimonial') }}</x-ui.heading>
    </div>

    <x-ui.separator />

    <form wire:submit="save" class="space-y-5">

        {{-- Client --}}
        <x-ui.field required>
            <x-ui.label required>{{ __('Client') }}</x-ui.label>
            <x-ui.select wire:model="userId" placeholder="{{ __('Select a user...') }}">
                @foreach ($this->users as $user)
                    <x-ui.select.option :value="$user->id">{{ $user->name }} — {{ $user->email }}</x-ui.select.option>
                @endforeach
            </x-ui.select>
            <x-ui.error name="userId" />
        </x-ui.field>

        {{-- Project --}}
        <x-ui.field>
            <x-ui.label>{{ __('Project') }}</x-ui.label>
            <x-ui.select wire:model="projectId" placeholder="{{ __('No project (optional)') }}" clearable>
                @foreach ($this->projects as $project)
                    <x-ui.select.option :value="$project->id">{{ $project->title }}</x-ui.select.option>
                @endforeach
            </x-ui.select>
            <x-ui.error name="projectId" />
        </x-ui.field>

        {{-- Comment --}}
        <x-ui.field required>
            <x-ui.label required>{{ __('Comment') }}</x-ui.label>
            <x-ui.textarea wire:model="comment" rows="4" placeholder="{{ __('What did the client say?') }}" />
            <x-ui.error name="comment" />
        </x-ui.field>

        <x-ui.separator label="{{ __('Details') }}" />

        <div class="grid gap-5 sm:grid-cols-2">

            {{-- Rating --}}
            <x-ui.field required>
                <x-ui.label required>{{ __('Rating') }}</x-ui.label>
                <x-ui.select wire:model="rating" placeholder="{{ __('Select a rating...') }}">
                    @foreach (range(5, 1) as $star)
                        <x-ui.select.option :value="$star">
                            {{ $star }} {{ $star === 1 ? __('star') : __('stars') }}
                        </x-ui.select.option>
                    @endforeach
                </x-ui.select>
                <x-ui.error name="rating" />
            </x-ui.field>

            {{-- Status --}}
            <x-ui.field required>
                <x-ui.label required>{{ __('Status') }}</x-ui.label>
                <x-ui.select wire:model="status" placeholder="{{ __('Select a status...') }}">
                    @foreach (TestimonialStatus::cases() as $case)
                        <x-ui.select.option :value="$case->value">{{ ucfirst($case->value) }}</x-ui.select.option>
                    @endforeach
                </x-ui.select>
                <x-ui.error name="status" />
            </x-ui.field>

        </div>

        <x-ui.separator />

        <div class="flex justify-end gap-3">
            <x-ui.button as="a" href="{{ route('admin.testimonials.index') }}" variant="ghost">
                {{ __('Cancel') }}
            </x-ui.button>
            <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                {{ __('Save Changes') }}
            </x-ui.button>
        </div>

    </form>
</div>
