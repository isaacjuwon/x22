@aware([
    'empty' => 'No results found'
])

@props([
    'empty' => 'No results found'
])

@php
    $classes=["[:where(&)]:max-h-[300px] grid grid-cols-[auto_1fr_auto] overflow-y-auto overflow-x-hidden"];
@endphp

<ul 
    {{ $attributes->class($classes) }}
    x-rover:options
    role="listbox"
    aria-label="suggestions" 
>
    {{ $slot }}
    {{-- Empty and loading states are <li> elements to maintain valid HTML inside <ul>.
    col-span-full spans all 3 grid columns so they don't break the layout. --}}
    
    <li 
        class="col-span-full [ul:is([data-loading])_&]:hidden"
        x-rover:empty 
        x-cloak
    >
        @if ($empty instanceof \Illuminate\View\ComponentSlot)
            {{ $empty }}
        @else
            <x-ui.text class="h-24 flex items-center justify-center">
                {{ $empty }}
            </x-ui.text>
        @endif
    </li>
</ul>