@props([
    'position' => 'bottom-center',
    'teleport' => 'body',
    'portal' => false,
    'trap' => false,
    'offset' => 6,
    'checkbox' => false,
    'radio' => false,
    'resetFocus' => false
])

@php
    $isDefaultDropdownVariant = $checkbox || $radio;
    $classes = [
        'isolate z-50',
        'grid grid-cols-[auto_1fr_auto]' => !$isDefaultDropdownVariant ,
        'grid grid-cols-[auto_auto_1fr_auto]' => $isDefaultDropdownVariant,
        '[:where(&)]:max-w-96 [:where(&)]:min-w-40 text-start',
        'bg-white dark:bg-neutral-900 border border-black/10 dark:border-white/10',
        '[--dropdown-radius:var(--radius-box)] [--dropdown-padding:--spacing(.75)]
         rounded-(--dropdown-radius) p-(--dropdown-padding) space-y-1',
    ];  
@endphp

<div {{ $attributes }}>
    <div
        x-data="{
            open: false,
            openContextMenu(event) {
                event.preventDefault();
                
                this.$refs.anchor.style.left = event.clientX + 'px';
                this.$refs.anchor.style.top = event.clientY + 'px';
                this.open = true;
            },

            close(focusAfter) {
                if (!this.open) return

                this.open = false;
                
                
                focusAfter && focusAfter.focus()
            }
        }"
        x-on:keydown.escape.prevent.stop="close()"
        x-id="['context-menu']"
        wire:key="context-{{ uniqid() }}"
        class="relative w-max"
    >
        <!-- Trigger Area -->
        <div 
            x-on:contextmenu.prevent="openContextMenu($event)" 
            {{ $trigger->attributes }}
        >
            {{ $trigger }}
        </div>
        
        <div 
            x-ref="anchor" 
            class="fixed w-0 h-0 pointer-events-none" 
            style="z-index: -1;">
        </div>
        
        @if($portal)
            <template x-teleport="{{ $teleport }}" wire:key="context-portal-{{ uniqid() }}">
        @endif
            <div 
                x-show="open"
                x-ref="panel"
                x-anchor.{{ $position }}.offset.{{ $offset }}="$refs.anchor"
                x-trap.noscroll="open"
                x-on:keydown.down.prevent.stop="$focus.wrap().next()"
                x-on:keydown.up.prevent.stop="$focus.wrap().prev()"
                x-on:keydown.home.prevent.stop="$focus.first()"
                x-on:keydown.page-up.prevent.stop="$focus.first()"
                x-on:keydown.end.prevent.stop="$focus.last()"
                x-on:keydown.page-down.prevent.stop="$focus.last()"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-on:click.away="close()"
                x-bind:id="$id('context-menu')"
                style="display: none; backdrop-filter: blur(64px); -webkit-backdrop-filter: blur(64px);"
                {{ $menu->attributes->class(Arr::toCssClasses($classes)) }}
            >
                {{ $menu }}
            </div>
        @if($portal)
            </template>
        @endif
    </div>
</div>
