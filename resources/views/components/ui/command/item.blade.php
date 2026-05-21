@props([
    'disabled' => false,
    'icon' => null,
    'kbd' => null, 
])

@php
    $classes = [
        "relative m-(--container-padding) cursor-default gap-2 grid grid-cols-subgrid col-span-2 items-center px-2 py-1.5 h-10  font-medium text-sm
        outline-none",
        // dynamic roundness calculations...
        'rounded-[calc(var(--command-radius)-var(--container-padding))] scroll-my-(--container-padding)', 
        // special states...
        "dark:data-active:bg-white/5 data-active:bg-neutral-900/5",
        // disabled state
        '[&[aria-disabled=true]]:pointer-events-none [&[aria-disabled=true]]:opacity-50',
    ];
@endphp

<div 
    {{ $attributes->class($classes) }}
    x-rover:option
    @if ($disabled)
        disabled aria-disabled="true"
    @endif
    value="{{ $slot }}"
    data-slot="command-item"
>
    @if($icon)
        <x-ui.icon :name="$icon" class="inline-flex shrink-0 mr-2"/>
    @endif

    <span class="col-start-2 whitespace-nowrap flex items-center justify-between gap-4_">
        <span class="flex-1">{{ $slot }}</span>
        
        @if(filled($kbd))
            <x-ui.kbd>
                {{ $kbd }}
            </x-ui.kbd>
        @endif
    </span>
</div>