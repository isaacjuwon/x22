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
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\HasMedia;
use App\Ai\Agents\WritingAssistance;

new #[Title('New Post'), Layout('layouts::app')] class extends Component {
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:500')]
    public string $excerpt = '';

    #[Validate('required|string')]
    public string $content = '';

    #[Validate('nullable|array')]
    public array $content_json = [];

    #[Validate('required|in:draft,published,archived')]
    public string $status = 'draft';

    #[Validate('nullable|date')]
    public string $publishedAt = '';

    #[Validate('array')]
    public array $tagNames = [];

    #[Validate('nullable|image|max:3072')]
    public mixed $featuredImage = null;

    /** @var array<int, TemporaryUploadedFile> */
    #[Validate(['galleryImages.*' => 'image|max:3072'])]
    public array $galleryImages = [];

    #[Computed]
    public function tagSuggestions(): array
    {
        return Tag::orderBy('name')->pluck('name')->toArray();
    }

    #[Computed]
    public function featuredImagePreviewUrl(): ?string
    {
        return $this->featuredImage instanceof TemporaryUploadedFile
            ? $this->featuredImage->temporaryUrl()
            : null;
    }

    /** @return array<int, string> */
    #[Computed]
    public function galleryPreviewUrls(): array
    {
        return collect($this->galleryImages)
            ->filter(fn (mixed $f) => $f instanceof TemporaryUploadedFile)
            ->map(fn (TemporaryUploadedFile $f) => $f->temporaryUrl())
            ->values()
            ->all();
    }

    public function removeFeaturedImage(): void
    {
        $this->featuredImage = null;
    }

    public function removeGalleryImage(int $index): void
    {
        if (! array_key_exists($index, $this->galleryImages)) {
            return;
        }

        unset($this->galleryImages[$index]);
        $this->galleryImages = array_values($this->galleryImages);
    }

    public function save(): void
    {
        $this->validate();

        $publishedAt = $this->resolvePublishedAt();

        $post = Post::create([
            'user_id'      => auth()->id(),
            'title'        => $this->title,
            'slug'         => Str::slug($this->title),
            'excerpt'      => $this->excerpt ?: null,
            'content'      => $this->content,
            'content_json' => $this->content_json,
            'status'       => $this->status,
            'published_at' => $publishedAt,
        ]);

        $tagIds = collect($this->tagNames)->map(function (string $name): int {
            return Tag::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)],
            )->id;
        })->toArray();

        $post->tags()->sync($tagIds);

        $this->syncMedia($post);

        $this->dispatch('notify', type: 'success', content: __('Post created.'));
        $this->redirect(route('admin.posts.index'), navigate: true);
    }

    public function saveAsDraft(): void
    {
        $this->status = PostStatus::Draft->value;
        $this->save();
    }

    public function generateExcerpt(): void
    {
        if (empty($this->content)) {
            $this->addError('excerpt', 'Please write some content first to generate an excerpt.');
            return;
        }

        $prompt = "Generate a concise, 1-2 sentence excerpt for the following article. Reply ONLY with the excerpt, no other text:\n\n" . strip_tags($this->content);
        $this->excerpt = (new WritingAssistance)->prompt($prompt)->text;
    }

    public function improveContent(): void
    {
        $this->dispatch('ai-action', action: 'improve', content: strip_tags($this->content));
    }

    private function resolvePublishedAt(): ?\Illuminate\Support\Carbon
    {
        if ($this->status === PostStatus::Published->value) {
            return filled($this->publishedAt) ? \Illuminate\Support\Carbon::parse($this->publishedAt) : now();
        }

        return filled($this->publishedAt) ? \Illuminate\Support\Carbon::parse($this->publishedAt) : null;
    }

    private function syncMedia(Post $post): void
    {
        if ($this->featuredImage instanceof TemporaryUploadedFile) {
            $this->attachMedia($post, $this->featuredImage, Post::FEATURED_IMAGE_COLLECTION);
        }

        foreach ($this->galleryImages as $image) {
            if ($image instanceof TemporaryUploadedFile) {
                $this->attachMedia($post, $image, Post::GALLERY_COLLECTION, clearFirst: false);
            }
        }
    }

    private function attachMedia(
        HasMedia $model,
        TemporaryUploadedFile $file,
        string $collection,
        bool $clearFirst = true,
    ): void {
        if ($clearFirst) {
            $model->clearMediaCollection($collection);
        }

        $model->addMedia($file->getRealPath())
            ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            ->usingFileName($file->hashName())
            ->toMediaCollection($collection);
    }
};
?>

