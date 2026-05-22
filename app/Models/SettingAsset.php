<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable(['key'])]
class SettingAsset extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const SEO_OG_IMAGE_KEY = 'seo_og_image';

    public const FILE_COLLECTION = 'file';

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(static::FILE_COLLECTION)
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('og')
            ->fit(Fit::Crop, 1200, 630)
            ->nonQueued();
    }

    public static function forKey(string $key): self
    {
        /** @var self */
        return static::query()->firstOrCreate(['key' => $key]);
    }

    public static function findForKey(string $key): ?self
    {
        /** @var ?self */
        return static::query()->where('key', $key)->first();
    }

    public function fileUrl(string $conversion = ''): ?string
    {
        $media = $this->getFirstMedia(static::FILE_COLLECTION);

        if ($media === null) {
            return null;
        }

        return $conversion === '' ? $media->getUrl() : $media->getUrl($conversion);
    }
}
