<x-layouts::main.header :title="$title ?? null">
    <x-slot:head>{{ $head ?? '' }}</x-slot:head>
    {{ $slot }}
    @include('layouts.main.footer')
</x-layouts::main.header>
