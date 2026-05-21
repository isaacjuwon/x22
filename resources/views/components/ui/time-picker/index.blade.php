@props([
    'format'      => 'auto',      // 'auto' | '12-hour' | '24-hour'
    'multiple'    => false,       //-- shorthand for mode="multiple"
    'interval'    => 30,          //-- minutes between slots
    'min'         => null,        //-- "HH:mm" | "now"
    'max'         => null,        //-- "HH:mm" | "now"
    'unavailable' => null,        //-- "03:00,04:00,05:30-07:29"
    'openTo'      => null,        
    'locale'      => 'auto',      //-- BCP-47 e.g. "fr", "ja-JP"
    'trigger'     => 'button',
    'clearable'   => false,
    'disabled'    => false,
    'invalid'     => false,
    'placeholder' => 'select a time',
    'size'        => 'default',  
    'label'       => null,
    'variant'     => 'default',
    'openPanel'   => true,
    'special'   => [],
])

@php

    // Detect wire:model and optional .live modifier
    $modelAttr = collect($attributes->getAttributes())
        ->keys()
        ->first(fn ($key) => str_starts_with($key, 'wire:model'));

    $model      = $modelAttr ? $attributes->get($modelAttr) : null;
    $isLive     = $modelAttr && str_contains($modelAttr, '.live');
    $livewireId = isset($__livewire) ? $__livewire->getId() : null;

    $interval = (int) $interval;

    if ($trigger === 'input' && $multiple) {
        throw new Exception("The 'input' trigger cannot be used with multiple selection. Use 'pills' or `default` trigger for multiple times.");
    }

    if (!in_array($format, ['auto', '12-hour', '24-hour'])) {
        throw new Exception("Invalid 'format' prop value: '{$format}'. Allowed: 'auto', '12-hour', '24-hour'.");
    }

    if (!in_array($trigger, ['button', 'input', 'pills'])) {
        throw new Exception("Invalid 'trigger' prop value: '{$trigger}'. Allowed: 'button', 'input', 'pills'.");
    }

    if (!in_array($variant, ['default', 'checkbox'])) {
        throw new Exception("Invalid 'variant' prop value: '{$variant}'. Allowed: 'default', 'checkbox'.");
    }

    if (!is_int($interval) || $interval <= 0 || $interval > 60) {
        throw new Exception("Invalid 'interval' prop value: '{$interval}'. Must be an integer between 1 and 60.");
    }
@endphp

<div
    x-data="timePickerComponent({
        livewire:    @js(isset($livewireId)) ? window.Livewire.find(@js($livewireId)) : null,
        isLive:      @js($isLive),
        model:       @js($model),
        interval:    @js($interval),
        min:         @js($min),
        max:         @js($max),
        isMultiple:  @js($multiple),
        unavailable: @js($unavailable),
        openTo:      @js($openTo),
        locale:      @js($locale),
        timeFormat:  @js($format),
        triggerType: @js($trigger),
        placeholder: @js($placeholder),
        openPanel:   @js($openPanel),
        special:     @js($special)
    })"
    x-rover
    data-slot="time-picker"
    {{ $attributes->class(['relative text-start [--timepicker-round:var(--radius-box)] [--timepicker-padding:--spacing(1)]']) }}
>
    <x-ui.time-picker.trigger/>
 
    @if ($openPanel)
        <x-ui.time-picker.panel />
    @endif
</div>