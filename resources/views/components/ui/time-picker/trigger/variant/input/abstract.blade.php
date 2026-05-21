@aware(['disabled'])

<input
    type="text"
    inputmode="numeric"
    placeholder="--"
    maxLength="2"
    min="0"
    max="59"
    x-on:click.stop="handleInputClick"
    x-on:focus="$el.select()"
    x-on:blur="normalizeOnBlur($el.name === 'hours-handler' ? 'h' : 'm')"
    x-on:paste="handleSegmentPaste($event, $el.name === 'hours-handler' ? 'h' : 'm')"
    {{ $attributes->class("w-6 text-center h-6 bg-transparent p-0 text-white outline-none border-none tabular-nums text-neutral-950 dark:text-neutral-50 placeholder-neutral-400 dark:placeholder-neutral-500 focus:bg-neutral-100 dark:focus:bg-neutral-800 focus:ring-2 dark:ring-neutral-700 rounded transition-colors") }}
    @disabled($disabled)
/>