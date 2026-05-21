@php
    use App\Settings\GeneralSettings;
    use App\Settings\SocialSettings;

    $general = app(GeneralSettings::class);
    $social  = app(SocialSettings::class);

    $socialLinks = array_filter([
        'GitHub'    => $social->github_url,
        'Twitter'   => $social->twitter_url,
        'LinkedIn'  => $social->linkedin_url,
        'Instagram' => $social->instagram_url,
        'YouTube'   => $social->youtube_url,
        'Facebook'  => $social->facebook_url,
    ]);
@endphp

<footer class="border-t border-neutral-300 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-950 py-12">
    <div class="mx-auto max-w-6xl px-6">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-3">

            {{-- Brand --}}
            <div class="space-y-3">
                <span class="term-prompt text-sm font-bold tracking-widest text-green-600 dark:text-green-400 uppercase">
                    {{ $general->site_name }}
                </span>
                @if ($general->site_description)
                    <p class="text-xs text-neutral-500 dark:text-neutral-500 leading-relaxed">
                        {{ $general->site_description }}
                    </p>
                @endif
                @if ($general->site_email)
                    <p class="text-xs text-neutral-500 dark:text-neutral-500">
                        <a href="mailto:{{ $general->site_email }}" class="hover:text-green-600 dark:hover:text-green-400">
                            {{ $general->site_email }}
                        </a>
                    </p>
                @endif
            </div>

            {{-- Navigation --}}
            <div class="space-y-3">
                <p class="text-xs font-semibold uppercase tracking-widest text-neutral-700 dark:text-neutral-400">{{ __('Navigation') }}</p>
                <nav class="flex flex-col gap-2">
                    <x-ui.link href="{{ route('home') }}" class="text-sm text-neutral-600 dark:text-neutral-500 hover:text-green-600 dark:hover:text-green-400">{{ __('Home') }}</x-ui.link>
                    @if ($general->show_posts_section)
                        <x-ui.link href="{{ route('posts.index') }}" class="text-sm text-neutral-600 dark:text-neutral-500 hover:text-green-600 dark:hover:text-green-400">{{ __('Blog') }}</x-ui.link>
                    @endif
                    @if ($general->show_projects_section)
                        <x-ui.link href="{{ route('projects.index') }}" class="text-sm text-neutral-600 dark:text-neutral-500 hover:text-green-600 dark:hover:text-green-400">{{ __('Projects') }}</x-ui.link>
                    @endif
                    @if ($general->show_testimonials_section)
                        <x-ui.link href="{{ route('home') }}#testimonials" class="text-sm text-neutral-600 dark:text-neutral-500 hover:text-green-600 dark:hover:text-green-400">{{ __('Testimonials') }}</x-ui.link>
                    @endif
                </nav>
            </div>

            {{-- Social / Account --}}
            <div class="space-y-3">
                @if (!empty($socialLinks))
                    <p class="text-xs font-semibold uppercase tracking-widest text-neutral-700 dark:text-neutral-400">{{ __('Follow Us') }}</p>
                    <nav class="flex flex-col gap-2">
                        @foreach ($socialLinks as $name => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                               class="text-sm text-neutral-600 dark:text-neutral-500 hover:text-green-600 dark:hover:text-green-400">
                                {{ $name }}
                            </a>
                        @endforeach
                    </nav>
                @else
                    <p class="text-xs font-semibold uppercase tracking-widest text-neutral-700 dark:text-neutral-400">{{ __('Account') }}</p>
                    <nav class="flex flex-col gap-2">
                        @auth
                            <x-ui.link href="{{ route('dashboard') }}" class="text-sm text-neutral-600 dark:text-neutral-500 hover:text-green-600 dark:hover:text-green-400">{{ __('Dashboard') }}</x-ui.link>
                        @else
                            <x-ui.link href="{{ route('login') }}" class="text-sm text-neutral-600 dark:text-neutral-500 hover:text-green-600 dark:hover:text-green-400">{{ __('Sign In') }}</x-ui.link>
                            @if (Route::has('register'))
                                <x-ui.link href="{{ route('register') }}" class="text-sm text-neutral-600 dark:text-neutral-500 hover:text-green-600 dark:hover:text-green-400">{{ __('Register') }}</x-ui.link>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>

        </div>

        <x-ui.separator class="my-8" />

        <div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
            <p class="term-comment text-xs text-neutral-500 dark:text-neutral-500">
                &copy; {{ date('Y') }} {{ $general->site_name }}. {{ __('All rights reserved.') }}
            </p>
            <x-ui.theme-switcher variant="inline" />
        </div>
    </div>
</footer>
