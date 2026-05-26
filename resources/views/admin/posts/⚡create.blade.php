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

<div class="mx-auto max-w-4xl space-y-6 p-4 sm:p-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <x-ui.button
                as="a"
                href="{{ route('admin.posts.index') }}"
                variant="ghost"
                size="sm"
                icon="arrow-left"
            />
            <x-ui.heading level="h1" size="lg" class="truncate">{{ __('New Post') }}</x-ui.heading>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <x-ui.button type="button" variant="ghost" size="sm" @click="history.back()" class="flex-1 sm:flex-none">
                {{ __('Cancel') }}
            </x-ui.button>
            <x-ui.button
                wire:click="saveAsDraft"
                wire:loading.attr="disabled"
                variant="soft"
                size="sm"
                icon="document"
                class="flex-1 sm:flex-none"
            >
                {{ __('Save as Draft') }}
            </x-ui.button>
            <x-ui.button type="submit" form="post-form" variant="primary" size="sm" wire:loading.attr="disabled" class="flex-1 sm:flex-none">
                {{ __('Create Post') }}
            </x-ui.button>
        </div>
    </div>

    <x-ui.separator />

    <form wire:submit="save" id="post-form" class="space-y-6">
        {{-- Publishing Controls --}}
        <x-ui.card class="p-4 sm:p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 pb-4 mb-2">
                <x-ui.heading level="h2" size="sm" class="uppercase tracking-widest text-neutral-500 font-bold">
                    {{ __('Publishing & Status') }}
                </x-ui.heading>
                <div class="flex items-center gap-3">
                    <x-ui.switch wire:model="featured" label="{{ __('Featured') }}" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
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
                    <x-ui.label>{{ __('Schedule Publication') }}</x-ui.label>
                    <x-ui.date-picker wire:model="publishedAt" clearable />
                    <x-ui.description class="text-[10px]">{{ __('Leave blank for immediate.') }}</x-ui.description>
                    <x-ui.error name="publishedAt" />
                </x-ui.field>
            </div>
        </x-ui.card>

        {{-- Main Content Section --}}
        <x-ui.card class="p-4 sm:p-6 space-y-5">
            <x-ui.field required>
                <x-ui.label required>{{ __('Title') }}</x-ui.label>
                <x-ui.input wire:model="title" placeholder="{{ __('Enter post title...') }}" class="text-lg sm:text-xl font-bold" />
                <x-ui.error name="title" />
            </x-ui.field>

            <x-ui.field required wire:ignore>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                    <x-ui.label required>{{ __('Content') }}</x-ui.label>
                    <x-ui.button type="button" wire:click="improveContent" variant="ghost" size="xs" icon="sparkles" class="w-full sm:w-auto text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 border border-blue-200/50 dark:border-blue-700/50">
                        {{ __('Improve with AI') }}
                    </x-ui.button>
                </div>
                <x-tiptap-editor id="content" name="content" wire:model="content_json" html-model="content" :value="!empty($content_json) ? $content_json : $content" />
                <x-ui.error name="content" />
            </x-ui.field>
        </x-ui.card>

        {{-- Featured Image & Excerpt --}}
        <div class="grid gap-6 md:grid-cols-2">
            <x-ui.card class="p-4 sm:p-6 space-y-5">
                <x-ui.heading level="h2" size="sm" class="uppercase tracking-widest text-neutral-500 font-bold">
                    {{ __('Featured Image') }}
                </x-ui.heading>
                <x-ui.media.single
                    :preview-url="$this->featuredImagePreviewUrl"
                    :preview-alt="$title ?: __('Featured preview')"
                    input-model="featuredImage"
                    input-name="featuredImage"
                    remove-action="removeFeaturedImage"
                    error="featuredImage"
                />
            </x-ui.card>

            <x-ui.card class="p-4 sm:p-6 space-y-5">
                <div class="flex items-center justify-between">
                    <x-ui.heading level="h2" size="sm" class="uppercase tracking-widest text-neutral-500 font-bold">
                        {{ __('Excerpt') }}
                    </x-ui.heading>
                    <x-ui.button type="button" wire:click="generateExcerpt" variant="ghost" size="xs" icon="sparkles" class="text-blue-600 dark:text-blue-400">
                        {{ __('Generate') }}
                    </x-ui.button>
                </div>
                <x-ui.field>
                    <x-ui.textarea wire:model="excerpt" rows="6" placeholder="{{ __('Brief summary...') }}" />
                    <x-ui.error name="excerpt" />
                </x-ui.field>
            </x-ui.card>
        </div>

        {{-- Tags & Gallery --}}
        <div class="grid gap-6 md:grid-cols-2">
            <x-ui.card class="p-4 sm:p-6 space-y-5">
                <x-ui.heading level="h2" size="sm" class="uppercase tracking-widest text-neutral-500 font-bold">
                    {{ __('Classification') }}
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

            <x-ui.card class="p-4 sm:p-6 space-y-5">
                <x-ui.heading level="h2" size="sm" class="uppercase tracking-widest text-neutral-500 font-bold">
                    {{ __('Gallery') }}
                </x-ui.heading>
                <x-ui.media.gallery
                    input-model="galleryImages"
                    input-name="galleryImages"
                    :new-preview-urls="$this->galleryPreviewUrls"
                    remove-new-action="removeGalleryImage"
                    error="galleryImages.*"
                />
            </x-ui.card>
        </div>

        {{-- Form Actions (Bottom) --}}
        <div class="flex items-center justify-end gap-3 pt-6">
            <x-ui.button type="button" variant="ghost" @click="history.back()">
                {{ __('Cancel') }}
            </x-ui.button>
            <x-ui.button type="submit" variant="primary" size="lg" icon="check" class="px-8" wire:loading.attr="disabled">
                {{ __('Create Post') }}
            </x-ui.button>
        </div>
    </form>

    <livewire:admin::posts.ai-assistant />
</div>