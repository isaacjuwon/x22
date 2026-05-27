<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => __('Welcome')])
    </head>
    <body class="flex min-h-screen flex-col items-center justify-center bg-black p-6 text-white lg:p-8">
        <main class="relative flex w-full max-w-5xl items-center justify-between gap-12">
            
            {{-- ── Left Content ── --}}
            <div class="flex flex-col gap-8">
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 self-start rounded-full border border-primary/30 bg-primary/5 px-4 py-1.5 text-xs font-medium text-primary">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-primary"></span>
                    </span>
                    {{ __('Coding Agent with taste-1') }}
                </div>

                {{-- Hero Section --}}
                <div class="space-y-4">
                    <h1 class="text-5xl font-bold tracking-tight lg:text-7xl">
                        DeepSeek + taste.
                    </h1>
                    <h2 class="text-5xl font-bold tracking-tight lg:text-7xl">
                        $40 <span class="rounded-lg bg-accent-purple px-4 py-1 text-white">usage.</span> $1 plan.
                    </h2>
                </div>

                {{-- Description --}}
                <div class="max-w-xl space-y-2 text-lg text-neutral-400">
                    <p>Command Code now supports DeepSeek models.</p>
                    <p>Offer is valid on DeepSeek V4 Pro.</p>
                </div>

                {{-- Terminal Command Button --}}
                <div class="flex max-w-md items-center gap-3 rounded-full bg-accent-purple p-2 pl-6 pr-4 transition-transform hover:scale-[1.02]">
                    <span class="font-mono text-xl font-bold text-white/70">&gt;</span>
                    <code class="flex-1 font-mono text-lg font-bold text-white">npm i -g command-code</code>
                </div>
            </div>

            {{-- ── Right Icon Sidebar ── --}}
            <div class="flex flex-col gap-8 text-neutral-500">
                <x-ui.icon name="squares-2x2" class="size-8 transition-colors hover:text-white" />
                <div class="relative">
                    <x-ui.icon name="magnifying-glass" class="size-8 transition-colors hover:text-white" />
                    <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-white text-[10px] font-bold text-black">8</span>
                </div>
                <x-ui.icon name="circle-stack" class="size-8 transition-colors hover:text-white" />
                <x-ui.icon name="command-line" class="size-8 transition-colors hover:text-white" />
                <x-ui.icon name="arrow-path" class="size-8 transition-colors hover:text-white" />
            </div>

            {{-- ── Floating Bottom Controls (Simulated from image) ── --}}
            <div class="fixed bottom-12 left-1/2 flex -translate-x-1/2 items-center gap-8 rounded-full border border-neutral-800 bg-black/50 px-8 py-4 backdrop-blur-xl">
                <div class="flex items-center gap-3">
                    <x-app-logo-icon class="size-6" />
                    <span class="text-sm font-bold tracking-tight">Command Code</span>
                    <x-ui.icon name="globe-alt" class="size-4 opacity-50" />
                </div>
                <div class="h-4 w-px bg-neutral-800"></div>
                <p class="text-sm text-neutral-500">$1 gets you $40 of DeepSeek V4 Pr... <span class="text-white cursor-pointer">more</span></p>
                <div class="flex gap-4">
                    <x-ui.icon name="heart" class="size-6 opacity-50" />
                    <x-ui.icon name="share" class="size-6 opacity-50" />
                </div>
            </div>

        </main>
    </body>
</html>

