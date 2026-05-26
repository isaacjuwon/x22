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

<footer data-slot="footer" class="py-12">
    <div class="mx-auto max-w-6xl px-6">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-4">

            {{-- Brand --}}
            <div class="space-y-4">
                <span class="text-sm font-bold tracking-[0.2em] text-neutral-950 uppercase">
                    {{ $general->site_name }}
                </span>
                @if ($general->site_description)
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">
                        {{ $general->site_description }}
                    </p>
                @endif
                @if ($general->site_email)
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                        <a href="mailto:{{ $general->site_email }}" class="hover:text-primary transition-colors">
                            {{ $general->site_email }}
                        </a>
                    </p>
                @endif
            </div>

            {{-- Navigation --}}
            <div class="space-y-3">
                <p class="text-xs font-semibold uppercase tracking-widest text-neutral-700 dark:text-neutral-300">{{ __('Navigation') }}</p>
                <nav class="flex flex-col gap-2">
                    <x-ui.link href="{{ route('home') }}" class="text-sm text-neutral-600 dark:text-neutral-400 hover:text-primary">{{ __('Home') }}</x-ui.link>
                    @if ($general->show_posts_section)
                        <x-ui.link href="{{ route('posts.index') }}" wire:navigate class="text-sm text-neutral-600 dark:text-neutral-400 hover:text-primary">{{ __('Blog') }}</x-ui.link>
                    @endif
                    @if ($general->show_projects_section)
                        <x-ui.link href="{{ route('projects.index') }}" wire:navigate class="text-sm text-neutral-600 dark:text-neutral-400 hover:text-primary">{{ __('Projects') }}</x-ui.link>
                    @endif
                    @if ($general->show_testimonials_section)
                        <x-ui.link href="{{ route('testimonials.index') }}" wire:navigate class="text-sm text-neutral-600 dark:text-neutral-400 hover:text-primary">{{ __('Testimonials') }}</x-ui.link>
                    @endif
                </nav>
            </div>

            {{-- Social / Account --}}
            <div class="space-y-3">
                @if (!empty($socialLinks))
                    <p class="text-xs font-semibold uppercase tracking-widest text-neutral-700 dark:text-neutral-300">{{ __('Follow Us') }}</p>
                    <nav class="flex flex-col gap-2">
                        @foreach ($socialLinks as $name => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                               class="text-sm text-neutral-600 dark:text-neutral-400 hover:text-primary transition-colors">
                                {{ $name }}
                            </a>
                        @endforeach
                    </nav>
                @else
                    <p class="text-xs font-semibold uppercase tracking-widest text-neutral-700 dark:text-neutral-300">{{ __('Account') }}</p>
                    <nav class="flex flex-col gap-2">
                        @auth
                            <x-ui.link href="{{ route('dashboard') }}" class="text-sm text-neutral-600 dark:text-neutral-400 hover:text-primary">{{ __('Dashboard') }}</x-ui.link>
                        @else
                            <x-ui.link href="{{ route('login') }}" class="text-sm text-neutral-600 dark:text-neutral-400 hover:text-primary">{{ __('Sign In') }}</x-ui.link>
                            @if (Route::has('register'))
                                <x-ui.link href="{{ route('register') }}" class="text-sm text-neutral-600 dark:text-neutral-400 hover:text-primary">{{ __('Register') }}</x-ui.link>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>

            {{-- Pages --}}
            @php
                $pages = App\Models\Page::published()->get(['title', 'slug']);
            @endphp
            @if($pages->isNotEmpty())
                <div class="space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-widest text-neutral-700 dark:text-neutral-300">{{ __('Company') }}</p>
                    <nav class="flex flex-col gap-2">
                        @foreach($pages as $page)
                            <x-ui.link href="{{ route('pages.show', $page->slug) }}" wire:navigate class="text-sm text-neutral-600 dark:text-neutral-400 hover:text-primary">
                                {{ $page->title }}
                            </x-ui.link>
                        @endforeach
                    </nav>
                </div>
            @endif

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
