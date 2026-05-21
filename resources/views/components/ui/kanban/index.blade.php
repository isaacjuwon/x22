@props([
    'size' => 'md', // sm, md, lg
])

@php
    $classes = [
        'grid grid-flow-col auto-cols-auto relative w-fit',
        'grid-rows-[auto_1fr_auto]',
        'overflow-x-auto pb-1 space-x-4',
        '[:where(&)]:[--column-width:20rem]',
    ];
@endphp

<div
    {{ $attributes->class($classes) }} 
    data-slot="kanban-board"
>
    {{ $slot }}
</div>
