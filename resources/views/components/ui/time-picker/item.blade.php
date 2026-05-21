@aware([
    'checkIcon' => 'check',
    'multiple'  => false,
    'variant'   => 'default' // on multiple mode we have `default` and `checkbox` variants 
])

@php
    $classes = [
        'col-span-full grid grid-cols-subgrid items-center gap-x-2',
        'rounded-[calc(var(--timepicker-round)-var(--timepicker-padding))]',
        'py-2 px-3 text-sm',
        'w-full cursor-pointer select-none',
        'data-active:bg-neutral-800/5 dark:data-active:bg-white/5',
        // Disabled
        '[&[aria-disabled=true]]:pointer-events-none [&[aria-disabled=true]]:opacity-40',
    ];

    $isCheckboxVariant = $variant === 'checkbox';
@endphp

<li
    data-slot="option"
    role="option"
    x-rover:option
    x-bind:value="slot.value"
    x-bind:aria-selected="isSelected(slot.value)"
    x-bind:data-selected="isSelected(slot.value)"
    x-bind:disabled="slot.disabled"
    x-bind:data-special="slot.keys.length ? slot.keys.join(' ') : false"
    x-on:click.stop="handleSelect(slot.value, slot.disabled)"
    {{ $attributes->class($classes) }}
>
    <span class="col-start-1 flex items-center justify-center w-4 h-4 shrink-0">

        @if (!($multiple && $isCheckboxVariant))    
            <x-ui.icon
                :name="$checkIcon"
                x-show="isSelected(slot.value)"
                x-cloak
                class="size-4 text-neutral-700 dark:text-neutral-200"
            />
        @else
            <span
                x-show="isMultiple"
                x-cloak
                x-bind:data-selected="isSelected(slot.value)"
                class="inline-flex items-center justify-center size-4.5 rounded-sm border transition-colors shrink-0 border-neutral-300 dark:border-neutral-600 dark:bg-white/5 bg-neutral-900/5"
            >
                <x-ui.icon x-show="isSelected(slot.value)" x-cloak name="check" variant="micro"/>
            </span>
        @endif
    </span>

    <span
        x-text="slot.label"
        x-bind:data-disabled="$rover.isDisabled(slot.value)"
        class="col-start-2 text-start tabular-nums whitespace-nowrap leading-none text-neutral-800 dark:text-neutral-200 data-disabled:line-through data-disabled:text-neutral-400 dark:data-disabled:text-neutral-500"
    ></span>

    <span
        x-show="slot.disabled"
        x-cloak
        class="col-start-3 ml-auto pl-2 text-xs text-neutral-400 dark:text-neutral-500 shrink-0"
    >Unavailable</span>

</li>