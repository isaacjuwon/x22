<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('social', function ($blueprint): void {
            $blueprint->add('github_url', '');
            $blueprint->add('twitter_url', '');
            $blueprint->add('linkedin_url', '');
            $blueprint->add('instagram_url', '');
            $blueprint->add('youtube_url', '');
            $blueprint->add('facebook_url', '');
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('social', function ($blueprint): void {
            $blueprint->delete('github_url');
            $blueprint->delete('twitter_url');
            $blueprint->delete('linkedin_url');
            $blueprint->delete('instagram_url');
            $blueprint->delete('youtube_url');
            $blueprint->delete('facebook_url');
        });
    }
};
