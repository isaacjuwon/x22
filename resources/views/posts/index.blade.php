<x-layouts::main title="{{ __('Posts') }}">
    <div class="container mx-auto px-4 py-12">
        <x-ui.heading level="h1" size="xl" class="mb-4">{{ __('Posts') }}</x-ui.heading>

        <livewire:posts.list />
    </div>
</x-layouts::main>
