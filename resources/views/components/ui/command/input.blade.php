
@aware([
    'label',
    'clearable' => false
])

@props([
    'placeholder' => 'Search...',
    'label' => 'command pallete',
    'icon' => 'magnifying-glass',
    'clearable' => false,
])


<div 
    {{ $attributes->class("flex justify-between items-center border-b border-neutral-400/30 dark:border-neutral-700/30 px-3 py-1") }}
>
        <span class="sr-only">{{ $label }}</span>
        
        @if ($icon instanceof \Illuminate\View\ComponentSlot)
            {{ $icon }}
        @else
            <x-ui.icon :name="$icon" :variant="$attributes->get('icon:variant')" /> 
        @endif

        <input 
            data-slot="input"
            placeholder="{{ $placeholder }}"
            class="flex w-full rounded-md bg-transparent min-h-10 border-none focus:outline-none focus:ring-0 py-3 text-sm outline-none placeholder:text-neutral-800 dark:placeholder:text-neutral-100 disabled:cursor-not-allowed disabled:opacity-50"
            type="text"
            x-rover:input 
            autocomplete="off" 
            aria-autocomplete="list" 
            autocorrect="off" 
        />

        @if($clearable)
            <button 
                type="button"
                class="hover:bg-white/5 p-1 rounded-box" 
                x-on:click="$rover.input.reset()"
            >
                <x-ui.icon name="x-mark" variant="mini"/>
            </button>
        @endif
</div>