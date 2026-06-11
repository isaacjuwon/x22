<?php

use App\Ai\Agents\WritingAssistance;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public string $prompt = '';
    public array $history = [];
    public bool $isOpen = false;
    public bool $isGenerating = false;

    public function toggle(): void
    {
        $this->isOpen = !$this->isOpen;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function submit(): void
    {
        if (empty(trim($this->prompt))) {
            return;
        }

        $userPrompt = $this->prompt;
        $this->prompt = '';

        $this->history[] = [
            'role' => 'user',
            'content' => $userPrompt,
        ];

        $this->isGenerating = true;

        try {
            $agent = new WritingAssistance();

            $context = collect($this->history)->map(fn($h) => $h['role'] . ': ' . $h['content'])->join("\n");

            $response = $agent->prompt($context);

            $this->history[] = [
                'role' => 'assistant',
                'content' => $response->text,
            ];
        } catch (\Exception $e) {
            $this->history[] = [
                'role' => 'assistant',
                'content' => __('Sorry, I encountered an error while generating a response.'),
            ];
        }

        $this->isGenerating = false;
    }

    #[On('ai-action')]
    public function handleInlineAction(string $action, string $content = ''): void
    {
        $this->isOpen = true;

        if ($action === 'improve') {
            $this->prompt = "Please improve the following text to make it sound more professional:\n\n" . $content;
            $this->submit();
        }
    }
};
?>

<div>
    {{-- Floating Toggle Button --}}
    <x-ui.button
        wire:click="toggle"
        :icon="$isOpen ? 'x-mark' : 'sparkles'"
        color="blue"
        variant="primary"
        size="lg"
        class="fixed bottom-6 right-6 z-50 !rounded-full !w-14 !h-14 shadow-xl"
        aria-label="{{ $isOpen ? __('Close AI assistant') : __('Open AI assistant') }}"
    />

    {{-- Sliding Sidebar Panel --}}
    <div
        class="fixed inset-y-0 right-0 z-40 w-96 transform border-l border-neutral-200 bg-white shadow-2xl transition-transform duration-300 ease-in-out dark:border-neutral-800 dark:bg-neutral-900 {{ $isOpen ? 'translate-x-0' : 'translate-x-full' }}"
    >
        <div class="flex h-full flex-col">
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-4">
                <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400">
                    <x-ui.icon name="sparkles" class="h-5 w-5" />
                    <x-ui.text class="font-semibold text-neutral-800 dark:text-neutral-100">
                        {{ __('AI Writing Assistant') }}
                    </x-ui.text>
                </div>
                <x-ui.button wire:click="close" variant="ghost" icon="x-mark" size="sm" />
            </div>

            <x-ui.separator />

            {{-- Chat History --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                @if (empty($history))
                    <x-ui.empty class="h-full">
                        <x-ui.empty.media>
                            <x-ui.icon name="chat-bubble-left-ellipsis" class="h-10 w-10 opacity-40" />
                        </x-ui.empty.media>
                        <x-ui.empty.contents>
                            <x-ui.text class="text-sm text-neutral-500">
                                {{ __('How can I help you write today?') }}
                            </x-ui.text>
                        </x-ui.empty.contents>
                    </x-ui.empty>
                @else
                    @foreach ($history as $msg)
                        <div @class([
                            'max-w-[85%] rounded-2xl px-4 py-3 text-sm',
                            'ml-auto bg-blue-100 text-blue-900 dark:bg-blue-900/50 dark:text-blue-100' => $msg['role'] === 'user',
                            'mr-auto bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-200' => $msg['role'] === 'assistant',
                        ])>
                            {{ $msg['content'] }}
                        </div>
                    @endforeach
                @endif

                @if ($isGenerating)
                    <div class="mr-auto max-w-[85%] space-y-2 rounded-2xl bg-neutral-100 px-4 py-3 dark:bg-neutral-800">
                        <x-ui.skeleton class="h-3 w-full" />
                        <x-ui.skeleton class="h-3 w-4/5" />
                        <x-ui.skeleton class="h-3 w-3/5" />
                    </div>
                @endif
            </div>

            <x-ui.separator />

            {{-- Input Form --}}
            <div class="p-4">
                <form wire:submit="submit" class="flex flex-col gap-2">
                    <x-ui.textarea
                        wire:model="prompt"
                        placeholder="{{ __('Ask for ideas, grammar checks, improvements...') }}"
                        rows="3"
                        resize="none"
                        wire:keydown.ctrl.enter="submit"
                    />
                    <div class="flex items-center justify-between">
                        <x-ui.text class="text-xs text-neutral-400">
                            {{ __('Ctrl + Enter to send') }}
                        </x-ui.text>
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            color="blue"
                            size="sm"
                            icon="paper-airplane"
                            wire:loading.attr="disabled"
                        >
                            {{ __('Send') }}
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
