@aware(['contained' => false])


@php
    $classes = [
        'rounded-xl',
        'border border-neutral-800/10 dark:border-white/5'=> $contained,
    ];
@endphp

<div {{ $attributes->class($classes) }} data-slot="wizard-body">
    {{ $slot }}
</div>
