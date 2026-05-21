@props([
    'count' => null,
])
<div 
    class="p-4 border-b row-start-1 border-neutral-200 dark:border-neutral-800"
>
    <div 
        class="flex gap-4 items-start"
    >   
        <div>
            {{ $slot }}
        </div>

        @if ($count !== null)
            <span class="text-sm ml-auto  text-neutral-500 dark:text-neutral-400">
                {{ $count }}
            </span>
        @endif
    </div>
</div>