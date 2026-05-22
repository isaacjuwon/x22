<?php

use App\Enums\PageStatus;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\HasMedia;

new #[Title('New Page'), Layout('layouts::app')] class extends Component {
    use WithFileUploads;

    public string $title = '';

    public string $excerpt = '';

    public string $content = '';

    public string $status = PageStatus::Draft->value;

    public string $publishedAt = '';

    public string $metaDescription = '';

    public string $ogImage = '';

    public mixed $featuredImage = null;

    /** @var array<int, TemporaryUploadedFile> */
    public array $galleryImages = [];

    public mixed $seoOgImage = null;

    #[Computed]
    public function currentFeaturedImageUrl(): ?string
    {
        if ($this->featuredImage instanceof TemporaryUploadedFile) {
            return $this->featuredImage->temporaryUrl();
        }

        return null;
    }

    #[Computed]
    public function currentSeoOgImageUrl(): ?string
    {
        if ($this->seoOgImage instanceof TemporaryUploadedFile) {
            return $this->seoOgImage->temporaryUrl();
        }

        return blank($this->ogImage) ? null : $this->ogImage;
    }

    /**
     * @return Collection<int, mixed>
     */
    #[Computed]
    public function existingGalleryMedia(): Collection
    {
        return collect();
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
            'publishedAt' => ['nullable', 'date'],
            'metaDescription' => ['nullable', 'string', 'max:160'],
            'ogImage' => ['nullable', 'url', 'max:255'],
            'featuredImage' => ['nullable', 'image', 'max:3072'],
            'galleryImages' => ['array'],
            'galleryImages.*' => ['image', 'max:3072'],
            'seoOgImage' => ['nullable', 'image', 'max:3072'],
        ];
    }

    public function removeFeaturedImageMedia(): void
    {
        $this->featuredImage = null;
    }

    public function removeSeoOgImageMedia(): void
    {
        $this->seoOgImage = null;
        $this->ogImage = '';
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

        $page = Page::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'excerpt' => $this->excerpt ?: null,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->publishedAt ?: null,
            'meta_description' => $this->metaDescription ?: null,
            'og_image' => $this->ogImage ?: null,
        ]);

        $this->syncMedia($page);

        $this->dispatch('notify', type: 'success', content: __('Page created.'));
        $this->redirect(route('admin.pages.index'), navigate: true);
    }

    protected function syncMedia(Page $page): void
    {
        if ($this->featuredImage instanceof TemporaryUploadedFile) {
            $this->attachUploadedMedia($page, $this->featuredImage, Page::FEATURED_IMAGE_COLLECTION);
        }

        if ($this->seoOgImage instanceof TemporaryUploadedFile) {
            $this->attachUploadedMedia($page, $this->seoOgImage, Page::SEO_OG_IMAGE_COLLECTION);
        }

        foreach ($this->galleryImages as $galleryImage) {
            if ($galleryImage instanceof TemporaryUploadedFile) {
                $this->attachUploadedMedia($page, $galleryImage, Page::GALLERY_COLLECTION, false);
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
};
?>

<div class="mx-auto max-w-3xl space-y-6 p-6">
    <div class="flex items-center gap-3">
        <x-ui.button
            as="a"
            href="{{ route('admin.pages.index') }}"
            variant="ghost"
            size="sm"
            icon="arrow-left"
        />
        <x-ui.heading level="h1" size="lg">{{ __('New Page') }}</x-ui.heading>
    </div>

    <x-ui.separator />

    <form wire:submit="save" class="space-y-5">
        <x-ui.field required>
            <x-ui.label required>{{ __('Title') }}</x-ui.label>
            <x-ui.input wire:model="title" placeholder="{{ __('Page title') }}" />
            <x-ui.error name="title" />
        </x-ui.field>

        <x-ui.field>
            <x-ui.label>{{ __('Excerpt') }}</x-ui.label>
            <x-ui.textarea wire:model="excerpt" rows="2" placeholder="{{ __('Short summary...') }}" />
            <x-ui.error name="excerpt" />
        </x-ui.field>

        <x-ui.field required>
            <x-ui.label required>{{ __('Content') }}</x-ui.label>
            <x-ui.textarea wire:model="content" rows="14" placeholder="{{ __('Write your page content...') }}" />
            <x-ui.error name="content" />
        </x-ui.field>

        <x-ui.separator label="{{ __('Settings') }}" />

        <div class="grid gap-5 sm:grid-cols-2">
            <x-ui.field required>
                <x-ui.label required>{{ __('Status') }}</x-ui.label>
                <x-ui.select wire:model="status">
                    @foreach (PageStatus::cases() as $case)
                        <x-ui.select.option :value="$case->value">{{ ucfirst($case->value) }}</x-ui.select.option>
                    @endforeach
                </x-ui.select>
                <x-ui.error name="status" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.label>{{ __('Publish Date') }}</x-ui.label>
                <x-ui.date-picker wire:model="publishedAt" clearable />
                <x-ui.error name="publishedAt" />
            </x-ui.field>
        </div>

        <x-ui.separator label="{{ __('Media') }}" />

        <x-ui.media.single
            :label="__('Featured Image')"
            :hint="__('Primary page image used for cards and fallback social sharing.')"
            :preview-url="$this->currentFeaturedImageUrl"
            :preview-alt="$title ?: __('Featured image preview')"
            input-model="featuredImage"
            input-name="featuredImage"
            remove-action="removeFeaturedImageMedia"
            error="featuredImage"
        />

        <x-ui.media.gallery
            :label="__('Gallery')"
            :hint="__('Optional supporting images stored with this page.')"
            input-model="galleryImages"
            input-name="galleryImages"
            :existing-media="$this->existingGalleryMedia"
            :new-preview-urls="$this->newGalleryPreviewUrls"
            remove-new-action="removeNewGalleryMedia"
            error="galleryImages.*"
        />

        <x-ui.separator label="{{ __('SEO') }}" />

        <x-ui.field>
            <x-ui.label>{{ __('Meta Description') }}</x-ui.label>
            <x-ui.textarea wire:model="metaDescription" rows="2" placeholder="{{ __('160 characters max...') }}" />
            <x-ui.error name="metaDescription" />
        </x-ui.field>

        <x-ui.media.single
            :label="__('SEO OG Image')"
            :hint="__('Overrides the featured image for social sharing when present.')"
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
            <x-ui.description>{{ __('Retained only as a compatibility fallback for existing records.') }}</x-ui.description>
            <x-ui.error name="ogImage" />
        </x-ui.field>

        <x-ui.separator />

        <div class="flex justify-end gap-3">
            <x-ui.button as="a" href="{{ route('admin.pages.index') }}" variant="ghost">
                {{ __('Cancel') }}
            </x-ui.button>
            <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                {{ __('Create Page') }}
            </x-ui.button>
        </div>
    </form>
</div>
