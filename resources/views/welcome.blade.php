<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Welcome')])
    </head>
    <body class="flex min-h-screen flex-col items-center justify-center p-6 lg:p-8">
        <main class="w-full max-w-4xl space-y-12">
            
            {{-- ── Hero Section (New Terminal) ── --}}
            <section class="space-y-8">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <x-ui.icon name="ps:terminal-window" class="size-6 text-primary" />
                        <p class="term-comment text-xs uppercase tracking-[0.2em]">{{ __('System v4.0.0-stable') }}</p>
                    </div>
                    <h1 class="text-4xl font-bold tracking-tighter text-neutral-900 dark:text-neutral-50 lg:text-6xl uppercase">
                        {{ __('Engineering Remarkable Digital Experiences.') }}
                    </h1>
                </div>

                <div class="term-block border-l-4 border-l-primary shadow-2xl">
                    <div class="flex items-center justify-between mb-6 border-b border-neutral-800 pb-4">
                        <div class="flex gap-2">
                            <div class="h-1.5 w-1.5 bg-neutral-800"></div>
                            <div class="h-1.5 w-1.5 bg-neutral-800"></div>
                            <div class="h-1.5 w-1.5 bg-neutral-800"></div>
                        </div>
                        <span class="text-[9px] uppercase tracking-widest opacity-40">{{ __('active_session: root@sheaf') }}</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <span class="term-prompt"></span>
                            <span class="animate-pulse">_</span>
                        </div>
                        
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <p class="text-primary text-[10px] uppercase font-bold tracking-widest">{{ __('Core Modules') }}</p>
                                <ul class="space-y-1 text-xs opacity-70">
                                    <li class="flex items-center gap-2"><x-ui.icon name="ps:check-circle" class="size-3" /> {{ __('Authentication Engine') }}</li>
                                    <li class="flex items-center gap-2"><x-ui.icon name="ps:check-circle" class="size-3" /> {{ __('Content Pipeline') }}</li>
                                    <li class="flex items-center gap-2"><x-ui.icon name="ps:check-circle" class="size-3" /> {{ __('Media Storage') }}</li>
                                </ul>
                            </div>
                            <div class="space-y-2">
                                <p class="text-primary text-[10px] uppercase font-bold tracking-widest">{{ __('System Stats') }}</p>
                                <ul class="space-y-1 text-xs opacity-70">
                                    <li class="flex items-center gap-2 text-success"><x-ui.icon name="ps:activity" class="size-3" /> {{ __('Uptime: 99.9%') }}</li>
                                    <li class="flex items-center gap-2 text-info"><x-ui.icon name="ps:git-branch" class="size-3" /> {{ __('Version: 4.2.1') }}</li>
                                    <li class="flex items-center gap-2"><x-ui.icon name="ps:database" class="size-3" /> {{ __('DB: Connected') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ── Quick Access ── --}}
            <div class="grid gap-6 sm:grid-cols-3">
                <x-ui.button as="a" href="{{ route('login') }}" variant="primary" size="lg" class="w-full">
                    <x-ui.icon name="ps:user-focus" class="size-4 mr-2" />
                    {{ __('Access System') }}
                </x-ui.button>
                
                <x-ui.button as="a" href="https://laravel.com/docs" target="_blank" variant="outline" size="lg" class="w-full">
                    <x-ui.icon name="ps:book-open-text" class="size-4 mr-2" />
                    {{ __('Documentation') }}
                </x-ui.button>

                <x-ui.button as="a" href="https://laracasts.com" target="_blank" variant="outline" size="lg" class="w-full">
                    <x-ui.icon name="ps:monitor-play" class="size-4 mr-2" />
                    {{ __('View Training') }}
                </x-ui.button>
            </div>

            {{-- ── Footer ── --}}
            <footer class="pt-12 border-t border-neutral-100 dark:border-neutral-900 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <x-app-logo class="h-6 opacity-50 grayscale hover:opacity-100 hover:grayscale-0 transition-all" />
                    <p class="text-[10px] uppercase tracking-[0.2em] text-neutral-400">
                        &copy; {{ date('Y') }} {{ config('app.name') }}
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="term-indicator text-success text-[9px] uppercase font-bold tracking-widest">{{ __('Network: Online') }}</span>
                    <span class="opacity-20">|</span>
                    <span class="text-[9px] uppercase tracking-widest text-neutral-500">{{ __('Secure Session') }}</span>
                </div>
            </footer>
        </main>
    </body>
</html>
