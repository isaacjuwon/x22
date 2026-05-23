<?php

use App\Ai\Agents\ReadingAssistance;
use App\Models\Post;
use Livewire\Component;

new class extends Component {
    public Post $post;
    public string $summary = '';
    public bool $isGenerating = false;
    public bool $failed = false;

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function summarize(): void
    {
        $this->isGenerating = true;

        try {
            $prompt = "Please provide a concise, 2-3 sentence summary of the following blog post:\n\nTitle: {$this->post->title}\n\nContent: " . strip_tags($this->post->content);

            $response = (new ReadingAssistance)->prompt($prompt);
            $this->summary = $response->text;
        } catch (\Exception $e) {
            $this->failed = true;
        }

        $this->isGenerating = false;
    }
};
?>

@if (! $failed)
    <div wire:init="summarize">
        <x-ui.alerts variant="info" icon="sparkles" class="mb-8">
            <x-ui.alerts.heading>{{ __('AI Summary') }}</x-ui.alerts.heading>

            @if ($isGenerating)
                <div class="mt-2 space-y-2">
                    <x-ui.skeleton class="h-4 w-full" />
                    <x-ui.skeleton class="h-4 w-5/6" />
                    <x-ui.skeleton class="h-4 w-4/6" />
                </div>
            @else
                <x-ui.alerts.description>{{ $summary }}</x-ui.alerts.description>
            @endif
        </x-ui.alerts>
    </div>
@endif
