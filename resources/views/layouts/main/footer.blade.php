        {{ $slot }}

        <footer class="mt-auto border-t border-neutral-200 dark:border-neutral-200 bg-white dark:bg-black py-12 transition-colors duration-300">
            @php
                use App\Settings\GeneralSettings;
                $general = app(GeneralSettings::class);
            @endphp
            <div class="container mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex flex-col items-center md:items-start gap-4">
                    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3">
                        <span class="text-lg font-black tracking-tighter text-neutral-950 dark:text-white uppercase">{{ $general->site_name }}</span>
                    </a>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400">
                        &copy; {{ date('Y') }} {{ config('app.name') }} — {{ __('Senior Web Architect') }}
                    </p>
                </div>

                <div class="flex items-center gap-10 text-[10px] font-black uppercase tracking-[0.2em] text-neutral-400">
                    <div class="flex items-center gap-2">
                        <span class="size-2 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-neutral-500">{{ __('Network: Online') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-ui.icon name="cpu-chip" class="size-4" />
                        <span>v{{ config('app.version', '4.2.0') }}</span>
                    </div>
                </div>

                <nav class="flex items-center gap-6">
                    <x-ui.button as="a" href="https://github.com" target="_blank" variant="none" class="p-0 text-neutral-400 hover:text-primary">
                        <x-ui.icon name="link" class="size-5" />
                    </x-ui.button>
                    <x-ui.button as="a" href="https://twitter.com" target="_blank" variant="none" class="p-0 text-neutral-400 hover:text-primary">
                        <x-ui.icon name="link" class="size-5" />
                    </x-ui.button>
                </nav>
            </div>
        </footer>
    </body>
</html>
