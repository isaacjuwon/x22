<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('seo', function ($blueprint): void {
            $blueprint->add('meta_title', config('app.name', 'My Portfolio'));
            $blueprint->add('meta_description', 'Building great products and sharing what we learn along the way.');
            $blueprint->add('meta_keywords', '');
            $blueprint->add('og_image', '');
            $blueprint->add('og_type', 'website');
            $blueprint->add('twitter_card', 'summary_large_image');
            $blueprint->add('twitter_site', '');
            $blueprint->add('google_analytics_id', '');
            $blueprint->add('google_tag_manager_id', '');
            $blueprint->add('index_site', true);
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('seo', function ($blueprint): void {
            $blueprint->delete('meta_title');
            $blueprint->delete('meta_description');
            $blueprint->delete('meta_keywords');
            $blueprint->delete('og_image');
            $blueprint->delete('og_type');
            $blueprint->delete('twitter_card');
            $blueprint->delete('twitter_site');
            $blueprint->delete('google_analytics_id');
            $blueprint->delete('google_tag_manager_id');
            $blueprint->delete('index_site');
        });
    }
};
