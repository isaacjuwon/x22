<?php

namespace App\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin \Illuminate\Database\Eloquent\Model&HasMedia
 */
trait HasContentMedia
{
    use InteractsWithMedia;

    public const FEATURED_IMAGE_COLLECTION = 'featured_image';

    public const GALLERY_COLLECTION = 'gallery';

    public const SEO_OG_IMAGE_COLLECTION = 'seo_og_image';

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(static::FEATURED_IMAGE_COLLECTION)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
            ->singleFile();

        $this->addMediaCollection(static::GALLERY_COLLECTION)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

        $this->addMediaCollection(static::SEO_OG_IMAGE_COLLECTION)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 240, 240)
            ->nonQueued();

        $this->addMediaConversion('card')
            ->fit(Fit::Crop, 960, 540)
            ->nonQueued();

        $this->addMediaConversion('hero')
            ->fit(Fit::Crop, 1600, 900)
            ->nonQueued();

        $this->addMediaConversion('og')
            ->fit(Fit::Crop, 1200, 630)
            ->nonQueued();
    }

    public function featuredImageMedia(): ?Media
    {
        return $this->getFirstMedia(static::FEATURED_IMAGE_COLLECTION);
    }

    public function seoOgImageMedia(): ?Media
    {
        return $this->getFirstMedia(static::SEO_OG_IMAGE_COLLECTION);
    }

    /**
     * @return Collection<int, Media>
     */
    public function galleryMedia(): Collection
    {
        /** @var Collection<int, Media> $media */
        $media = $this->getMedia(static::GALLERY_COLLECTION);

        return $media;
    }

    public function featuredImageUrl(string $conversion = ''): ?string
    {
        $media = $this->featuredImageMedia();

        if ($media !== null) {
            return $conversion === '' ? $media->getUrl() : $media->getUrl($conversion);
        }

        return $this->legacyImageUrl($this->og_image ?? null);
    }

    public function seoOgImageUrl(string $conversion = 'og'): ?string
    {
        $media = $this->seoOgImageMedia();

        if ($media !== null) {
            return $media->getUrl($conversion);
        }

        $featuredImageUrl = $this->featuredImageUrl($conversion);

        if ($featuredImageUrl !== null) {
            return $featuredImageUrl;
        }

        return $this->legacyImageUrl($this->og_image ?? null);
    }

    protected function legacyImageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        return asset($path);
    }
}
