@aware([
    'label' => null,
])

<div
    x-ref="panel"
    x-show="__isOpen"
    x-cloak
    x-anchor="$refs.trigger"
    x-on:click.away="handleClickAway($event.target)"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-100 pointer-events-none"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    {{ $attributes->class([
        'absolute z-50 w-full mt-1',
        '[:where(&)]:min-w-52 [:where(&)]:max-w-52',
        'bg-white dark:bg-neutral-800',
        'border border-neutral-200 dark:border-neutral-700',
        'rounded-(--timepicker-round) shadow-lg',
        'py-(--timepicker-padding)',
    ]) }}
>
    <ul
        x-ref="panelList"
        x-rover:options
        tabindex="0"
        x-bind:id="$id('timepicker-options')"
        x-bind:aria-multiselectable="isMultiple ? 'true' : 'false'"
        x-bind:aria-label="@js($label ?? 'Time options')"
        role="listbox"
        class="grid grid-cols-[auto_1fr_auto] gap-y-0.5 focus:outline-transparent overflow-y-auto px-(--timepicker-padding) max-h-60 overscroll-contain"
    >
       
        <template x-for="slot of __slots" x-bind:key="slot.value">
            <x-ui.time-picker.item />
        </template>
    </ul>

    <p
        x-show="__slots.length === 0"
        class="px-3 py-4 text-sm text-center text-neutral-400 dark:text-neutral-500"
    >No times available</p>
</div>