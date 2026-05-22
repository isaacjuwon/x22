<x-layouts::main :title="__('Testimonials')">
    <section class="border-b border-neutral-800 px-6 py-16 text-center">
        <div class="mx-auto max-w-2xl space-y-4">
            <x-ui.heading level="h1" size="xl" class="text-4xl font-bold text-neutral-700 dark:text-neutral-200">
                {{ __('Testimonials') }}
            </x-ui.heading>
            <x-ui.text class="text-lg text-neutral-600 dark:text-neutral-400">
                {{ __('What people are saying about our work.') }}
            </x-ui.text>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-6 py-12">
        @if ($testimonials->isEmpty())
            <x-ui.empty class="py-16">
                <x-ui.empty.media>
                    <x-ui.icon name="chat-bubble-bottom-center-text" class="h-10 w-10 text-neutral-400 dark:text-neutral-600" />
                </x-ui.empty.media>
                <x-ui.empty.contents>
                    <x-ui.text class="text-neutral-500">{{ __('No testimonials yet.') }}</x-ui.text>
                </x-ui.empty.contents>
            </x-ui.empty>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($testimonials as $testimonial)
                    <article data-slot="card" class="flex flex-col p-6 border border-neutral-200 dark:border-neutral-800 rounded-lg">
                        <div class="mb-4">
                            <div class="flex items-center gap-1 text-amber-500 mb-2">
                                @for ($i = 0; $i < $testimonial->rating; $i++)
                                    <x-ui.icon name="star" class="h-4 w-4" />
                                @endfor
                            </div>
                            <x-ui.text class="italic text-neutral-600 dark:text-neutral-400">
                                "{{ $testimonial->comment }}"
                            </x-ui.text>
                        </div>
                        <div class="mt-auto pt-4 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <x-ui.avatar :name="$testimonial->user->name ?? 'Anonymous'" size="sm" />
                                <x-ui.text class="text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    {{ $testimonial->user->name ?? 'Anonymous' }}
                                </x-ui.text>
                            </div>
                            @if ($testimonial->project)
                                <x-ui.text class="text-xs text-neutral-500">
                                    <a href="{{ route('projects.show', $testimonial->project) }}" wire:navigate class="hover:text-green-500">
                                        {{ $testimonial->project->title }}
                                    </a>
                                </x-ui.text>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($testimonials->hasPages())
                <div class="flex justify-center pt-8">
                    {{ $testimonials->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts::main>
