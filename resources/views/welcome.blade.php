<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-bind:class="$theme.currentTheme === 'dark' ? 'dark' : ''">
    <head>
        @include('partials.head', ['title' => __('Welcome')])
    </head>
    <body class="flex min-h-screen flex-col items-center justify-center bg-neutral-50 p-6 text-neutral-950 lg:p-8 transition-colors duration-300">
        <div class="fixed right-8 top-8">
            <x-ui.theme-switcher variant="inline" />
        </div>
        <main class="relative flex w-full max-w-5xl items-center justify-between gap-12">
            
            {{-- ── Left Content ── --}}
            <div class="flex flex-col gap-8">
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 self-start rounded-full border border-primary/30 bg-primary/5 px-4 py-1.5 text-xs font-bold text-primary">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-primary">
                        </span>
                    </span>
                    {{ __('Coding Agent with taste-1') }}
                </div>

                {{-- Hero Section --}}
                <div class="space-y-4">
                    <h1 class="text-5xl font-bold tracking-tight text-neutral-950 lg:text-7xl">
                        DeepSeek + taste.
                    </h1>
                    <h2 class="text-5xl font-bold tracking-tight text-neutral-950 lg:text-7xl">
                        $40 <span class="rounded-lg bg-accent-purple px-4 py-1 text-white">usage.</span> $1 plan.
                    </h2>
                </div>

                {{-- Description --}}
                <div class="max-w-xl space-y-2 text-lg text-neutral-500">
                    <p>Command Code now supports DeepSeek models.</p>
                    <p>Offer is valid on DeepSeek V4 Pro.</p>
                </div>

                {{-- Terminal Command Button --}}
                <div class="flex max-w-md items-center gap-3 rounded-full bg-accent-purple p-2 pl-6 pr-4 transition-transform hover:scale-[1.02] shadow-lg shadow-primary/20">
                    <span class="font-mono text-xl font-bold text-white/70">&gt;</span>
                    <code class="flex-1 font-mono text-lg font-bold text-white">npm i -g command-code</code>
                </div>
            </div>

            {{-- ── Right Icon Sidebar ── --}}
            <div class="flex flex-col gap-8 text-neutral-400 dark:text-neutral-500">
                <x-ui.icon name="squares-2x2" class="size-8 transition-colors hover:text-primary" />
                <div class="relative">
                    <x-ui.icon name="magnifying-glass" class="size-8 transition-colors hover:text-primary" />
                    <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-white">8</span>
                </div>
                <x-ui.icon name="circle-stack" class="size-8 transition-colors hover:text-primary" />
                <x-ui.icon name="command-line" class="size-8 transition-colors hover:text-primary" />
                <x-ui.icon name="arrow-path" class="size-8 transition-colors hover:text-primary" />
            </div>

            {{-- ── Floating Bottom Controls ── --}}
            <div class="fixed bottom-12 left-1/2 flex -translate-x-1/2 items-center gap-8 rounded-full border border-neutral-200 dark:border-neutral-800 bg-white/80 dark:bg-black/50 px-8 py-4 backdrop-blur-xl shadow-2xl transition-colors duration-300">
                <div class="flex items-center gap-3">
                    <x-app-logo-icon class="size-6" />
                    <span class="text-sm font-bold tracking-tight text-neutral-950">Command Code</span>
                    <x-ui.icon name="globe-alt" class="size-4 text-neutral-400" />
                </div>
                <div class="h-4 w-px bg-neutral-200 dark:bg-neutral-800"></div>
                <p class="text-sm text-neutral-500">$1 gets you $40 of DeepSeek V4 Pr... <span class="text-primary font-bold cursor-pointer">more</span></p>
                <div class="flex gap-4">
                    <x-ui.icon name="heart" class="size-6 text-neutral-400 hover:text-red-500 transition-colors" />
                    <x-ui.icon name="share" class="size-6 text-neutral-400 hover:text-primary transition-colors" />
                </div>
            </div>

        </main>
    </body>
</html>

