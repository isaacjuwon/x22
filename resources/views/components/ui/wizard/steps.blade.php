@aware(['variant' => 'default'])

@props(['color' => null])

@php
    // Override theme variables based on the provided color
    $colors = match ($color) {
        'slate' => '[--color-primary:var(--color-slate-800)] dark:[--color-primary:var(--color-slate-400)]',
        'neutral' => '[--color-primary:var(--color-neutral-800)] dark:[--color-primary:var(--color-neutral-400)]',
        'zinc' => '[--color-primary:var(--color-zinc-800)] dark:[--color-primary:var(--color-zinc-400)]',
        'stone' => '[--color-primary:var(--color-stone-800)] dark:[--color-primary:var(--color-stone-400)]',
        'red' => '[--color-primary:var(--color-red-600)] dark:[--color-primary:var(--color-red-500)]',
        'orange' => '[--color-primary:var(--color-orange-600)] dark:[--color-primary:var(--color-orange-400)]',
        'amber' => '[--color-primary:var(--color-amber-600)] dark:[--color-primary:var(--color-amber-400)]',
        'yellow' => '[--color-primary:var(--color-yellow-600)] dark:[--color-primary:var(--color-yellow-400)]',
        'lime' => '[--color-primary:var(--color-lime-600)] dark:[--color-primary:var(--color-lime-400)]',
        'green' => '[--color-primary:var(--color-green-600)] dark:[--color-primary:var(--color-green-400)]',
        'emerald' => '[--color-primary:var(--color-emerald-600)] dark:[--color-primary:var(--color-emerald-400)]',
        'teal' => '[--color-primary:var(--color-teal-600)] dark:[--color-primary:var(--color-teal-400)]',
        'cyan' => '[--color-primary:var(--color-cyan-600)] dark:[--color-primary:var(--color-cyan-400)]',
        'sky' => '[--color-primary:var(--color-sky-600)] dark:[--color-primary:var(--color-sky-400)]',
        'blue' => '[--color-primary:var(--color-blue-600)] dark:[--color-primary:var(--color-blue-400)]',
        'indigo' => '[--color-primary:var(--color-indigo-600)] dark:[--color-primary:var(--color-indigo-400)]',
        'violet' => '[--color-primary:var(--color-violet-600)] dark:[--color-primary:var(--color-violet-400)]',
        'purple' => '[--color-primary:var(--color-purple-600)] dark:[--color-primary:var(--color-purple-400)]',
        'fuchsia' => '[--color-primary:var(--color-fuchsia-600)] dark:[--color-primary:var(--color-fuchsia-400)]',
        'pink' => '[--color-primary:var(--color-pink-600)] dark:[--color-primary:var(--color-pink-400)]',
        'rose' => '[--color-primary:var(--color-rose-600)] dark:[--color-primary:var(--color-rose-400)]',
        default => '',
    };

    $classes = [
        'grid grid-cols-[repeat(auto-fit,minmax(0,1fr))] rounded-box ',
        'border border-neutral-950/10 dark:border-white/5' => $variant !== 'minimal',
        ' mx-4' => $variant === 'minimal',
        $colors => filled($color), // Apply color variables only if color is provided
    ];
@endphp

<div {{ $attributes->class($classes) }} data-slot="wizard-steps">
    {{ $slot }}
</div>
