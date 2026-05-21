<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('general', function ($blueprint): void {
            $blueprint->add('site_name', config('app.name', 'My Portfolio'));
            $blueprint->add('site_description', 'Building great products and sharing what we learn along the way.');
            $blueprint->add('site_email', '');
            $blueprint->add('site_phone', '');
            $blueprint->add('site_address', '');
            $blueprint->add('hero_title', config('app.name', 'My Portfolio'));
            $blueprint->add('hero_subtitle', 'We build great products, work with great clients, and share what we learn along the way.');
            $blueprint->add('show_posts_section', true);
            $blueprint->add('show_projects_section', true);
            $blueprint->add('show_testimonials_section', true);
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('general', function ($blueprint): void {
            $blueprint->delete('site_name');
            $blueprint->delete('site_description');
            $blueprint->delete('site_email');
            $blueprint->delete('site_phone');
            $blueprint->delete('site_address');
            $blueprint->delete('hero_title');
            $blueprint->delete('hero_subtitle');
            $blueprint->delete('show_posts_section');
            $blueprint->delete('show_projects_section');
            $blueprint->delete('show_testimonials_section');
        });
    }
};
