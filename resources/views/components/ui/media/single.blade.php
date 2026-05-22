@props([
    'label' => null,
    'hint' => null,
    'previewUrl' => null,
    'previewAlt' => null,
    'inputModel' => null,
    'inputName' => null,
    'removeAction' => null,
    'removeLabel' => __('Remove image'),
    'emptyLabel' => __('No image uploaded yet.'),
    'error' => null,
])

<x-ui.field>
    @if ($label)
        <x-ui.label>{{ $label }}</x-ui.label>
    @endif

    <x-ui.card size="2xl" class="space-y-4">
        @if ($previewUrl)
            <div class="overflow-hidden rounded-lg border border-black/10 bg-neutral-950/5 dark:border-white/10 dark:bg-white/5">
                <img src="{{ $previewUrl }}" alt="{{ $previewAlt }}" class="h-56 w-full object-cover">
            </div>
        @else
            <x-ui.empty class="rounded-lg border border-dashed border-black/10 py-10 dark:border-white/10">
                <x-ui.empty.media>
                    <x-ui.icon name="photo" class="size-10 text-neutral-400 dark:text-neutral-500" />
                </x-ui.empty.media>
                <x-ui.empty.contents>
                    <x-ui.text class="text-neutral-500 dark:text-neutral-400">{{ $emptyLabel }}</x-ui.text>
                </x-ui.empty.contents>
            </x-ui.empty>
        @endif

        <div class="space-y-3">
            <input
                @if ($inputModel) wire:model="{{ $inputModel }}" @endif
                type="file"
                name="{{ $inputName }}"
                accept="image/*"
                class="block w-full rounded-lg border border-black/10 bg-white px-3 py-2 text-sm text-neutral-700 file:me-3 file:rounded-md file:border-0 file:bg-neutral-900 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white dark:border-white/10 dark:bg-neutral-900 dark:text-neutral-200 dark:file:bg-white dark:file:text-neutral-950"
            >

            @if ($hint)
                <x-ui.description>{{ $hint }}</x-ui.description>
            @endif

            @if ($previewUrl && $removeAction)
                <div class="flex justify-end">
                    <x-ui.button wire:click="{{ $removeAction }}" variant="ghost" color="red" size="sm" icon="trash">
                        {{ $removeLabel }}
                    </x-ui.button>
                </div>
            @endif
        </div>
    </x-ui.card>

    @if ($error)
        <x-ui.error :name="$error" />
    @endif
</x-ui.field>
