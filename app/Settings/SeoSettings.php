<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SeoSettings extends Settings
{
    public string $meta_title;

    public string $meta_description;

    public string $meta_keywords;

    public string $og_image;

    public string $og_type;

    public string $twitter_card;

    public string $twitter_site;

    public string $google_analytics_id;

    public string $google_tag_manager_id;

    public bool $index_site;

    public static function group(): string
    {
        return 'seo';
    }
}
