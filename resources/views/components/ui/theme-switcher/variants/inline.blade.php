@aware(['darkIcon'=>'moon','lightIcon'=>'sun','iconVariant' => "outline"])

<div class="flex items-center transition-all duration-300">
    <x-ui.button 
        :icon="$lightIcon" 
        :$iconVariant
        variant="none"
        class="h-9 w-9 p-0 text-neutral-500 hover:text-primary transition-colors flex items-center justify-center"
        x-show="$theme.isResolvedToLight"
        x-on:click="$theme.toggle()"
        style="display: none;"
        role="button"
        aria-pressed="true"
        x-bind:aria-pressed="$theme.isResolvedToLight"
        aria-label="Activate light theme"
    />
    <x-ui.button 
        :icon="$darkIcon" 
        :$iconVariant
        variant="none"
        class="h-9 w-9 p-0 text-neutral-400 hover:text-primary transition-colors flex items-center justify-center"
        x-show="$theme.isResolvedToDark"
        x-on:click="$theme.toggle()"
        style="display: none;"
        role="button"
        aria-pressed="true"
        x-bind:aria-pressed="$theme.isResolvedToDark"
        aria-label="Activate dark theme"
    />
</div>
