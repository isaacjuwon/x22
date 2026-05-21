@props(['testimonial'])

<blockquote data-slot="card" class="flex flex-col p-6">
    {{-- Star rating --}}
    <div class="mb-4 flex gap-0.5">
        @for ($i = 1; $i <= 5; $i++)
            <x-ui.icon
                name="star"
                variant="solid"
                class="h-4 w-4 {{ $i <= $testimonial->rating ? 'text-green-400' : 'text-neutral-700' }}"
            />
        @endfor
    </div>

    <x-ui.text class="flex-1 text-sm leading-relaxed text-neutral-600 dark:text-neutral-400">
        "{{ $testimonial->comment }}"
    </x-ui.text>

    <footer class="mt-6 flex items-center gap-3 border-t border-neutral-800 pt-4">
        <img
            src="https://api.dicebear.com/9.x/avataaars/svg?seed={{ $testimonial->user->id }}"
            alt="{{ $testimonial->user->name }}"
            class="h-9 w-9"
        />
        <div>
            <x-ui.text class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">{{ $testimonial->user->name }}</x-ui.text>
            @if ($testimonial->project)
                <x-ui.text class="term-comment text-xs text-neutral-500 dark:text-neutral-600">{{ $testimonial->project->title }}</x-ui.text>
            @endif
        </div>
    </footer>
</blockquote>
