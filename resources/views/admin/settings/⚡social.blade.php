<?php

use App\Settings\SocialSettings;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Social Settings'), Layout('layouts::app')] class extends Component {

    #[Validate('nullable|url|max:500')]
    public string $github_url = '';

    #[Validate('nullable|url|max:500')]
    public string $twitter_url = '';

    #[Validate('nullable|url|max:500')]
    public string $linkedin_url = '';

    #[Validate('nullable|url|max:500')]
    public string $instagram_url = '';

    #[Validate('nullable|url|max:500')]
    public string $youtube_url = '';

    #[Validate('nullable|url|max:500')]
    public string $facebook_url = '';

    public function mount(SocialSettings $settings): void
    {
        $this->github_url    = $settings->github_url;
        $this->twitter_url   = $settings->twitter_url;
        $this->linkedin_url  = $settings->linkedin_url;
        $this->instagram_url = $settings->instagram_url;
        $this->youtube_url   = $settings->youtube_url;
        $this->facebook_url  = $settings->facebook_url;
    }

    public function save(SocialSettings $settings): void
    {
        $this->validate();

        $settings->github_url    = $this->github_url;
        $settings->twitter_url   = $this->twitter_url;
        $settings->linkedin_url  = $this->linkedin_url;
        $settings->instagram_url = $this->instagram_url;
        $settings->youtube_url   = $this->youtube_url;
        $settings->facebook_url  = $this->facebook_url;

        $settings->save();

        $this->dispatch('notify', type: 'success', content: __('Social settings saved.'));
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
        :heading="__('Social')"
        :subheading="__('Links to your social media profiles shown in the footer and contact sections.')"
    >
        <form wire:submit="save" class="space-y-5">

            {{-- GitHub --}}
            <x-ui.field>
                <x-ui.label>{{ __('GitHub') }}</x-ui.label>
                <x-ui.input wire:model="github_url" type="url" placeholder="https://github.com/username" />
                <x-ui.error name="github_url" />
            </x-ui.field>

            {{-- Twitter / X --}}
            <x-ui.field>
                <x-ui.label>{{ __('Twitter / X') }}</x-ui.label>
                <x-ui.input wire:model="twitter_url" type="url" placeholder="https://twitter.com/username" />
                <x-ui.error name="twitter_url" />
            </x-ui.field>

            {{-- LinkedIn --}}
            <x-ui.field>
                <x-ui.label>{{ __('LinkedIn') }}</x-ui.label>
                <x-ui.input wire:model="linkedin_url" type="url" placeholder="https://linkedin.com/in/username" />
                <x-ui.error name="linkedin_url" />
            </x-ui.field>

            {{-- Instagram --}}
            <x-ui.field>
                <x-ui.label>{{ __('Instagram') }}</x-ui.label>
                <x-ui.input wire:model="instagram_url" type="url" placeholder="https://instagram.com/username" />
                <x-ui.error name="instagram_url" />
            </x-ui.field>

            {{-- YouTube --}}
            <x-ui.field>
                <x-ui.label>{{ __('YouTube') }}</x-ui.label>
                <x-ui.input wire:model="youtube_url" type="url" placeholder="https://youtube.com/@channel" />
                <x-ui.error name="youtube_url" />
            </x-ui.field>

            {{-- Facebook --}}
            <x-ui.field>
                <x-ui.label>{{ __('Facebook') }}</x-ui.label>
                <x-ui.input wire:model="facebook_url" type="url" placeholder="https://facebook.com/page" />
                <x-ui.error name="facebook_url" />
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
