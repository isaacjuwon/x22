<x-layouts::app :title="__('Overview')">
    <div class="space-y-8 px-6 py-8">
        {{-- Header Section --}}
        <div class="flex flex-col gap-2">
            <h1 class="text-4xl font-bold tracking-tight text-neutral-950">{{ __('Welcome back,') }} {{ auth()->user()->name }}</h1>
            <p class="text-neutral-500 font-medium">{{ __('Here is what is happening with your portfolio and blog today.') }}</p>
        </div>

        {{-- Livewire Stats Component --}}
        <livewire:dashboard.stats />
    </div>
</x-layouts::app>
