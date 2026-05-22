<?php

use App\Models\SettingAsset;
use App\Settings\SeoSettings;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('SEO Settings'), Layout('layouts::app')] class extends Component {
    use WithFileUploads;

    public string $meta_title = '';

    public string $meta_description = '';

    public string $meta_keywords = '';

    public string $og_image = '';

    public string $og_type = '';

    public string $twitter_card = '';

    public string $twitter_site = '';

    public string $google_analytics_id = '';

    public string $google_tag_manager_id = '';

    public bool $index_site = true;

    public mixed $seoOgImageUpload = null;

    public bool $shouldRemoveSeoOgImage = false;

    public function mount(SeoSettings $settings): void
    {
        $this->meta_title = $settings->meta_title;
        $this->meta_description = $settings->meta_description;
        $this->meta_keywords = $settings->meta_keywords;
        $this->og_image = $settings->og_image;
        $this->og_type = $settings->og_type;
        $this->twitter_card = $settings->twitter_card;
        $this->twitter_site = $settings->twitter_site;
        $this->google_analytics_id = $settings->google_analytics_id;
        $this->google_tag_manager_id = $settings->google_tag_manager_id;
        $this->index_site = $settings->index_site;
    }

    #[Computed]
    public function currentSeoOgImageUrl(): ?string
    {
        if ($this->seoOgImageUpload instanceof TemporaryUploadedFile) {
            return $this->seoOgImageUpload->temporaryUrl();
        }

        if ($this->shouldRemoveSeoOgImage) {
            return null;
        }

        return SettingAsset::findForKey(SettingAsset::SEO_OG_IMAGE_KEY)?->fileUrl('og') ?: (blank($this->og_image) ? null : $this->og_image);
    }

    protected function rules(): array
    {
        return [
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'og_type' => ['nullable', 'string', 'max:50'],
            'twitter_card' => ['nullable', 'string', 'max:50'],
            'twitter_site' => ['nullable', 'string', 'max:100'],
            'google_analytics_id' => ['nullable', 'string', 'max:50'],
            'google_tag_manager_id' => ['nullable', 'string', 'max:50'],
            'index_site' => ['boolean'],
            'seoOgImageUpload' => ['nullable', 'image', 'max:3072'],
        ];
    }

    public function removeSeoOgImageMedia(): void
    {
        $this->seoOgImageUpload = null;
        $this->shouldRemoveSeoOgImage = true;
    }

    public function save(SeoSettings $settings): void
    {
        $this->validate();

        $settings->meta_title = $this->meta_title;
        $settings->meta_description = $this->meta_description;
        $settings->meta_keywords = $this->meta_keywords;
        $settings->og_type = $this->og_type;
        $settings->twitter_card = $this->twitter_card;
        $settings->twitter_site = $this->twitter_site;
        $settings->google_analytics_id = $this->google_analytics_id;
        $settings->google_tag_manager_id = $this->google_tag_manager_id;
        $settings->index_site = $this->index_site;

        if ($this->shouldRemoveSeoOgImage) {
            $settings->og_image = '';
        }

        $settings->save();

        $asset = SettingAsset::forKey(SettingAsset::SEO_OG_IMAGE_KEY);

        if ($this->shouldRemoveSeoOgImage && ! $this->seoOgImageUpload instanceof TemporaryUploadedFile) {
            $asset->clearMediaCollection(SettingAsset::FILE_COLLECTION);
        }

        if ($this->seoOgImageUpload instanceof TemporaryUploadedFile) {
            $asset->clearMediaCollection(SettingAsset::FILE_COLLECTION);
            $asset->addMedia($this->seoOgImageUpload->getRealPath())
                ->usingName(pathinfo($this->seoOgImageUpload->getClientOriginalName(), PATHINFO_FILENAME))
                ->usingFileName($this->seoOgImageUpload->hashName())
                ->toMediaCollection(SettingAsset::FILE_COLLECTION);
        }

        $this->dispatch('notify', type: 'success', content: __('SEO settings saved.'));
    }
};
?>

