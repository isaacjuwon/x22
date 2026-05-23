<?php

use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

Route::get('/posts', fn () => view('posts.index'))->name('posts.index');

Route::view('/projects', 'projects.index')->name('projects.index');

Route::view('/projects/{project}', 'projects.show')->name('projects.show');

Route::get('/testimonials', function () {
    $testimonials = Testimonial::with(['user', 'project'])->where('status', 'approved')->latest()->paginate(12);

    return view('testimonials.index', compact('testimonials'));
})->name('testimonials.index');

Route::get('/posts/{post:slug}', function (Post $post) {
    return view('posts.show', compact('post'));
})->name('posts.show');

Route::get('/tags/{tag:slug}', function (Tag $tag) {
    return view('tags.show', compact('tag'));
})->name('tags.show');

Route::get('/page/{page:slug}', function (Page $page) {
    abort_if($page->status->value !== 'published', 404);

    return view('pages.show', compact('page'));
})->name('pages.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::livewire('/posts', 'admin::posts.index')->name('posts.index');
    Route::livewire('/posts/create', 'admin::posts.create')->name('posts.create');
    Route::livewire('/posts/{post}/edit', 'admin::posts.edit')->name('posts.edit');

    Route::livewire('/projects', 'admin::projects.index')->name('projects.index');
    Route::livewire('/projects/create', 'admin::projects.create')->name('projects.create');
    Route::livewire('/projects/{project}/edit', 'admin::projects.edit')->name('projects.edit');

    Route::livewire('/team', 'admin::team.index')->name('team.index');
    Route::livewire('/team/create', 'admin::team.create')->name('team.create');
    Route::livewire('/team/{teamMember}/edit', 'admin::team.edit')->name('team.edit');

    Route::livewire('/testimonials', 'admin::testimonials.index')->name('testimonials.index');
    Route::livewire('/testimonials/create', 'admin::testimonials.create')->name('testimonials.create');
    Route::livewire('/testimonials/{testimonial}/edit', 'admin::testimonials.edit')->name('testimonials.edit');

    Route::livewire('/pages', 'admin::pages.index')->name('pages.index');
    Route::livewire('/pages/create', 'admin::pages.create')->name('pages.create');
    Route::livewire('/pages/{page}/edit', 'admin::pages.edit')->name('pages.edit');

    Route::redirect('/settings', '/admin/settings/general');
    Route::livewire('/settings/general', 'admin::settings.general')->name('settings.general');
    Route::livewire('/settings/seo', 'admin::settings.seo')->name('settings.seo');
    Route::livewire('/settings/social', 'admin::settings.social')->name('settings.social');
});

require __DIR__.'/settings.php';
