@aware([
    'disabled' => false,
    'placeholder' => 'select a time',
    'clearable' => false,
    'iconAfter'   => 'chevron-up-down',
])

@props(['iconSize'])

<div
    x-bind:data-open="__isOpen"
    data-slot="select-control"
    x-on:click="handleButtonClick"
    x-bind:data-state-filled="hasValue"
    {{  $attributes->class([
            'col-span-full col-start-1 row-start-1 min-w-0 justify-self-stretch',
            'flex flex-wrap gap-1 items-center data-state-filled:items-start w-full cursor-pointer',
            'min-h-10 pl-1 pr-10',
        ]) }}
>
    {{-- Selected time chips --}}
    <template x-for="chip in selectedItems" x-bind:key="chip.value">
        <span class="inline-flex items-center gap-1 pl-2 pr-1 py-1.5 rounded-md bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-200 text-xs font-medium leading-none shrink-0">
            <span x-text="chip.label" class="tabular-nums"></span>
            <button
                type="button"
                x-on:click.stop="removeChip(chip.value)"
                tabindex="-1"
                aria-label="Remove"
                class="rounded hover:bg-neutral-200 dark:hover:bg-neutral-600 p-0.25 opacity-60 hover:opacity-100 transition-opacity"
            >
                <x-ui.icon name="x-mark" variant="micro" class="size-4" />
            </button>
        </span>
    </template>

    {{-- Placeholder shown only when nothing selected --}}
    <span
        x-show="!hasValue"
        class="text-neutral-400 pl-2 dark:text-neutral-500 text-sm select-none"
    >{{ $placeholder }}</span>
</div>

{{-- the buttons: clear and chevron trigger --}}
<span 
    class="col-start-2 self-start row-start-1 px flex items-center gap-0.5 z-10 rounded hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors mt-1 mr-1"
>
    @if ($clearable)
        <button
            type="button"
            x-show="hasValue"
            x-cloak
            x-on:click.stop="clear"
            tabindex="-1"
            aria-label="Clear all"
        >
            <x-ui.icon name="x-mark" @class($iconSize) />
        </button>
    @endif

    <x-ui.icon
        :name="$iconAfter"
        x-show="!hasValue"
        x-rover:button
        @class($iconSize)
    />
</span>