<x-layouts::main :title="$page->title">
    <div class="mx-auto max-w-4xl px-6 py-16">
        <div class="mb-12 border-b border-neutral-200 dark:border-neutral-800 pb-8">
            <x-ui.heading level="h1" size="3xl" class="text-neutral-800 dark:text-neutral-100">
                {{ $page->title }}
            </x-ui.heading>
            @if($page->excerpt)
                <p class="mt-4 text-xl text-neutral-600 dark:text-neutral-400">
                    {{ $page->excerpt }}
                </p>
            @endif
        </div>

        <div class="prose prose-neutral dark:prose-invert max-w-none">
            {!! $page->content !!}
        </div>
    </div>
</x-layouts::main>
