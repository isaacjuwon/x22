<?php

use App\Settings\GeneralSettings;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('General Settings'), Layout('layouts::app')] class extends Component {

    #[Validate('required|string|max:255')]
    public string $site_name = '';

    #[Validate('nullable|string|max:500')]
    public string $site_description = '';

    #[Validate('nullable|email|max:255')]
    public string $site_email = '';

    #[Validate('nullable|string|max:50')]
    public string $site_phone = '';

    #[Validate('nullable|string|max:500')]
    public string $site_address = '';

    #[Validate('required|string|max:255')]
    public string $hero_title = '';

    #[Validate('nullable|string|max:500')]
    public string $hero_subtitle = '';

    #[Validate('boolean')]
    public bool $show_posts_section = true;

    #[Validate('boolean')]
    public bool $show_projects_section = true;

    #[Validate('boolean')]
    public bool $show_testimonials_section = true;

    public function mount(GeneralSettings $settings): void
    {
        $this->site_name               = $settings->site_name;
        $this->site_description        = $settings->site_description;
        $this->site_email              = $settings->site_email;
        $this->site_phone              = $settings->site_phone;
        $this->site_address            = $settings->site_address;
        $this->hero_title              = $settings->hero_title;
        $this->hero_subtitle           = $settings->hero_subtitle;
        $this->show_posts_section      = $settings->show_posts_section;
        $this->show_projects_section   = $settings->show_projects_section;
        $this->show_testimonials_section = $settings->show_testimonials_section;
    }

    public function save(GeneralSettings $settings): void
    {
        $this->validate();

        $settings->site_name               = $this->site_name;
        $settings->site_description        = $this->site_description;
        $settings->site_email              = $this->site_email;
        $settings->site_phone              = $this->site_phone;
        $settings->site_address            = $this->site_address;
        $settings->hero_title              = $this->hero_title;
        $settings->hero_subtitle           = $this->hero_subtitle;
        $settings->show_posts_section      = $this->show_posts_section;
        $settings->show_projects_section   = $this->show_projects_section;
        $settings->show_testimonials_section = $this->show_testimonials_section;

        $settings->save();

        $this->dispatch('notify', type: 'success', content: __('General settings saved.'));
    }
};
?>

<div class="mx-auto max-w-4xl space-y-6 p-6">

    {{-- Page header --}}
    <div>
        <x-ui.heading level="h1" size="lg">{{ __('Site Settings') }}</x-ui.heading>
        <x-ui.text class="mt-1 text-neutral-500 dark:text-neutral-400">
            {{ __('Manage your site configuration.') }}
        </x-ui.text>
    </div>

    <x-ui.separator />

    <x-admin::settings.layout
        :heading="__('General')"
        :subheading="__('Basic site identity and homepage section visibility.')"
    >
        <form wire:submit="save" class="space-y-6">

            <x-ui.separator :label="__('Site Identity')" />

            {{-- Site name --}}
            <x-ui.field required>
                <x-ui.label required>{{ __('Site Name') }}</x-ui.label>
                <x-ui.input wire:model="site_name" placeholder="{{ __('My Portfolio') }}" />
                <x-ui.error name="site_name" />
            </x-ui.field>

            {{-- Site description --}}
            <x-ui.field>
                <x-ui.label>{{ __('Site Description') }}</x-ui.label>
                <x-ui.textarea wire:model="site_description" rows="3"
                    placeholder="{{ __('A short description of your site...') }}" />
                <x-ui.error name="site_description" />
            </x-ui.field>

            <div class="grid gap-5 sm:grid-cols-2">
                {{-- Email --}}
                <x-ui.field>
                    <x-ui.label>{{ __('Contact Email') }}</x-ui.label>
                    <x-ui.input wire:model="site_email" type="email" placeholder="hello@example.com" />
                    <x-ui.error name="site_email" />
                </x-ui.field>

                {{-- Phone --}}
                <x-ui.field>
                    <x-ui.label>{{ __('Phone') }}</x-ui.label>
                    <x-ui.input wire:model="site_phone" placeholder="+1 234 567 890" />
                    <x-ui.error name="site_phone" />
                </x-ui.field>
            </div>

            {{-- Address --}}
            <x-ui.field>
                <x-ui.label>{{ __('Address') }}</x-ui.label>
                <x-ui.textarea wire:model="site_address" rows="2" placeholder="{{ __('123 Main St, City, Country') }}" />
                <x-ui.error name="site_address" />
            </x-ui.field>

            <x-ui.separator :label="__('Hero Section')" />

            {{-- Hero title --}}
            <x-ui.field required>
                <x-ui.label required>{{ __('Hero Title') }}</x-ui.label>
                <x-ui.input wire:model="hero_title" placeholder="{{ __('Welcome to my portfolio') }}" />
                <x-ui.error name="hero_title" />
            </x-ui.field>

            {{-- Hero subtitle --}}
            <x-ui.field>
                <x-ui.label>{{ __('Hero Subtitle') }}</x-ui.label>
                <x-ui.textarea wire:model="hero_subtitle" rows="2"
                    placeholder="{{ __('A short tagline shown below the title...') }}" />
                <x-ui.error name="hero_subtitle" />
            </x-ui.field>

            <x-ui.separator :label="__('Homepage Sections')" />

            {{-- Section toggles --}}
            <div class="space-y-4">
                <x-ui.field>
                    <x-ui.checkbox wire:model="show_posts_section" id="show_posts_section">
                        <x-slot name="label">{{ __('Show Posts Section') }}</x-slot>
                    </x-ui.checkbox>
                </x-ui.field>

                <x-ui.field>
                    <x-ui.checkbox wire:model="show_projects_section" id="show_projects_section">
                        <x-slot name="label">{{ __('Show Projects Section') }}</x-slot>
                    </x-ui.checkbox>
                </x-ui.field>

                <x-ui.field>
                    <x-ui.checkbox wire:model="show_testimonials_section" id="show_testimonials_section">
                        <x-slot name="label">{{ __('Show Testimonials Section') }}</x-slot>
                    </x-ui.checkbox>
                </x-ui.field>
            </div>

            <x-ui.separator />

            <div class="flex justify-end">
                <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                    {{ __('Save Changes') }}
                </x-ui.button>
            </div>

        </form>
    </x-admin::settings.layout>
</div>
