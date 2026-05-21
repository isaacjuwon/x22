@aware(['size'])

@props([
    'id' => null,
    'top' => null,
    'bottom' => null,
    'handle' => null,
    'size' => 'md',
])

@php
    $classes = [
        'group relative',
        'bg-neutral-800/[3%] dark:bg-white/[3%]',
        'border border-neutral-800/5 dark:border-white/5',
        'rounded-lg hover:shadow-sm transition-shadow',
    ];
@endphp

<div {{ $attributes->class($classes) }} data-slot="kanban-card">
    @if ($handle)
        <div class="absolute top-0 left-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
            {{ $handle }}
        </div>
    @endif

    <div @class([
        'flex flex-col gap-1',
        match ($size) {
            // add more variants
            'md' => 'px-4 py-3',
            'sm' => 'px-3 py-2',
            'xs' => 'px-2 py-1.5',
            default => 'px-4 py-3',
        },
    ])>
        @if ($top)
            <div {{ $top?->attributes }}>
                {{ $top }}
            </div>
        @endif
        <div>
            {{ $slot }}
        </div>
        @if ($bottom)
            <div {{ $bottom?->attributes }}>
                {{ $bottom }}
            </div>
        @endif
    </div>
</div>
