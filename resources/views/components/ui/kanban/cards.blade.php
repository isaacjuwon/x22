<div 

    {{ $attributes->class("p-3 space-y-2 overflow-y-auto") }} 
>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @endif

    @if ($slot->isEmpty() && $empty)
        <div class="flex items-center justify-center py-8 text-center">
            {{ $empty }}
        </div>
    @endif
</div>