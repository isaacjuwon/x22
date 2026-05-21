@props(['aligned' => false])
<div
    {{ 
        $attributes->class([
            'p-4 border-t border-neutral-200 dark:border-neutral-800',
            // 'row-start-3' => $aligned
        ]) 
    }}
>
    {{ $slot }}
</div>
