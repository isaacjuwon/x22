<?php

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Ai\Agents\WritingAssistance;

new #[Title('Edit Post'), Layout('layouts::app')] class extends Component {
    use WithFileUploads;

    public Post $post;

    public string $title = '';

    public string $excerpt = '';

    public string $content = '';

    public string $status = '';

    public bool $featured = false;

    public string $publishedAt = '';

    public string $metaDescription = '';

    public string $ogImage = '';

    /** @var array<int, string> */
    public array $tagNames = [];

    public mixed $featuredImage = null;

    /** @var array<int, TemporaryUploadedFile> */
    public array $galleryImages = [];

    public mixed $seoOgImage = null;

    public bool $shouldRemoveFeaturedImage = false;

    public bool $shouldRemoveSeoOgImage = false;

    /** @var array<int, int> */
    public array $removedGalleryMediaIds = [];

    #[Computed]
    public function tagSuggestions(): array
    {
        return Tag::orderBy('name')->pluck('name')->toArray();
    }

    public function mount(Post $post): void
    {
        $this->post = $post->load('tags');
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

    #[Computed]
    public function currentFeaturedImageUrl(): ?string
    {
        if ($this->featuredImage instanceof TemporaryUploadedFile) {
            return $this->featuredImage->temporaryUrl();
        }

        if ($this->shouldRemoveFeaturedImage) {
            return null;
        }

        return $this->post->featuredImageUrl('card');
    }

    #[Computed]
    public function currentSeoOgImageUrl(): ?string
    {
        if ($this->seoOgImage instanceof TemporaryUploadedFile) {
            return $this->seoOgImage->temporaryUrl();
        }

        if ($this->shouldRemoveSeoOgImage) {
            return blank($this->ogImage) ? null : $this->ogImage;
        }

        return $this->post->seoOgImageUrl();
    }

    /**
     * @return Collection<int, Media>
     */
    #[Computed]
    public function existingGalleryMedia(): Collection
    {
        return $this->post->galleryMedia()
            ->reject(fn (Media $media): bool => in_array($media->id, $this->removedGalleryMediaIds, true))
            ->values();
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function newGalleryPreviewUrls(): array
    {
        return collect($this->galleryImages)
            ->filter(fn (mixed $file): bool => $file instanceof TemporaryUploadedFile)
            ->map(fn (TemporaryUploadedFile $file): string => $file->temporaryUrl())
            ->values()
            ->all();
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
            'featured' => ['boolean'],
            'publishedAt' => ['nullable', 'date'],
            'metaDescription' => ['nullable', 'string', 'max:160'],
            'ogImage' => ['nullable', 'url', 'max:255'],
            'tagNames' => ['array'],
            'tagNames.*' => ['string', 'max:50'],
            'featuredImage' => ['nullable', 'image', 'max:3072'],
            'galleryImages' => ['array'],
            'galleryImages.*' => ['image', 'max:3072'],
            'seoOgImage' => ['nullable', 'image', 'max:3072'],
        ];
    }

    public function removeFeaturedImageMedia(): void
    {
        $this->featuredImage = null;
        $this->shouldRemoveFeaturedImage = true;
    }

    public function removeSeoOgImageMedia(): void
    {
        $this->seoOgImage = null;
        $this->shouldRemoveSeoOgImage = true;
    }

    public function removeExistingGalleryMedia(int $mediaId): void
    {
        if (! in_array($mediaId, $this->removedGalleryMediaIds, true)) {
            $this->removedGalleryMediaIds[] = $mediaId;
        }
    }

    public function removeNewGalleryMedia(int $index): void
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

        $this->post->update([
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'excerpt' => $this->excerpt ?: null,
            'content' => $this->content,
            'status' => $this->status,
            'featured' => $this->featured,
            'published_at' => $publishedAt,
            'meta_description' => $this->metaDescription ?: null,
            'og_image' => $this->ogImage ?: null,
        ]);

        $tagIds = collect($this->tagNames)->map(function (string $name): int {
            return Tag::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)],
            )->id;
        })->toArray();

        $this->post->tags()->sync($tagIds);

        $this->syncMedia();

        $this->dispatch('notify', type: 'success', content: __('Post updated.'));
        $this->redirect(route('admin.posts.index'), navigate: true);
    }

    private function resolvePublishedAt(): ?\Illuminate\Support\Carbon
    {
        if ($this->status === PostStatus::Published->value) {
            if (filled($this->publishedAt)) {
                return \Illuminate\Support\Carbon::parse($this->publishedAt);
            }

            return $this->post->published_at ?? now();
        }

        return filled($this->publishedAt) ? \Illuminate\Support\Carbon::parse($this->publishedAt) : null;
    }

    protected function syncMedia(): void
    {
        if ($this->shouldRemoveFeaturedImage && ! $this->featuredImage instanceof TemporaryUploadedFile) {
            $this->post->clearMediaCollection(Post::FEATURED_IMAGE_COLLECTION);
        }

        if ($this->featuredImage instanceof TemporaryUploadedFile) {
            $this->attachUploadedMedia($this->post, $this->featuredImage, Post::FEATURED_IMAGE_COLLECTION);
        }

        if ($this->shouldRemoveSeoOgImage && ! $this->seoOgImage instanceof TemporaryUploadedFile) {
            $this->post->clearMediaCollection(Post::SEO_OG_IMAGE_COLLECTION);
        }

        if ($this->seoOgImage instanceof TemporaryUploadedFile) {
            $this->attachUploadedMedia($this->post, $this->seoOgImage, Post::SEO_OG_IMAGE_COLLECTION);
        }

        $this->post->galleryMedia()
            ->filter(fn (Media $media): bool => in_array($media->id, $this->removedGalleryMediaIds, true))
            ->each(fn (Media $media) => $media->delete());

        foreach ($this->galleryImages as $galleryImage) {
            if ($galleryImage instanceof TemporaryUploadedFile) {
                $this->attachUploadedMedia($this->post, $galleryImage, Post::GALLERY_COLLECTION, false);
            }
        }
    }

    protected function attachUploadedMedia(
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
            <x-ui.heading level="h1" size="lg">{{ __('Edit Post') }}</x-ui.heading>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button type="button" variant="ghost" @click="history.back()">
                {{ __('Cancel') }}
            </x-ui.button>
            <x-ui.button type="submit" form="post-form" variant="primary" wire:loading.attr="disabled">
                {{ __('Save Changes') }}
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
                    <x-tiptap-editor id="content" name="content" wire:model="content" :value="$content" />
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
                    :existing-media="$this->existingGalleryMedia"
                    :new-preview-urls="$this->newGalleryPreviewUrls"
                    remove-existing-action="removeExistingGalleryMedia"
                    remove-new-action="removeNewGalleryMedia"
                    error="galleryImages.*"
                />
            </x-ui.card>

            <x-ui.card class="p-6 space-y-5">
                <x-ui.heading level="h2" size="sm" class="uppercase tracking-widest text-neutral-500">
                    {{ __('SEO') }}
                </x-ui.heading>

                <x-ui.field>
                    <x-ui.label>{{ __('Meta Description') }}</x-ui.label>
                    <x-ui.textarea wire:model="metaDescription" rows="2" placeholder="{{ __('160 characters max...') }}" />
                    <x-ui.error name="metaDescription" />
                </x-ui.field>

                <x-ui.media.single
                    :label="__('SEO OG Image')"
                    :preview-url="$this->currentSeoOgImageUrl"
                    :preview-alt="$title ?: __('SEO image preview')"
                    input-model="seoOgImage"
                    input-name="seoOgImage"
                    remove-action="removeSeoOgImageMedia"
                    error="seoOgImage"
                />

                <x-ui.field>
                    <x-ui.label>{{ __('Legacy OG Image URL') }}</x-ui.label>
                    <x-ui.input wire:model="ogImage" placeholder="https://..." leftIcon="photo" />
                    <x-ui.description class="text-[10px]">{{ __('Compatibility fallback.') }}</x-ui.description>
                    <x-ui.error name="ogImage" />
                </x-ui.field>
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
                    <x-ui.description class="text-[10px]">{{ __('Leave blank for current time.') }}</x-ui.description>
                    <x-ui.error name="publishedAt" />
                </x-ui.field>

                <x-ui.switch wire:model="featured" label="{{ __('Featured post') }}" />
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
                    :preview-url="$this->currentFeaturedImageUrl"
                    :preview-alt="$title ?: __('Featured image preview')"
                    input-model="featuredImage"
                    input-name="featuredImage"
                    remove-action="removeFeaturedImageMedia"
                    error="featuredImage"
                />
            </x-ui.card>
        </div>
    </form>

    <livewire:admin.posts.ai-assistant />
</div>
