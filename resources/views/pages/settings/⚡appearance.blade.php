<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Appearance settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-ui.heading class="sr-only">{{ __('Appearance settings') }}</x-ui.heading>

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <x-ui.radio.group x-data variant="segmented" x-model="$x-ui..appearance">
            <x-ui.radio value="light" icon="sun">{{ __('Light') }}</x-ui.radio>
            <x-ui.radio value="dark" icon="moon">{{ __('Dark') }}</x-ui.radio>
            <x-ui.radio value="system" icon="computer-desktop">{{ __('System') }}</x-ui.radio>
        </x-ui.radio.group>
    </x-pages::settings.layout>
</section>
