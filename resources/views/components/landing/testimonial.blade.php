@props(['testimonial'])

<blockquote data-slot="card" class="group flex flex-col overflow-hidden rounded-[2rem] border border-neutral-900 bg-neutral-900/50 p-0 transition-all duration-300 hover:border-primary/50 hover:bg-neutral-900">

    {{-- Quote body --}}
    <div class="flex flex-1 flex-col gap-6 p-8">
        {{-- Star rating --}}
        <div class="flex items-center gap-1.5">
            <div class="flex items-center gap-1">
                @for ($i = 1; $i <= 5; $i++)
                    <x-ui.icon
                        name="heroicon-s-star"
                        class="h-4 w-4 {{ $i <= $testimonial->rating ? 'text-primary' : 'text-neutral-800' }}"
                    />
                @endfor
            </div>
            <span class="ml-auto text-[10px] font-bold uppercase tracking-widest text-neutral-500">
                {{ $testimonial->rating }}.0
            </span>
        </div>

        {{-- Opening quote mark in terminal style --}}
        <div class="flex flex-col gap-4">
            <div class="flex items-center gap-2">
                <span class="font-mono text-sm font-bold text-primary animate-pulse">&gt;_</span>
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-600">{{ __('verified_feedback') }}</span>
            </div>
            <p class="flex-1 text-lg font-medium leading-relaxed text-neutral-300 italic">
                "{{ $testimonial->comment }}"
            </p>
        </div>

        <div class="h-px w-full bg-gradient-to-r from-transparent via-neutral-800 to-transparent"></div>

        {{-- Author --}}
        <footer class="flex items-center gap-4">
            <x-ui.avatar
                :name="$testimonial->user->name"
                size="sm"
                class="ring-2 ring-neutral-800 group-hover:ring-primary/50 transition-all"
            />
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-bold text-white">
                    {{ $testimonial->user->name }}
                </p>
                @if ($testimonial->project)
                    <p class="truncate text-[10px] font-bold uppercase tracking-widest text-primary/70">
                        {{ $testimonial->project->title }}
                    </p>
                @endif
            </div>
        </footer>
    </div>
</blockquote>

