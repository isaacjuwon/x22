@props([
    'deleteHandler' => null,
    'duplicateHandler' => null,
    'footer'=>null
])

<div 
    {{ $attributes->class("relative shadow-sm flex flex-col space-x-4 rounded-xl bg-white [--item-padding:--spacing(4)] p-(--item-padding) transition-all dark:bg-white/2 ") }}
>
    <div 
        class="flex items-center w-full justify-between"
    >
        <div class="ml-auto flex items-center">
            @if(filled($duplicateHandler))
                <x-ui.button 
                    wire:click="{!! $duplicateHandler !!}" 
                    wire:target="{!! $duplicateHandler !!}" 
                    wire:loading
                    size="sm" 
                    variant="soft" 
                    icon="document-duplicate" 
                />
            @endif
            
            @if(filled($deleteHandler))
                <x-ui.button 
                    wire:click="{!! $deleteHandler !!}"
                    wire:target="{!! $deleteHandler !!}"
                    wire:loading
                    size="sm" 
                    variant="soft" 
                    icon="trash" 
                    class="[&_[data-slot=right-icon]]:text-red-500/85 [&_[data-slot=right-icon]]:hover:text-red-500"
                />
            @endif
        </div>
    </div>
        
    <div class="w-full">
        {{ $slot }}
    </div>

    @if (filled($footer))
        <div {{ $footer->attributes->class("flex items-start -mx-(--item-padding)") }} >
            {{ $footer }}
        </div>
    @endif
</div>
