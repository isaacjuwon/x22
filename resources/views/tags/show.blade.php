<x-layouts::main title="{{ $tag->name }} {{ __('Posts') }}">
    <div class="container mx-auto px-4 py-12">
        <div class="mb-4">
            <x-ui.link href="{{ route('posts.index') }}" wire:navigate class="text-sm text-neutral-600 dark:text-neutral-500 hover:text-green-600 dark:hover:text-green-400">
                &larr; {{ __('Back to all posts') }}
            </x-ui.link>
        </div>

        <div class="mb-8">
            <x-ui.heading level="h1" size="2xl" class="mb-2">
                {{ __('Tag:') }} <span class="text-green-600 dark:text-green-400">{{ $tag->name }}</span>
            </x-ui.heading>
            <x-ui.text class="text-neutral-600 dark:text-neutral-400">
                {{ __('Browsing all posts associated with the') }} "{{ $tag->name }}" {{ __('tag.') }}
            </x-ui.text>
        </div>

        <livewire:posts.list :activeTag="$tag->slug" />
    </div>
</x-layouts::main>
