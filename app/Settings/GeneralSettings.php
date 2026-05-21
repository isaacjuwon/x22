<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    public string $site_description;

    public string $site_email;

    public string $site_phone;

    public string $site_address;

    public string $hero_title;

    public string $hero_subtitle;

    public bool $show_posts_section;

    public bool $show_projects_section;

    public bool $show_testimonials_section;

    public static function group(): string
    {
        return 'general';
    }
}
