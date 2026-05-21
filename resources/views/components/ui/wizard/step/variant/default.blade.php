@aware([
    'active' => false,
    'label' => 1,
    'completed' => false,
    'icon' => null,
    'completedIcon' => 'check'
])

@php
    $classes = [
        'relative flex data-[active=true]:border-white/30',
        '[&:last-child_[data-slot=separator-icon]]:hidden',
    ];

@endphp
<div     
    {{ $attributes->class($classes) }} 
    data-slot="wizard-step"

    @if ($active)
        data-active
    @endif

    @if ($completed)
        data-completed
    @endif
>
    <div 
        @class([
            "size-12 self-center ml-4 rtl:mr-4 rounded-full flex justify-center items-center",
            "[[data-slot=wizard-step]:is([data-active],[data-completed])_&]:text-(--color-primary)",
            "[[data-slot=wizard-step]:is([data-completed],[data-active])_&]:border-(--color-primary) border-2 border-neutral-950/10 dark:border-white/5"
        ])
    >
        <div class="[[data-slot=wizard-step][data-completed]_&]:hidden">
            @if (filled($icon))
                <div>
                    {{ $icon }}
                </div>
            @else
                <div>
                    {{ $label }}
                </div>
            @endif
        </div>
        <x-ui.icon :name="$completedIcon" class="text-(--color-primary)! [[data-slot=wizard-step]:not([data-completed])_&]:hidden"/>
    </div>

    <div class="p-4 flex-1 self-center">
        {{ $slot }}
    </div>

    <svg fill="none" data-slot="separator-icon" preserveAspectRatio="none" viewBox="0 0 22 80" aria-hidden="true"
        class="absolute end-0 hidden h-full w-5 text-neutral-200 md:block rtl:rotate-180 dark:text-white/5">
        <path d="M0 -2L20 40L0 82" stroke-linejoin="round" stroke="currentcolor" vector-effect="non-scaling-stroke">
        </path>
    </svg>
</div>