@props([
    'label' => 'command palette'
])

@php
    $classes = [
        "flex flex-col w-full border dark:border-neutral-700/30 border-neutral-400/30 dark:bg-neutral-900 overflow-hidden bg-neutral-50 dark:text-neutral-50 text-neutral-900",
        // radius calculations...
        "rounded-(--command-radius) [--container-padding:--spacing(1)] [--command-radius:calc(1.5*var(--radius-box))]"
    ];   
@endphp

<div 
    x-rover
    x-init="
        let inputManager = $rover.input;

        inputManager.enableDefaultInputHandlers();

        inputManager.on('click', () => $rover.activateFirst());

        inputManager.on('keydown', (event, activeItem) => {
            if(event.key === 'Enter'){
                $rover.getOptionElByValue(activeItem)?.click();
            }

            {{-- if we're inside a modal handle escape key  --}}
            if(event.key==='Escape'){
                $data.close?.()
            }
        });

        $rover.options.enableDefaultOptionsHandlers();
    "
    {{ $attributes->class($classes) }} 
>
    {{ $slot }}
</div>