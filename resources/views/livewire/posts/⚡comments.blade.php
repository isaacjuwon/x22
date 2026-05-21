<?php

use App\Models\Post;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public Post $post;

    #[Validate('required|string|max:1000')]
    public string $comment = '';

    public function submit(): void
    {
        $this->validate();

        // TODO: Uncomment when Comment model is ready
        // Comment::create([
        //     'post_id' => $this->post->id,
        //     'user_id' => auth()->id(),
        //     'content' => $this->comment,
        // ]);

        $this->comment = '';
        $this->dispatch('notify', type: 'success', content: __('Comment added successfully.'));
        $this->dispatch('comment-added');
    }
};
?>

<div class="space-y-6">

    {{-- Comment form --}}
    @auth
        <form wire:submit="submit" class="space-y-4 rounded-xl border border-neutral-200 bg-neutral-50 p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <x-ui.field>
                <x-ui.label>{{ __('Your comment') }}</x-ui.label>
                <x-ui.textarea
                    wire:model="comment"
                    placeholder="{{ __('Share your thoughts...') }}"
                    rows="4"
                />
                <x-ui.error name="comment" />
            </x-ui.field>

            <div class="flex justify-end">
                <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Post comment') }}</span>
                    <span wire:loading>{{ __('Posting...') }}</span>
                </x-ui.button>
            </div>
        </form>
    @else
        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/30">
            <x-ui.text>
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:underline dark:text-blue-400">
                    {{ __('Sign in') }}
                </a>
                {{ __('to leave a comment.') }}
            </x-ui.text>
        </div>
    @endauth

    {{-- Comments list --}}
    <div class="space-y-4">
        <x-ui.text class="font-semibold">{{ __('Comments') }}</x-ui.text>

        {{-- TODO: Render comments when Comment model is ready --}}
        <x-ui.empty>
            <x-ui.empty.media>
                <x-ui.icon name="chat-bubble-left-right" class="h-10 w-10 text-neutral-300 dark:text-neutral-600" />
            </x-ui.empty.media>
            <x-ui.empty.contents>
                <x-ui.text class="text-neutral-400">{{ __('No comments yet. Be the first to comment!') }}</x-ui.text>
            </x-ui.empty.contents>
        </x-ui.empty>
    </div>

</div>
