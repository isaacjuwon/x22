<?php

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Edit Post'), Layout('layouts::app')] class extends Component {

    public Post $post;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:500')]
    public string $excerpt = '';

    #[Validate('required|string')]
    public string $content = '';

    #[Validate('required|in:draft,published,archived')]
    public string $status = '';

    #[Validate('boolean')]
    public bool $featured = false;

    #[Validate('nullable|date')]
    public string $publishedAt = '';

    #[Validate('nullable|string|max:160')]
    public string $metaDescription = '';

    #[Validate('nullable|string|max:255')]
    public string $ogImage = '';

    /** @var array<int, string> */
    #[Validate('array')]
    public array $tagNames = [];

    #[Computed]
    public function tagSuggestions(): array
    {
        return Tag::orderBy('name')->pluck('name')->toArray();
    }

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->excerpt = $post->excerpt ?? '';
        $this->content = $post->content;
        $this->status = $post->status->value;
        $this->featured = $post->featured;
        $this->publishedAt = $post->published_at?->format('Y-m-d') ?? '';
        $this->metaDescription = $post->meta_description ?? '';
        $this->ogImage = $post->og_image ?? '';
        $this->tagNames = $post->tags->pluck('name')->toArray();
    }

    public function save(): void
    {
        $this->validate();

        $this->post->update([
            'title'            => $this->title,
            'slug'             => Str::slug($this->title),
            'excerpt'          => $this->excerpt ?: null,
            'content'          => $this->content,
            'status'           => $this->status,
            'featured'         => $this->featured,
            'published_at'     => $this->publishedAt ?: null,
            'meta_description' => $this->metaDescription ?: null,
            'og_image'         => $this->ogImage ?: null,
        ]);

        // Resolve tag names to IDs, creating new tags as needed
        $tagIds = collect($this->tagNames)->map(function (string $name): int {
            return Tag::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)]
            )->id;
        })->toArray();

        $this->post->tags()->sync($tagIds);

        $this->dispatch('notify', type: 'success', content: __('Post updated.'));
        $this->redirect(route('admin.posts.index'), navigate: true);
    }
};
?>

<div class="mx-auto max-w-3xl space-y-6 p-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <x-ui.button
            as="a"
            href="{{ route('admin.posts.index') }}"
            variant="ghost"
            size="sm"
            icon="arrow-left"
        />
        <x-ui.heading level="h1" size="lg">{{ __('Edit Post') }}</x-ui.heading>
    </div>

    <x-ui.separator />

    <form wire:submit="save" class="space-y-5">

        {{-- Title --}}
        <x-ui.field required>
            <x-ui.label required>{{ __('Title') }}</x-ui.label>
            <x-ui.input wire:model="title" placeholder="{{ __('Post title') }}" />
            <x-ui.error name="title" />
        </x-ui.field>

        {{-- Excerpt --}}
        <x-ui.field>
            <x-ui.label>{{ __('Excerpt') }}</x-ui.label>
            <x-ui.textarea wire:model="excerpt" rows="2" placeholder="{{ __('Short summary...') }}" />
            <x-ui.error name="excerpt" />
        </x-ui.field>

        {{-- Content --}}
        <x-ui.field required>
            <x-ui.label required>{{ __('Content') }}</x-ui.label>
            <x-ui.textarea wire:model="content" rows="12" placeholder="{{ __('Write your post...') }}" />
            <x-ui.error name="content" />
        </x-ui.field>

        <x-ui.separator label="{{ __('Settings') }}" />

        <div class="grid gap-5 sm:grid-cols-2">

            {{-- Status --}}
            <x-ui.field required>
                <x-ui.label required>{{ __('Status') }}</x-ui.label>
                <x-ui.select wire:model="status">
                    @foreach (PostStatus::cases() as $case)
                        <x-ui.select.option :value="$case->value">{{ ucfirst($case->value) }}</x-ui.select.option>
                    @endforeach
                </x-ui.select>
                <x-ui.error name="status" />
            </x-ui.field>

            {{-- Publish Date --}}
            <x-ui.field>
                <x-ui.label>{{ __('Publish Date') }}</x-ui.label>
                <x-ui.date-picker wire:model="publishedAt" clearable />
                <x-ui.error name="publishedAt" />
            </x-ui.field>

        </div>

        {{-- Tags --}}
        <x-ui.field>
            <x-ui.label>{{ __('Tags') }}</x-ui.label>
            <x-ui.tags-input
                wire:model="tagNames"
                placeholder="{{ __('Add tags...') }}"
                :suggestions="$this->tagSuggestions"
            />
            <x-ui.error name="tagNames" />
        </x-ui.field>

        <x-ui.separator label="{{ __('SEO') }}" />

        {{-- Meta Description --}}
        <x-ui.field>
            <x-ui.label>{{ __('Meta Description') }}</x-ui.label>
            <x-ui.textarea wire:model="metaDescription" rows="2" placeholder="{{ __('160 characters max...') }}" />
            <x-ui.error name="metaDescription" />
        </x-ui.field>

        {{-- OG Image --}}
        <x-ui.field>
            <x-ui.label>{{ __('OG Image URL') }}</x-ui.label>
            <x-ui.input wire:model="ogImage" placeholder="https://..." leftIcon="photo" />
            <x-ui.error name="ogImage" />
        </x-ui.field>

        {{-- Featured --}}
        <x-ui.switch wire:model="featured" label="{{ __('Featured post') }}" />

        <x-ui.separator />

        <div class="flex justify-end gap-3">
            <x-ui.button as="a" href="{{ route('admin.posts.index') }}" variant="ghost">
                {{ __('Cancel') }}
            </x-ui.button>
            <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                {{ __('Save Changes') }}
            </x-ui.button>
        </div>

    </form>
</div>
