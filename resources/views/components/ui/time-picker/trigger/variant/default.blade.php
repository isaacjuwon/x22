@aware([
    'disabled'    => false,
    'icon'        => 'clock',
    'placeholder' => 'select a time',
    'clearable'   => false,
    'iconAfter'   => 'chevron-up-down',
])

@props([
    'iconSize',    
])


<x-ui.icon
    :name="$icon"
    variant="solid"
    class="col-start-1 ml-1 row-start-1 z-10 !size-[1.1rem] opacity-80 pointer-events-none place-self-center justify-self-center"
/>

<button
    type="button"
    role="combobox"
    aria-haspopup="listbox"
    x-bind:aria-expanded="__isOpen"
    x-bind:aria-controls="$id('timepicker-options')"
    x-bind:data-open="__isOpen"
    x-on:click.stop="handleButtonClick"
    data-slot="select-control"
    {{ $attributes->class([
        'col-span-full col-start-1 row-start-1 justify-self-stretch',
        'flex items-center pl-8 pr-1 cursor-pointer whitespace-nowrap',
        ])
    }}
    @disabled($disabled)
>
    <span
        x-ref="triggerValue"
        x-bind:data-state-filled="hasValue"
        class="flex-1 truncate text-start [&:not([data-state-filled])]:text-neutral-400 [&:not([data-state-filled])]:dark:text-neutral-500 data-state-filled:text-neutral-950 data-state-filled:dark:text-neutral-50"
    >{{ $placeholder }}</span>
</button>

<span 
    class="col-start-3 row-start-1 self-start flex items-center gap-0.5 z-10 rounded hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors mt-1 mr-1"
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
        name="{{ $iconAfter }}"
        x-show="!hasValue"
        x-rover:button
        @class($iconSize)
    />
</span>