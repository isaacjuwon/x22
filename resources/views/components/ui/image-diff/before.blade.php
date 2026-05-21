@aware(['vertical'=>false])

<img 
    {{ $attributes->class('z-10 absolute inset-0 block size-full object-cover object-left') }} 
     @style([
        "clip-path: inset(0 calc(100% - var(--covered-area)) 0 0)" => !$vertical,
        "clip-path: inset(0 0 calc(100% - var(--covered-area)) 0)" => $vertical,
    ])
/>