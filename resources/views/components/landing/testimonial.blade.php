@props(['testimonial'])

<blockquote data-slot="card" class="group flex flex-col overflow-hidden p-0 transition-all duration-200">

    {{-- Quote body --}}
    <div class="flex flex-1 flex-col gap-4 p-5">
        {{-- Star rating --}}
        <div class="flex items-center gap-1.5">
            <div class="flex items-center gap-0.5">
                @for ($i = 1; $i <= 5; $i++)
                    <x-ui.icon
                        name="star"
                        variant="{{ $i <= $testimonial->rating ? 'solid' : 'outline' }}"
                        class="h-3.5 w-3.5 {{ $i <= $testimonial->rating ? 'text-green-500' : 'text-neutral-400 dark:text-neutral-600' }}"
                    />
                @endfor
            </div>
            <x-ui.text class="ml-auto text-xs text-neutral-500">
                {{ $testimonial->rating }}/5
            </x-ui.text>
        </div>

        {{-- Opening quote mark in terminal style --}}
        <div class="flex gap-2">
            <span class="mt-0.5 font-mono text-lg font-bold leading-none text-green-500 select-none">&gt;_</span>
            <x-ui.text class="flex-1 text-sm leading-relaxed italic">
                {{ $testimonial->comment }}
            </x-ui.text>
        </div>

        <x-ui.separator />

        {{-- Author --}}
        <footer class="flex items-center gap-3">
            <x-ui.avatar
                :name="$testimonial->user->name"
                size="sm"
                color="auto"
                circle
            />
            <div class="min-w-0 flex-1">
                <x-ui.text class="truncate text-sm font-semibold">
                    {{ $testimonial->user->name }}
                </x-ui.text>
                @if ($testimonial->project)
                    <x-ui.text class="truncate text-xs text-neutral-500 dark:text-neutral-500">
                        # {{ $testimonial->project->title }}
                    </x-ui.text>
                @endif
            </div>
        </footer>
    </div>
</blockquote>