<div class="mx-auto max-w-4xl space-y-6 p-6">
    <div>
        <x-ui.heading level="h1" size="lg">{{ __('Site Settings') }}</x-ui.heading>
        <x-ui.text class="mt-1 text-neutral-500 dark:text-neutral-400">
            {{ __('Manage your site configuration.') }}
        </x-ui.text>
    </div>

    <x-ui.separator />

    <x-admin::settings.layout
        :heading="__('SEO')"
        :subheading="__('Control how your site appears in search engines and social shares.')"
    >
        <form wire:submit="save" class="space-y-6">
            <x-ui.separator :label="__('Meta Tags')" />

            <x-ui.field>
                <x-ui.label>{{ __('Meta Title') }}</x-ui.label>
                <x-ui.input wire:model="meta_title" placeholder="{{ __('My Portfolio - Developer & Designer') }}" />
                <x-ui.description>{{ __('Shown in browser tabs and search results. ~60 characters recommended.') }}</x-ui.description>
                <x-ui.error name="meta_title" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.label>{{ __('Meta Description') }}</x-ui.label>
                <x-ui.textarea wire:model="meta_description" rows="3"
                    placeholder="{{ __('A brief description of your site for search engines...') }}" />
                <x-ui.description>{{ __('~160 characters recommended.') }}</x-ui.description>
                <x-ui.error name="meta_description" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.label>{{ __('Meta Keywords') }}</x-ui.label>
                <x-ui.input wire:model="meta_keywords" placeholder="{{ __('portfolio, developer, laravel, design') }}" />
                <x-ui.description>{{ __('Comma-separated keywords.') }}</x-ui.description>
                <x-ui.error name="meta_keywords" />
            </x-ui.field>

            <x-ui.separator :label="__('Open Graph')" />

            <x-ui.media.single
                :label="__('Default OG Image')"
                :hint="__('Recommended size: 1200x630px. This image is used site-wide unless a post or page overrides it.')"
                :preview-url="$this->currentSeoOgImageUrl"
                :preview-alt="__('Default SEO image preview')"
                input-model="seoOgImageUpload"
                input-name="seoOgImageUpload"
                remove-action="removeSeoOgImageMedia"
                error="seoOgImageUpload"
            />

            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.field>
                    <x-ui.label>{{ __('OG Type') }}</x-ui.label>
                    <x-ui.select wire:model="og_type">
                        <x-ui.select.option value="website">website</x-ui.select.option>
                        <x-ui.select.option value="profile">profile</x-ui.select.option>
                        <x-ui.select.option value="blog">blog</x-ui.select.option>
                    </x-ui.select>
                    <x-ui.error name="og_type" />
                </x-ui.field>

                <x-ui.field>
                    <x-ui.label>{{ __('Twitter Card') }}</x-ui.label>
                    <x-ui.select wire:model="twitter_card">
                        <x-ui.select.option value="summary">summary</x-ui.select.option>
                        <x-ui.select.option value="summary_large_image">summary_large_image</x-ui.select.option>
                    </x-ui.select>
                    <x-ui.error name="twitter_card" />
                </x-ui.field>
            </div>

            <x-ui.field>
                <x-ui.label>{{ __('Twitter Site Handle') }}</x-ui.label>
                <x-ui.input wire:model="twitter_site" placeholder="@yourhandle" />
                <x-ui.error name="twitter_site" />
            </x-ui.field>

            <x-ui.separator :label="__('Analytics')" />

            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.field>
                    <x-ui.label>{{ __('Google Analytics ID') }}</x-ui.label>
                    <x-ui.input wire:model="google_analytics_id" placeholder="G-XXXXXXXXXX" />
                    <x-ui.error name="google_analytics_id" />
                </x-ui.field>

                <x-ui.field>
                    <x-ui.label>{{ __('Google Tag Manager ID') }}</x-ui.label>
                    <x-ui.input wire:model="google_tag_manager_id" placeholder="GTM-XXXXXXX" />
                    <x-ui.error name="google_tag_manager_id" />
                </x-ui.field>
            </div>

            <x-ui.separator :label="__('Indexing')" />

            <x-ui.field>
                <x-ui.checkbox wire:model="index_site" id="index_site">
                    <x-slot name="label">{{ __('Allow search engines to index this site') }}</x-slot>
                </x-ui.checkbox>
                <x-ui.description>{{ __('Disabling this adds a noindex meta tag to all pages.') }}</x-ui.description>
            </x-ui.field>

            <x-ui.separator />

            <div class="flex justify-end">
                <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                    {{ __('Save Changes') }}
                </x-ui.button>
            </div>
        </form>
    </x-admin::settings.layout>
</div>
