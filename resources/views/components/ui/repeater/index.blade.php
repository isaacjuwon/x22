@props([
    'header' => null,
    'actions' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-4']) }}>
    @if($header)
        <div {{ $header->attributes }}>
            {{ $header }}
        </div>
    @endif

    <div 
        {{ $slot->attributes }}
        class="space-y-4" 
    >
        {{ $slot }}
    </div>

    @if (filled($actions))
        <div class="flex items-center justify-center">
            {{ $actions }}
        </div>
    @endif
</div>