<?php

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
use Laravel\Ai\Ai;

new #[Title('New Post'), Layout('layouts::app')] class extends Component {
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:500')]
    public string $excerpt = '';

    #[Validate('required|string')]
    public string $content = '';

    #[Validate('nullable|image|max:3072')]
    public mixed $featuredImage = null;

    /** @var array<int, TemporaryUploadedFile> */
    #[Validate(['galleryImages.*' => 'image|max:3072'])]
    public array $galleryImages = [];

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

        $post = Post::create([
            'user_id'   => auth()->id(),
            'title'     => $this->title,
            'slug'      => Str::slug($this->title),
            'excerpt'   => $this->excerpt ?: null,
            'content'   => $this->content,
        ]);

        $this->syncMedia($post);

        $this->dispatch('notify', type: 'success', content: __('Post created.'));
        $this->redirect(route('admin.posts.index'), navigate: true);
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

    public function generateExcerpt(): void
    {
        if (empty($this->content)) {
            $this->addError('excerpt', 'Please write some content first to generate an excerpt.');
            return;
        }

        $prompt = "Generate a concise, 1-2 sentence excerpt for the following article. Reply ONLY with the excerpt, no other text:\n\n" . strip_tags($this->content);
        $this->excerpt = Ai::ask($prompt);
    }
};
?>

<div class="mx-auto max-w-3xl space-y-6 p-6">
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

    <x-ui.separator />

    <form wire:submit="save" class="space-y-5">
        <x-ui.field>
            <x-ui.label required>{{ __('Title') }}</x-ui.label>
            <x-ui.input wire:model="title" placeholder="{{ __('Post title') }}" />
            <x-ui.error name="title" />
        </x-ui.field>

        <x-ui.field>
            <div class="flex items-center justify-between mb-1">
                <x-ui.label>{{ __('Excerpt') }}</x-ui.label>
                <x-ui.button wire:click="generateExcerpt" variant="ghost" size="sm" class="text-blue-600 dark:text-blue-400">
                    <x-ui.icon name="sparkles" class="w-4 h-4 mr-1" />
                    {{ __('Generate with AI') }}
                </x-ui.button>
            </div>
            <x-ui.textarea wire:model="excerpt" rows="2" placeholder="{{ __('Short summary...') }}" />
            <x-ui.error name="excerpt" />
        </x-ui.field>

        <x-ui.field>
            <x-ui.label required>{{ __('Content') }}</x-ui.label>
	    <x-tiptap-editor id="content" name="content" wire:model="content" :value="$content" />
           {{-- <x-ui.textarea wire:model="content" rows="12" placeholder="{{ __('Write your post...') }}" /> --}}
            <x-ui.error name="content" />
        </x-ui.field>

        <x-ui.separator label="{{ __('Media') }}" />

        <x-ui.media.single
            :label="__('Featured Image')"
            :hint="__('Used in cards and as the default fallback for social sharing.')"
            :preview-url="$this->featuredImagePreviewUrl"
            :preview-alt="$title ?: __('Featured image preview')"
            input-model="featuredImage"
            input-name="featuredImage"
            remove-action="removeFeaturedImage"
            error="featuredImage"
        />

        <x-ui.media.gallery
            :label="__('Gallery')"
            :hint="__('Optional supporting images shown alongside the post.')"
            input-model="galleryImages"
            input-name="galleryImages"
            :existing-media="collect()"
            :new-preview-urls="$this->galleryPreviewUrls"
            remove-new-action="removeGalleryImage"
            error="galleryImages.*"
        />

        <x-ui.separator />

        <div class="flex justify-end gap-3">
            <x-ui.button as="a" href="{{ route('admin.posts.index') }}" variant="ghost">
                {{ __('Cancel') }}
            </x-ui.button>
            <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                {{ __('Create Post') }}
            </x-ui.button>
        </div>
    </form>
</div>