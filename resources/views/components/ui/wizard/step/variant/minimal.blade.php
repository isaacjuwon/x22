@aware([
    'active' => false,
    'label' => 1,
    'completed' => false,
    'icon' => null,
    'completedIcon' => 'check',
])

@php
    $classes = [
        'relative data-[active=true]:border-white/30',
        '[&:last-child_[data-slot=separator-icon]]:hidden',
    ];
@endphp

<div 
    {{ $attributes->class($classes) }} 
    data-slot="wizard-step" 
    @if ($active) data-active @endif
    @if ($completed) data-completed @endif
>
    <div class="flex">
        <div
            @class([
                "size-12 shrink-0 self-center flex justify-center items-center rounded-full",
                "[[data-slot=wizard-step]:is([data-active],[data-completed])_&]:border-(--color-primary)", // styles when this step is active or completed
                "[[data-slot=wizard-step]:is([data-active],[data-completed])_&]:text-(--color-primary)", // styles when this step  is completed
                "border-2 border-neutral-950/10 dark:border-white/5"
            ])
        >
            <div class="[[data-slot=wizard-step][data-completed]_&]:hidden">
                @if (filled($icon))
                    <x-ui.icon :name="$icon" class="text-(--color-primary)!"/>
                @else
                    <div>
                        {{ $label }}
                    </div>
                @endif
            </div>
            <x-ui.icon :name="$completedIcon" class="text-(--color-primary)! [[data-slot=wizard-step]:not([data-completed])_&]:hidden" />
        </div>
        <div
            @class([
                "w-full grow h-[2px] [:where(&)]:bg-white/5 self-center [[data-slot=wizard-step]:last-child_&]:hidden",
                "bg-neutral-200 dark:bg-white/5",
                "[[data-slot=wizard-step]:is([data-completed])_&]:bg-(--color-primary)",
            ])  
            data-slot="connector" 
        ></div>
    </div>

    <div class="flex-1 mt-4 self-center">
        {{ $slot }}
    </div>
</div>
