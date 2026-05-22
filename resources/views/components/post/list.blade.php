<div class="space-y-6">
    <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $heading ?? 'Latest Posts' }}</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($posts as $post)
            <x-post.card :post="$post" />
        @empty
            <div class="col-span-full">
                <x-x-ui.:empty>
                    <x-slot name="icon">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.248 6.253 2 10.501 2 15.5S6.248 24.747 12 24.747s10-4.248 10-9.247S17.752 6.253 12 6.253z" />
                        </svg>
                    </x-slot>
                    No posts found
                </x-x-ui.:empty>
            </div>
        @endforelse
    </div>
</div>
