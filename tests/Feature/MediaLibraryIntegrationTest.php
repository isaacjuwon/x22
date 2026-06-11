<?php

use App\Enums\PageStatus;
use App\Enums\PostStatus;
use App\Models\Page;
use App\Models\Post;
use App\Models\SettingAsset;
use App\Models\User;
use App\Settings\SeoSettings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function (): void {
    config()->set('filesystems.default', 'public');
    Storage::fake('public');
});

it('creates a post with featured, gallery, and seo media uploads', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('admin::posts.create')
        ->set('title', 'Media Library Post')
        ->set('excerpt', 'Post excerpt')
        ->set('content', '<p>Body copy</p>')
        ->set('status', PostStatus::Published->value)
        ->set('publishedAt', now()->toDateString())
        ->set('metaDescription', 'Search description')
        ->set('featuredImage', UploadedFile::fake()->image('featured.jpg', 1600, 900))
        ->set('galleryImages', [
            UploadedFile::fake()->image('gallery-one.jpg', 1600, 900),
            UploadedFile::fake()->image('gallery-two.jpg', 1600, 900),
        ])
        ->set('seoOgImage', UploadedFile::fake()->image('seo-og.jpg', 1200, 630))
        ->call('save')
        ->assertHasNoErrors();

    $post = Post::query()->latest('id')->firstOrFail();

    expect($post->getFirstMedia(Post::FEATURED_IMAGE_COLLECTION))->not->toBeNull()
        ->and($post->getFirstMedia(Post::SEO_OG_IMAGE_COLLECTION))->not->toBeNull()
        ->and($post->getMedia(Post::GALLERY_COLLECTION))->toHaveCount(2)
        ->and($post->featuredImageUrl('card'))->not->toBeNull()
        ->and($post->seoOgImageUrl())->not->toBeNull();
});

it('updates page media and removes selected existing gallery items', function (): void {
    $user = User::factory()->create();

    $page = Page::create([
        'user_id' => $user->id,
        'title' => 'About',
        'slug' => 'about',
        'excerpt' => 'Old excerpt',
        'content' => '<p>Old content</p>',
        'status' => PageStatus::Published,
        'published_at' => now(),
        'meta_description' => 'Old description',
        'og_image' => 'https://legacy.example.test/og.jpg',
    ]);

    $page->addMedia(UploadedFile::fake()->image('featured-old.jpg')->getRealPath())
        ->usingFileName('featured-old.jpg')
        ->toMediaCollection(Page::FEATURED_IMAGE_COLLECTION);

    $page->addMedia(UploadedFile::fake()->image('gallery-old-1.jpg')->getRealPath())
        ->usingFileName('gallery-old-1.jpg')
        ->toMediaCollection(Page::GALLERY_COLLECTION);

    $oldGalleryMedia = $page->addMedia(UploadedFile::fake()->image('gallery-old-2.jpg')->getRealPath())
        ->usingFileName('gallery-old-2.jpg')
        ->toMediaCollection(Page::GALLERY_COLLECTION);

    $this->actingAs($user);

    Livewire::test('admin::pages.edit', ['page' => $page])
        ->set('title', 'About us')
        ->set('featuredImage', UploadedFile::fake()->image('featured-new.jpg', 1600, 900))
        ->set('galleryImages', [UploadedFile::fake()->image('gallery-new.jpg', 1600, 900)])
        ->call('removeExistingGalleryMedia', $oldGalleryMedia->id)
        ->call('save')
        ->assertHasNoErrors();

    $page->refresh();

    expect($page->title)->toBe('About us')
        ->and($page->getMedia(Page::GALLERY_COLLECTION))->toHaveCount(2)
        ->and(Media::query()->whereKey($oldGalleryMedia->id)->exists())->toBeFalse()
        ->and($page->featuredImageUrl('card'))->not->toBeNull();
});

it('stores the default seo og image in media and renders it in the shared head metadata', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('admin::settings.seo')
        ->set('meta_title', 'Zic Test Site')
        ->set('seoOgImageUpload', UploadedFile::fake()->image('site-og.jpg', 1200, 630))
        ->call('save')
        ->assertHasNoErrors();

    $asset = SettingAsset::findForKey(SettingAsset::SEO_OG_IMAGE_KEY);
    $ogUrl = $asset?->fileUrl('og');

    expect($asset)->not->toBeNull()
        ->and($ogUrl)->not->toBeNull();

    $seoSettings = app(SeoSettings::class);

    expect($seoSettings->meta_title)->toBe('Zic Test Site');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee($ogUrl, escape: false);
});
