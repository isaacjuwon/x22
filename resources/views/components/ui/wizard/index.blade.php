@props([
    'contained' => false,
    'variant' => 'default',
])

@php
    $classes = [
        'flex flex-col w-full',
        'gap-4' => !$contained,
    ];
@endphp

<div
    {{ $attributes->class($classes) }} 
>
    {{ $slot }}
</div>