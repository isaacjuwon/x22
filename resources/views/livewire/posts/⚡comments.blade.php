<?php

use App\Models\Comment;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public Post $post;

    #[Validate('required|string|max:1000')]
    public string $comment = '';
    public ?int $replyTo = null;

    public function setReply(int $commentId): void
    {
        $this->replyTo = $commentId;
        $this->dispatch('focus-comment-input');
    }

    public function cancelReply(): void
    {
        $this->replyTo = null;
    }

    public function submit(): void
    {
        $this->validate();

        Comment::create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'parent_id' => $this->replyTo,
            'content' => $this->comment,
        ]);

        $this->comment = '';
        $this->replyTo = null;
        $this->dispatch('notify', type: 'success', content: __('Comment added successfully.'));
        $this->dispatch('comment-added');
    }

    #[Computed]
    #[On('comment-added')]
    public function comments()
    {
        return $this->post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'replies' => function($q) { $q->oldest(); }])
            ->latest()
            ->get();
    }
};
?>

<div class="space-y-6">

    {{-- Comment form --}}
    @auth
        <form wire:submit="submit" id="comment-form" x-on:focus-comment-input.window="$refs.commentInput.focus()" class="space-y-4 rounded-xl border border-neutral-200 bg-neutral-50 p-6 dark:border-neutral-700 dark:bg-neutral-900">
            @if ($replyTo)
                <div class="flex items-center justify-between rounded-lg bg-neutral-200/50 p-3 text-sm dark:bg-neutral-800/50">
                    <x-ui.text class="text-neutral-600 dark:text-neutral-400">
                        {{ __('Replying to a comment') }}
                    </x-ui.text>
                    <button type="button" wire:click="cancelReply" class="text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300">
                        <x-ui.icon name="x-mark" class="h-4 w-4" />
                    </button>
                </div>
            @endif

            <x-ui.field>
                <x-ui.label>{{ __('Your comment') }}</x-ui.label>
                <x-ui.textarea
                    x-ref="commentInput"
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

        @forelse ($this->comments as $comment)
            <div class="flex gap-4 rounded-xl border border-neutral-100 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900/50">
                <x-ui.avatar :name="$comment->user->name" size="sm" color="auto" class="shrink-0" />
                <div class="flex-1 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-ui.text class="font-medium text-neutral-800 dark:text-neutral-200">
                                {{ $comment->user->name }}
                            </x-ui.text>
                            <x-ui.text class="text-xs text-neutral-500">
                                {{ $comment->created_at->diffForHumans() }}
                            </x-ui.text>
                        </div>
                        @auth
                            <button type="button" wire:click="setReply({{ $comment->id }})" class="text-xs font-medium text-blue-600 hover:underline dark:text-blue-400">
                                {{ __('Reply') }}
                            </button>
                        @endauth
                    </div>
                    <div class="prose prose-sm dark:prose-invert max-w-none text-neutral-600 dark:text-neutral-400">
                        {{ $comment->content }}
                    </div>

                    {{-- Replies --}}
                    @if ($comment->replies->isNotEmpty())
                        <div class="mt-4 space-y-4 border-l-2 border-neutral-100 pl-4 dark:border-neutral-800">
                            @foreach ($comment->replies as $reply)
                                <div class="flex gap-3">
                                    <x-ui.avatar :name="$reply->user->name" size="xs" color="auto" class="shrink-0 mt-1" />
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <x-ui.text class="text-sm font-medium text-neutral-800 dark:text-neutral-200">
                                                {{ $reply->user->name }}
                                            </x-ui.text>
                                            <x-ui.text class="text-xs text-neutral-500">
                                                {{ $reply->created_at->diffForHumans() }}
                                            </x-ui.text>
                                        </div>
                                        <div class="prose prose-sm dark:prose-invert max-w-none text-neutral-600 dark:text-neutral-400">
                                            {{ $reply->content }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <x-ui.empty>
                <x-ui.empty.media>
                    <x-ui.icon name="chat-bubble-left-right" class="h-10 w-10 text-neutral-300 dark:text-neutral-600" />
                </x-ui.empty.media>
                <x-ui.empty.contents>
                    <x-ui.text class="text-neutral-400">{{ __('No comments yet. Be the first to comment!') }}</x-ui.text>
                </x-ui.empty.contents>
            </x-ui.empty>
        @endforelse
    </div>

</div>
