@props(['testimonial'])

<blockquote data-slot="card" class="flex flex-col p-8 gap-6 transition-all duration-300">
    {{-- Quote --}}
    <div class="space-y-4">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-primary uppercase tracking-widest">{{ __('feedback_log') }}</span>
            <div class="h-px flex-1 bg-neutral-100 dark:bg-neutral-800"></div>
        </div>
        <p class="text-lg font-medium leading-relaxed text-neutral-900 dark:text-white italic">
            "{{ $testimonial->comment }}"
        </p>
    </div>

    {{-- Author --}}
    <footer class="mt-auto flex items-center gap-4">
        <x-ui.avatar
            :name="$testimonial->user->name"
            size="sm"
            class="ring-2 ring-neutral-100 dark:ring-neutral-800"
        />
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-bold text-neutral-900 dark:text-white uppercase tracking-widest">
                {{ $testimonial->user->name }}
            </p>
            @if ($testimonial->project)
                <p class="truncate text-[10px] font-bold text-primary uppercase tracking-widest opacity-80">
                    // {{ $testimonial->project->title }}
                </p>
            @endif
        </div>
    </footer>
</blockquote>
