@props(['project'])

<article data-slot="card" class="group flex flex-col gap-6 border-none bg-transparent">
    {{-- Project Image --}}
    @if ($project->featuredImageUrl('card'))
        <div class="aspect-[16/10] overflow-hidden rounded-[2rem] bg-neutral-900 p-1 transition-all">
            <img
                src="{{ $project->featuredImageUrl('card') }}"
                alt="{{ $project->title }}"
                class="h-full w-full object-cover rounded-[1.8rem] transition-all duration-500 group-hover:scale-105"
                loading="lazy"
            />
        </div>
    @endif

    <div class="flex flex-col gap-4 px-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-neutral-400">
                <x-ui.icon name="folder" class="size-4" />
                <span class="text-xs uppercase tracking-widest font-bold">{{ $project->category ?? __('Production') }}</span>
            </div>
            <x-ui.badge variant="outline" class="text-[10px] font-bold border-primary/30 text-primary rounded-full h-6 px-3 bg-primary/5">
                {{ __('ACTIVE') }}
            </x-ui.badge>
        </div>

        <h3 class="text-2xl font-bold tracking-tight text-white group-hover:text-primary transition-colors leading-tight">
            <a href="{{ route('projects.show', $project) }}" wire:navigate>
                {{ $project->title }}
            </a>
        </h3>

        @isset($project->description)
            <p class="text-neutral-400 line-clamp-2 text-sm leading-relaxed">
                {{ $project->description }}
            </p>
        @endisset

        <div class="pt-2 flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-500 group-hover:text-primary transition-colors">
                {{ __('Explore Case Study') }}
            </span>
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-neutral-900 text-neutral-500 transition-all group-hover:bg-primary group-hover:text-white">
                <x-ui.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
            </div>
        </div>
    </div>
</article>

