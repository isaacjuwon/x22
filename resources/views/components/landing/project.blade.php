@props(['project'])

<article data-slot="card" class="group flex flex-col gap-6">
    {{-- Project Image --}}
    @if ($project->featuredImageUrl('card'))
        <div class="aspect-[16/10] overflow-hidden rounded-[1.2rem] bg-neutral-100 p-1 transition-all">
            <img
                src="{{ $project->featuredImageUrl('card') }}"
                alt="{{ $project->title }}"
                class="h-full w-full object-cover rounded-[0.9rem] grayscale group-hover:grayscale-0 transition-all duration-500"
                loading="lazy"
            />
        </div>
    @endif

    <div class="flex flex-col gap-4 px-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-skeleton-meta">
                <x-ui.icon name="ps:folder" class="size-4" />
                <span>{{ $project->category ?? __('Production') }}</span>
            </div>
            <x-ui.badge variant="outline" class="text-[9px] font-bold border-neutral-200 rounded-full h-5 px-2">
                {{ __('ACTIVE') }}
            </x-ui.badge>
        </div>

        <h3 class="text-skeleton-title group-hover:text-primary transition-colors leading-tight">
            <a href="{{ route('projects.show', $project) }}" wire:navigate class="hover:underline decoration-1 underline-offset-4">
                {{ $project->title }}
            </a>
        </h3>

        @isset($project->description)
            <p class="text-skeleton-body line-clamp-3">
                {{ $project->description }}
            </p>
        @endisset

        <div class="pt-4 flex items-center justify-between">
            <span class="text-skeleton-meta group-hover:text-primary transition-colors">
                {{ __('Explore Case Study') }}
            </span>
            <x-ui.icon name="ps:arrow-right" class="size-4 text-neutral-300 transition-transform group-hover:translate-x-1" />
        </div>
    </div>
</article>
