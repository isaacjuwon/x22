@props(['heading' => ''])
<div 
    data-slot="command-group"
    x-rover:group
    class="contents" 
>
    <h3 
        class="text-xs pl-3 mt-2 text-start col-span-full px-[calc(--spacing(1.5)_+_var(--container-padding))] font-medium text-neutral-500 dark:text-neutral-400 tracking-wide" 
        aria-hidden="true"
    >
        {{ $heading }}
    </h3>
    
    {{ $slot }}
</div>