<?php

use Livewire\Component;
use App\Models\Post;
use Laravel\Ai\Facades\Ai;

new class extends Component
{
    public Post $post;
    public string $summary = '';
    public bool $isGenerating = false;

    public function summarize()
    {
        $this->isGenerating = true;

        $prompt = "Summarize the following article in a 3-bullet point TL;DR:\n\n" . strip_tags($this->post->content);

        $this->summary = Ai::ask($prompt, function ($partial) {
            $this->stream(to: 'summary', content: $partial);
        });

        $this->isGenerating = false;
    }
};
?>

<div class="mb-8 p-6 bg-neutral-100 dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
            <x-ui.icon name="sparkles" class="w-5 h-5 text-blue-500" />
            AI Summary
        </h3>
        @if(! $summary && ! $isGenerating)
            <x-ui.button wire:click="summarize" size="sm" variant="outline">
                Generate TL;DR
            </x-ui.button>
        @endif
    </div>
    
    @if($isGenerating && ! $summary)
        <div class="text-sm text-neutral-500 flex items-center gap-2">
            <x-ui.icon name="arrow-path" class="w-4 h-4 animate-spin" />
            Analyzing article...
        </div>
    @endif

    @if($summary || $isGenerating)
        <div class="prose prose-sm dark:prose-invert">
            <div wire:stream="summary" class="whitespace-pre-line">{{ $summary }}</div>
        </div>
    @endif
</div>