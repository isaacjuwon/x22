<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\AnalyticsDatesController;
use App\Http\Controllers\AnalyticsDownloadController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\FaviconController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\MarkdownResponse;
use App\Http\Middleware\TrackPageView;
use App\Services\PageService;
use App\Support\UrlGenerator;







$basePrefix = UrlGenerator::basePrefix();

Route::prefix($basePrefix)->group(function (): void {

    // Sitemap
    if (config('pergament.sitemap.enabled', true)) {
        Route::get('sitemap.xml', SitemapController::class)->name('pergament.sitemap');
    }

    // Robots.txt
    if (config('pergament.robots.enabled', true)) {
        Route::get('robots.txt', [RobotsController::class, 'robots'])->name('pergament.robots');
    }

    // LLMs.txt
    if (config('pergament.llms.enabled', true)) {
        Route::get('llms.txt', [RobotsController::class, 'llms'])->name('pergament.llms');
    }

    // Favicon — served from the content directory unless an absolute URL is configured
    $favicon = config('pergament.favicon');
    if (is_string($favicon) && $favicon !== '' && ! Str::startsWith($favicon, ['http://', 'https://', '//'])) {
        Route::get(basename($favicon), [FaviconController::class, 'show'])->name('pergament.favicon');
    }

    // PWA
    if (config('pergament.pwa.enabled', false)) {
        Route::get('manifest.json', [PwaController::class, 'manifest'])->name('pergament.manifest');
        Route::get('sw.js', [PwaController::class, 'serviceWorker'])->name('pergament.sw');
    }

    // Analytics endpoints — always registered when analytics is enabled, gated by token + download.enabled inside controllers
    if (config('pergament.analytics.enabled', false)) {
        Route::get('analytics/download', AnalyticsDownloadController::class)->name('pergament.analytics.download');
        Route::get('analytics/dates', AnalyticsDatesController::class)->name('pergament.analytics.dates');
    }

    // Search
    if (config('pergament.search.enabled', true)) {
        Route::get('search', SearchController::class)->name('pergament.search');
    }

    // Blog
    if (config('pergament.blog.enabled', true)) {
        $blogPrefix = config('pergament.blog.url_prefix', 'blog');

        Route::prefix($blogPrefix)->name('pergament.blog.')->group(function (): void {
            // Feed and media are not HTML pages — skip markdown middleware
            if (config('pergament.blog.feed.enabled', true)) {
                Route::get('feed', FeedController::class)->name('feed');
            }

            Route::get('media/{slug}/{filename}', [BlogController::class, 'media'])
                ->where('filename', '.*')
                ->name('media');

            Route::get('/', [BlogController::class, 'index'])->middleware(TrackPageView::class)->name('index');
            // Content pages — serve as markdown when requested
            Route::middleware([MarkdownResponse::class, TrackPageView::class])->group(function (): void {
                Route::get('category/{category}', [BlogController::class, 'category'])->name('category');
                Route::get('category/{category}.md', [BlogController::class, 'category'])->name('category.md');
                Route::get('tag/{tag}', [BlogController::class, 'tag'])->name('tag');
                Route::get('tag/{tag}.md', [BlogController::class, 'tag'])->name('tag.md');
                Route::get('author/{author}', [BlogController::class, 'author'])->name('author');
                Route::get('author/{author}.md', [BlogController::class, 'author'])->name('author.md');
                Route::get('{slug}', [BlogController::class, 'show'])->name('show');
                Route::get('{slug}.md', [BlogController::class, 'show'])->name('show.md');
            });
        });
    }

    // Documentation
    if (config('pergament.docs.enabled', true)) {
        $docsPrefix = config('pergament.docs.url_prefix', 'docs');

        Route::prefix($docsPrefix)->name('pergament.docs.')->group(function (): void {
            // Media files are not HTML pages — skip markdown middleware
            Route::get('media/{path}', [DocumentationController::class, 'media'])
                ->where('path', '.*')
                ->name('media');

            Route::get('/', [DocumentationController::class, 'index'])->middleware(TrackPageView::class)->name('index');
            // Content pages — serve as markdown when requested
            Route::middleware([MarkdownResponse::class, TrackPageView::class])->group(function (): void {
                Route::get('{chapter}/{page}', [DocumentationController::class, 'show'])->name('show');
                Route::get('{chapter}/{page}.md', [DocumentationController::class, 'show'])->name('show.md');
            });
        });
    }

    // Homepage and standalone pages — serve as markdown when requested
    Route::middleware([MarkdownResponse::class, TrackPageView::class])->group(function (): void {
        Route::get('/', HomeController::class)->name('pergament.home');
        Route::get('/index.md', HomeController::class)->name('pergament.home.md');

        if (config('pergament.pages.enabled', true)) {
            /** @var PageService $pageService */
            $pageService = resolve(PageService::class);

            Route::get('{slug}', PageController::class)
                ->whereIn('slug', $pageService->getSlugs()->toArray())
                ->name('pergament.page');
            Route::get('{slug}.md', PageController::class)
                ->whereIn('slug', $pageService->getSlugs()->toArray())
                ->name('pergament.page.md');
        }
    });
});