<div class="mx-auto max-w-7xl space-y-6 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <x-ui.button
                as="a"
                href="{{ route('admin.posts.index') }}"
                variant="ghost"
                size="sm"
                icon="arrow-left"
            />
            <x-ui.heading level="h1" size="lg">{{ __('New Post') }}</x-ui.heading>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button type="button" variant="ghost" @click="history.back()">
                {{ __('Cancel') }}
            </x-ui.button>
            <x-ui.button
                wire:click="saveAsDraft"
                wire:loading.attr="disabled"
                variant="soft"
                icon="document"
            >
                {{ __('Save as Draft') }}
            </x-ui.button>
            <x-ui.button type="submit" form="post-form" variant="primary" wire:loading.attr="disabled">
                {{ __('Create Post') }}
            </x-ui.button>
        </div>
    </div>

    <x-ui.separator />

    <form wire:submit="save" id="post-form" class="grid gap-6 lg:grid-cols-3">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card class="p-6 space-y-5">
                <x-ui.field required>
                    <x-ui.label required>{{ __('Title') }}</x-ui.label>
                    <x-ui.input wire:model="title" placeholder="{{ __('Post title') }}" />
                    <x-ui.error name="title" />
                </x-ui.field>

                <x-ui.field required wire:ignore>
                    <div class="flex items-center justify-between mb-1">
                        <x-ui.label required>{{ __('Content') }}</x-ui.label>
                        <x-ui.button type="button" wire:click="improveContent" variant="ghost" size="sm" class="text-blue-600 dark:text-blue-400">
                            <x-ui.icon name="sparkles" class="w-4 h-4 mr-1" />
                            {{ __('Improve with AI') }}
                        </x-ui.button>
                    </div>
                    <x-tiptap-editor id="content" name="content" wire:model="content_json" html-model="content" :value="$content_json" />
                    <x-ui.error name="content" />
                </x-ui.field>

                <x-ui.field>
                    <div class="flex items-center justify-between mb-1">
                        <x-ui.label>{{ __('Excerpt') }}</x-ui.label>
                        <x-ui.button type="button" wire:click="generateExcerpt" variant="ghost" size="sm" class="text-blue-600 dark:text-blue-400">
                            <x-ui.icon name="sparkles" class="w-4 h-4 mr-1" />
                            {{ __('Generate with AI') }}
                        </x-ui.button>
                    </div>
                    <x-ui.textarea wire:model="excerpt" rows="3" placeholder="{{ __('Short summary...') }}" />
                    <x-ui.error name="excerpt" />
                </x-ui.field>
            </x-ui.card>

            <x-ui.card class="p-6 space-y-5">
                <x-ui.heading level="h2" size="sm" class="uppercase tracking-widest text-neutral-500">
                    {{ __('Gallery') }}
                </x-ui.heading>
                <x-ui.media.gallery
                    input-model="galleryImages"
                    input-name="galleryImages"
                    :existing-media="collect()"
                    :new-preview-urls="$this->galleryPreviewUrls"
                    remove-new-action="removeGalleryImage"
                    error="galleryImages.*"
                />
            </x-ui.card>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <x-ui.card class="p-6 space-y-5">
                <x-ui.heading level="h2" size="sm" class="uppercase tracking-widest text-neutral-500">
                    {{ __('Publishing') }}
                </x-ui.heading>

                <x-ui.field required>
                    <x-ui.label required>{{ __('Status') }}</x-ui.label>
                    <x-ui.select wire:model="status">
                        @foreach (PostStatus::cases() as $case)
                            <x-ui.select.option :value="$case->value">{{ ucfirst($case->value) }}</x-ui.select.option>
                        @endforeach
                    </x-ui.select>
                    <x-ui.error name="status" />
                </x-ui.field>

                <x-ui.field>
                    <x-ui.label>{{ __('Publish Date') }}</x-ui.label>
                    <x-ui.date-picker wire:model="publishedAt" clearable />
                    <x-ui.description class="text-[10px]">{{ __('Leave blank for immediate.') }}</x-ui.description>
                    <x-ui.error name="publishedAt" />
                </x-ui.field>
            </x-ui.card>

            <x-ui.card class="p-6 space-y-5">
                <x-ui.heading level="h2" size="sm" class="uppercase tracking-widest text-neutral-500">
                    {{ __('Metadata') }}
                </x-ui.heading>

                <x-ui.field>
                    <x-ui.label>{{ __('Tags') }}</x-ui.label>
                    <x-ui.tags-input
                        wire:model="tagNames"
                        placeholder="{{ __('Add tags...') }}"
                        :suggestions="$this->tagSuggestions"
                    />
                    <x-ui.error name="tagNames" />
                </x-ui.field>
            </x-ui.card>

            <x-ui.card class="p-6 space-y-5">
                <x-ui.heading level="h2" size="sm" class="uppercase tracking-widest text-neutral-500">
                    {{ __('Featured Image') }}
                </x-ui.heading>
                <x-ui.media.single
                    :preview-url="$this->featuredImagePreviewUrl"
                    :preview-alt="$title ?: __('Featured image preview')"
                    input-model="featuredImage"
                    input-name="featuredImage"
                    remove-action="removeFeaturedImage"
                    error="featuredImage"
                />
            </x-ui.card>
        </div>
    </form>

    <livewire:admin.posts.ai-assistant />
</div>