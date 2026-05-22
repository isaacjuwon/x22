<?php

use Livewire\Component;

new class extends Component {}; ?>

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <x-ui.heading>{{ __('Delete account') }}</x-ui.heading>
        <x-ui.description>{{ __('Delete your account and all of its resources') }}</x-ui.description>
    </div>

    <x-ui.modal.trigger name="confirm-user-deletion">
        <x-ui.button variant="danger" data-test="delete-user-button">
            {{ __('Delete account') }}
        </x-ui.button>
    </x-ui.modal.trigger>

    <livewire:pages::settings.delete-user-modal />
</section>
