<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Welcome')])
    </head>
    <body class="flex min-h-screen flex-col items-center justify-center p-6 lg:p-8">
        <main class="w-full max-w-4xl space-y-12">
            
            {{-- ── Hero Section (JSK Style) ── --}}
            <section class="space-y-6">
                <div class="space-y-2">
                    <p class="term-comment text-sm uppercase tracking-widest">{{ __('System Initialization') }}</p>
                    <h1 class="text-3xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50 lg:text-5xl">
                        {{ __('Better content, fewer incidents, faster delivery.') }}
                    </h1>
                </div>

                <div class="term-block font-mono shadow-2xl">
                    <div class="flex items-center gap-2 mb-4 border-b border-neutral-800 pb-2">
                        <div class="h-2 w-2 rounded-full bg-red-500"></div>
                        <div class="h-2 w-2 rounded-full bg-amber-500"></div>
                        <div class="h-2 w-2 rounded-full bg-green-500"></div>
                        <span class="ml-2 text-[10px] uppercase opacity-50">{{ __('Terminal — v1.0.0') }}</span>
                    </div>
                    <p class="term-prompt mb-2">{{ __('audit --check=platform') }}</p>
                    <p class="term-arrow mb-4">{{ __('Profiling system modules...') }}</p>
                    <ul class="space-y-1 text-sm">
                        <li class="term-dot term-dot-success text-green-400">GET /api/v1/auth <span class="opacity-50">142ms p95 [OK]</span></li>
                        <li class="term-dot term-dot-success text-green-400">GET /api/v1/posts <span class="opacity-50">198ms p95 [OK]</span></li>
                        <li class="term-dot term-dot-warning text-amber-400">POST /api/v1/search <span class="opacity-50">1,180ms p95 [SLOW]</span></li>
                        <li class="term-dot term-dot-error text-red-400">GET /api/v1/analytics <span class="opacity-50">timeout p95 [CRITICAL]</span></li>
                    </ul>
                    <p class="mt-4 text-xs opacity-50">↳ 1 critical, 1 slow — avg 480ms, target <200ms</p>
                </div>
            </section>

            {{-- ── Main Content ── --}}
            <x-ui.card class="p-8 lg:p-12">
                <div class="mb-12 flex items-center justify-between">
                    <x-app-logo class="h-8" />
                    
                    @if (Route::has('login'))
                        <nav class="flex gap-4">
                            @auth
                                <x-ui.button as="a" href="{{ route('dashboard') }}" variant="primary" size="sm" wire:navigate>
                                    {{ __('Dashboard') }}
                                </x-ui.button>
                            @else
                                <x-ui.button as="a" href="{{ route('login') }}" variant="ghost" size="sm" wire:navigate>
                                    {{ __('Log in') }}
                                </x-ui.button>

                                @if (Route::has('register'))
                                    <x-ui.button as="a" href="{{ route('register') }}" variant="outline" size="sm" wire:navigate>
                                        {{ __('Register') }}
                                    </x-ui.button>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </div>

                <div class="grid gap-12 lg:grid-cols-2">
                    <div class="space-y-4 border-l-2 border-neutral-200 dark:border-neutral-800 pl-6 transition-colors hover:border-primary">
                        <h2 class="text-xl font-bold uppercase tracking-tight">{{ __('Documentation') }}</h2>
                        <p class="text-sm leading-relaxed text-neutral-600 dark:text-neutral-400">
                            {{ __('A focused investigation into the performance, reliability, and design issues slowing your team down.') }}
                        </p>
                        <x-ui.button as="a" href="https://laravel.com/docs" target="_blank" variant="ghost" size="sm">
                            {{ __('Read Docs') }}
                        </x-ui.button>
                    </div>

                    <div class="space-y-4 border-l-2 border-neutral-200 dark:border-neutral-800 pl-6 transition-colors hover:border-primary">
                        <h2 class="text-xl font-bold uppercase tracking-tight">{{ __('Video Tutorials') }}</h2>
                        <p class="text-sm leading-relaxed text-neutral-600 dark:text-neutral-400">
                            {{ __('Hands-on work to remove bottlenecks, cut avoidable incidents, and make delivery feel less stressful.') }}
                        </p>
                        <x-ui.button as="a" href="https://laracasts.com" target="_blank" variant="ghost" size="sm">
                            {{ __('Watch Videos') }}
                        </x-ui.button>
                    </div>
                </div>

                <div class="mt-12 border-t border-neutral-100 pt-8 dark:border-neutral-800">
                    <div class="flex flex-col items-center justify-between gap-6 lg:flex-row">
                        <p class="term-comment text-xs font-mono">
                            {{ __('Ready to stop losing time to platform instability?') }}
                        </p>
                        <x-ui.button as="a" href="https://cloud.laravel.com" target="_blank" variant="primary" size="md">
                            {{ __('Deploy System') }}
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>
            
            <footer class="text-center">
                <p class="text-[10px] uppercase tracking-[0.3em] text-neutral-400 dark:text-neutral-600">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('Built for Stability') }}.
                </p>
            </footer>
        </main>
    </body>
</html>
