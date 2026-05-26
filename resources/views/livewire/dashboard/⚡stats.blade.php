<?php

use Livewire\Component;
use App\Models\Post;
use App\Models\Project;
use App\Models\Comment;
use App\Models\Testimonial;
use Illuminate\Support\Facades\DB;

new class extends Component {


    public function render()
    {
        return $this->view([
            'totalPosts' => Post::count(),
            'totalProjects' => Project::count(),
            'totalViews' => Post::sum('view_count'),
            'recentComments' => Comment::with('post', 'user')->latest()->limit(5)->get(),
            'totalTestimonials' => Testimonial::count(),
            'viewsTrend' => $this->getViewsTrend(),
        ]);
    }

    private function getViewsTrend(): array
    {
        // Simple mock trend data or real query if needed
        return [12, 18, 15, 25, 32, 28, 45];
    }
}; ?>

<div class="space-y-8">
    {{-- Stats Grid --}}
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        {{-- Total Views --}}
        <div data-slot="card" class="flex flex-col gap-2 !p-6">
            <div class="flex items-center justify-between">
                <span class="text-skeleton-meta">{{ __('Total Views') }}</span>
                <x-ui.icon name="ps:eye" class="size-4 text-primary" />
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-neutral-950">{{ number_format($totalViews) }}</span>
                <span class="text-xs font-medium text-success">+12.5%</span>
            </div>
        </div>

        {{-- Total Posts --}}
        <div data-slot="card" class="flex flex-col gap-2 !p-6">
            <div class="flex items-center justify-between">
                <span class="text-skeleton-meta">{{ __('Articles') }}</span>
                <x-ui.icon name="ps:article" class="size-4 text-primary" />
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-neutral-950">{{ $totalPosts }}</span>
                <span class="text-xs font-medium text-neutral-400">{{ __('Published') }}</span>
            </div>
        </div>

        {{-- Total Projects --}}
        <div data-slot="card" class="flex flex-col gap-2 !p-6">
            <div class="flex items-center justify-between">
                <span class="text-skeleton-meta">{{ __('Projects') }}</span>
                <x-ui.icon name="ps:folder" class="size-4 text-primary" />
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-neutral-950">{{ $totalProjects }}</span>
                <span class="text-xs font-medium text-neutral-400">{{ __('Case Studies') }}</span>
            </div>
        </div>

        {{-- Testimonials --}}
        <div data-slot="card" class="flex flex-col gap-2 !p-6">
            <div class="flex items-center justify-between">
                <span class="text-skeleton-meta">{{ __('Feedback') }}</span>
                <x-ui.icon name="ps:chat-teardrop-dots" class="size-4 text-primary" />
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-neutral-950">{{ $totalTestimonials }}</span>
                <span class="text-xs font-medium text-neutral-400">{{ __('Approved') }}</span>
            </div>
        </div>
    </div>

    {{-- Detailed Section --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Recent Comments --}}
        <div data-slot="card" class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between border-b border-neutral-200 pb-4">
                <h3 class="font-bold text-neutral-900">{{ __('Recent Interactions') }}</h3>
                <x-ui.button variant="ghost" size="xs">{{ __('View All') }}</x-ui.button>
            </div>

            <div class="space-y-4">
                @forelse($recentComments as $comment)
                    <div class="flex items-start gap-4 p-3 rounded-xl hover:bg-neutral-200/50 transition-colors">
                        <x-ui.avatar :name="$comment->user->name" size="sm" class="rounded-lg" />
                        <div class="flex-1 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-neutral-900">{{ $comment->user->name }}</span>
                                <span class="text-[10px] text-neutral-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-neutral-600 line-clamp-1">
                                {{ $comment->content }}
                            </p>
                            <div class="text-[10px] text-neutral-400 flex items-center gap-1">
                                <span>{{ __('on') }}</span>
                                <span class="font-medium text-primary line-clamp-1">{{ $comment->post->title }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <x-ui.icon name="ps:chat-centered-dots" class="size-10 mx-auto text-neutral-200 mb-3" />
                        <p class="text-sm text-neutral-400">{{ __('No recent comments yet.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Actions --}}
        <div data-slot="card" class="space-y-6">
            <div class="flex items-center justify-between border-b border-neutral-200 pb-4">
                <h3 class="font-bold text-neutral-900">{{ __('Quick Actions') }}</h3>
            </div>

            <div class="grid gap-3">
                <x-ui.button as="a" href="{{ route('admin.posts.create') }}" variant="outline" class="justify-start !h-12">
                    <x-ui.icon name="ps:plus-circle" class="size-4 mr-3" />
                    {{ __('New Blog Post') }}
                </x-ui.button>
                <x-ui.button as="a" href="{{ route('admin.projects.create') }}" variant="outline" class="justify-start !h-12">
                    <x-ui.icon name="ps:folder-plus" class="size-4 mr-3" />
                    {{ __('Add Project') }}
                </x-ui.button>
                <x-ui.button as="a" href="{{ route('admin.settings.general') }}" variant="outline" class="justify-start !h-12">
                    <x-ui.icon name="ps:gear" class="size-4 mr-3" />
                    {{ __('Site Settings') }}
                </x-ui.button>
            </div>

            <div class="pt-6 border-t border-neutral-200">
                <div class="rounded-2xl bg-primary/5 p-4 border border-primary/10">
                    <p class="text-xs font-bold text-primary uppercase tracking-widest mb-2">{{ __('Pro Tip') }}</p>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        {{ __('Use the AI Assistant in the post editor to help generate summaries and metadata automatically.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
