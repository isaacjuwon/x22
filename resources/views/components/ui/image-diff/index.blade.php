@props([
    'aspectRatio' => 1,
    'vertical' => false
])

<div
    {{ $attributes->class('grid items-center min-h-full isolate relative overflow-hidden rounded-box ') }}
    x-data="{ position: 50 }"
    style="--covered-area: {{ (int)$attributes->get('initial-position') }}%"
    x-modelable="position"
    x-init="
        position = parseInt($el.getAttribute('initial-position')) || 50
    "
    x-bind:style="`--covered-area: ${position}%`"
>
    <div  
        style="aspect-ratio:{{ $aspectRatio }}" 
    >
        {{ $slot }}
    </div>
    
   <input
        type="range"
        min="0"
        max="100"
        x-model="position"
        step="1"
        aria-label="Comparison position"
        @if ($vertical)
            x-on:keydown.up.prevent="
                position = Math.max(0, position - 1)
            "
            x-on:keydown.down.prevent="
                position = Math.min(100, position + 1);
            "
            
            {{--
                //  ensures the input 'width' matches the container 'height' when rotated 
                //  bacause when the ratio isn't squared isn't saty exact  to interact wiith the mouse
            --}}
            
            style="width: calc(100% / {{ $aspectRatio }}); height: 100%;"
        @endif

        

        @class([
            'absolute inset-0 cursor-pointer opacity-0 size-full z-30',
            'rotate-90' =>$vertical
        ])
    />


    {{-- the vertical line in the center --}}
    <div 
        {{-- this a hack anyway to prevents layout shifting while alpine get initialized --}}
        x-show="true"
        x-transition
        x-cloak
        @class([
            'bg-neutral-50 shadow-xs shadow-neutral-900/50 z-20 absolute pointer-events-none ',
            'top-0 bottom-0 left-(--covered-area) -translate-x-1/2 h-full w-px' => !$vertical,
            'left-0 right-0 top-(--covered-area) -translate-y-1/2 w-full h-px' => $vertical,
        ])
        aria-hidden="true"
    ></div>

    {{-- the button in the center --}}
    <button 
        {{-- a hack explained above --}}
        x-show="true"
        x-transition
        x-cloak
        @class([
            'absolute bg-white/20 shadow-sm shadow-neutral-900/50 z-20 outline-1 outline-neutral-50 -translate-1/2 p-2 rounded-full pointer-events-none',
            'top-1/2 left-(--covered-area)' => !$vertical,
            'left-1/2 top-(--covered-area)' => $vertical
        ])
    >
        <x-ui.icon 
            name="chevron-up-down"
            @class([
                '!text-neutral-50',
                'rotate-90' => !$vertical
            ])
        />  
    </button>
</div>