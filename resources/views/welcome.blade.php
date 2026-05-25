<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Welcome')])
    </head>
    <body class="flex min-h-screen flex-col items-center justify-center p-6 lg:p-8">
        <main class="w-full max-w-4xl">
            <x-ui.card class="overflow-hidden">
                <div class="p-8 lg:p-12">
                    <div class="mb-8 flex items-center justify-between">
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

                    <div class="space-y-6">
                        <div>
                            <h1 class="term-prompt text-2xl font-bold text-neutral-900 dark:text-neutral-50 lg:text-3xl">
                                {{ __('System Initialization') }}
                            </h1>
                            <p class="term-comment mt-2 text-neutral-500 dark:text-neutral-400">
                                {{ __('Laravel Framework') }} v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                            </p>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-2">
                            <div class="space-y-4">
                                <x-ui.heading level="h2" size="sm" class="term-accent uppercase tracking-widest">
                                    {{ __('Documentation') }}
                                </x-ui.heading>
                                <p class="text-sm leading-relaxed text-neutral-600 dark:text-neutral-400">
                                    Laravel has incredibly rich documentation covering every aspect of the framework. Whether you are a newcomer or have prior experience, we recommend reading our documentation from beginning to end.
                                </p>
                                <x-ui.button as="a" href="https://laravel.com/docs" target="_blank" variant="ghost" size="sm" class="group">
                                    {{ __('Read Docs') }} <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                                </x-ui.button>
                            </div>

                            <div class="space-y-4">
                                <x-ui.heading level="h2" size="sm" class="term-accent uppercase tracking-widest">
                                    {{ __('Laracasts') }}
                                </x-ui.heading>
                                <p class="text-sm leading-relaxed text-neutral-600 dark:text-neutral-400">
                                    Laracasts offers thousands of video tutorials on Laravel, PHP, and JavaScript development. Check them out, see for yourself, and massively level up your development skills in the process.
                                </p>
                                <x-ui.button as="a" href="https://laracasts.com" target="_blank" variant="ghost" size="sm" class="group">
                                    {{ __('Watch Videos') }} <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
                                </x-ui.button>
                            </div>
                        </div>

                        <div class="border-t border-neutral-200 pt-8 dark:border-neutral-800">
                            <div class="flex flex-col items-center justify-between gap-4 lg:flex-row">
                                <p class="term-comment text-xs text-neutral-500">
                                    {{ __('ready to deploy') }} — {{ now()->format('Y-m-d H:i') }}
                                </p>
                                <x-ui.button as="a" href="https://cloud.laravel.com" target="_blank" variant="primary" size="md">
                                    {{ __('Deploy to Laravel Cloud') }}
                                </x-ui.button>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.card>
            
            <footer class="mt-8 text-center">
                <p class="text-[10px] uppercase tracking-[0.2em] text-neutral-400 dark:text-neutral-600">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('Built with Sheaf UI') }}.
                </p>
            </footer>
        </main>
    </body>
</html>
