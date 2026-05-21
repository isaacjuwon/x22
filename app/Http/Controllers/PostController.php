<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('posts.index');
    }

    public function show(Post $post): View
    {
        abort_unless($post->scopePublished(Post::query())->where('id', $post->id)->exists(), 404);

        $post->load('tags', 'user');
        $post->incrementViewCount();

        $tagIds = $post->tags->pluck('id');

        $relatedPosts = Post::published()
            ->whereNot('id', $post->id)
            ->when($tagIds->isNotEmpty(), fn ($q) => $q->whereHas(
                'tags',
                fn ($q) => $q->whereIn('tags.id', $tagIds)
            ))
            ->latest('published_at')
            ->limit(2)
            ->get();

        if ($relatedPosts->isEmpty()) {
            $relatedPosts = Post::published()
                ->whereNot('id', $post->id)
                ->latest('published_at')
                ->limit(2)
                ->get();
        }

        $previousPost = Post::published()
            ->where('published_at', '<', $post->published_at)
            ->latest('published_at')
            ->first();

        $nextPost = Post::published()
            ->where('published_at', '>', $post->published_at)
            ->oldest('published_at')
            ->first();

        return view('posts.show', compact('post', 'relatedPosts', 'previousPost', 'nextPost'));
    }
}
