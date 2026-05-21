@aware(['size'])

@props([
    'id' => null,
    'count' => null,
])

@php
    $classes = [
        'grid grid-rows-subgrid row-span-full',
        'w-(--column-width)',
        'bg-white dark:bg-neutral-900',
        'rounded-lg border border-neutral-200 dark:border-neutral-800',
    ];
@endphp

<div
    {{ $attributes->class($classes) }}
    data-slot="kanban-column"
>
    {{ $slot }}
</div>
