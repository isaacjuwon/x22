@aware([
    'disabled' => false,
    'iconAfter'=> 'chevron-up-down',
    'icon'     => 'clock',
    'openPanel'=> true,
    'clearable'=> false
])

<div
    class="contents"
    x-ref="inputTrigger"
    x-data="inputTimePickerComponent({ dataStack: Alpine.$data($el) })" 
>
    <div
        x-bind:data-open="__isOpen"
        data-slot="select-control"
        x-on:click="handleInputClick"
        {{ $attributes->class([
            'col-span-full col-start-1 row-start-1 justify-self-stretch',
            'flex items-center gap-0.5 pl-2 pr-1 cursor-text',
        ]) }}
    >
        {{-- Hours segment --}}
        <x-ui.time-picker.trigger.variant.input.abstract  
            x-ref="timeInputH"
            x-on:input.stop="handleHour($el.value)"
            x-on:keydown="handleSegmentKey($event, 'h')"
            x-bind:value="__hours"
            name="hours-handler"
        />

        <span class="text-neutral-400 dark:text-neutral-500 font-bold select-none leading-none">:</span>

        {{-- Minutes segment --}}
        <x-ui.time-picker.trigger.variant.input.abstract  
            x-ref="timeInputM"
            x-on:input.stop="handleMinute($el.value)"
            x-on:keydown="handleSegmentKey($event, 'm')"
            x-bind:value="__minutes"
            name="minutes-handler"
        />

        {{-- AM/PM toggle --}}
        <button
            type="button"
            x-show="__is12Hour"
            x-ref="amPmToggler"
            x-cloak
            x-on:click.stop="toggleAmPm()"
            x-on:keydown.up.prevent="toggleAmPm()"
            x-on:keydown.left.prevent="focusAndSelectEl($refs.timeInputM)"
            x-on:keydown.down.prevent="toggleAmPm()"
            x-bind:aria-pressed="__ampm === 'PM'"
            aria-label="Toggle AM/PM"
            class="ml-0.5 px-1 py-0.5 focus:ring-2 dark:ring-neutral-700 rounded text-xs font-medium text-neutral-500 dark:text-neutral-400 bg-neutral-100 hover:bg-neutral-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors select-none"
        >
            <span x-text="__ampm"></span>
        </button>
    </div>

    {{-- Toggle button --}}
    <button
        type="button"
        x-on:click.stop="handleButtonClick"
        @if($clearable) x-show="!hasValue" @endif
        x-cloak
        tabindex="-1"
        aria-label="Toggle time picker"
        @class([
            "col-start-2 inline-flex row-start-1 self-start flex items-center z-10 rounded transition-colors mt-1 mr-1",
            "hover:bg-neutral-100 dark:hover:bg-neutral-800" => $openPanel
        ])
    >
        <x-ui.icon :name="$icon"  @class($iconSize) variant="solid"/>
    </button>

    @if ($clearable)
        <button
            type="button"
            x-on:click.stop="clearInput"
            tabindex="-1"
            x-show="hasValue"
            x-cloak
            aria-label="Clear time picker"
            class="col-start-2 inline-flex row-start-1 self-start flex items-center z-10 rounded hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors mt-1 mr-1"
        >
            <x-ui.icon name="x-mark"  @class($iconSize) variant="solid"/>
        </button>  
    @endif
</div>