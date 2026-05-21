@aware(['contained' => false, 'variant' => 'default'])

@props([
    'active' => false,
    'label' => 1,
    'completed' => false,
    'icon' => null,
    'completedIcon' => 'check',
])

@php
    if (!in_array($variant, ['default', 'minimal'])) {
        throw new RuntimeException('invalid variant');
    }

    $variantPath = "ui.wizard.step.variant.$variant";
@endphp

<x-dynamic-component :component="$variantPath" :$attributes>
    {{ $slot }}
</x-dynamic-component>
