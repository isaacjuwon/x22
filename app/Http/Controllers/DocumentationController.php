<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use App\Services\DocumentationService;
use App\Services\SeoService;
use App\Support\MarkdownExporter;
use App\Support\UrlGenerator;

final class DocumentationController
{
    public function index(DocumentationService $service): RedirectResponse
    {
        $first = $service->getFirstPage();

        if ($first === null) {
            abort(404);
        }

        $docsPrefix = config('pergament.docs.url_prefix', 'docs');

        return redirect(UrlGenerator::path($docsPrefix, $first['chapter'], $first['page']));
    }

    public function show(
        Request $request,
        string $chapter,
        string $page,
        DocumentationService $service,
        SeoService $seoService,
        MarkdownExporter $exporter,
    ): View|Response {
        if (str_ends_with($page, '.md')) {
            $page = substr($page, 0, -3);
        }

        if ($request->attributes->get('pergament.wants_raw_markdown')) {
            $pageData = $service->getRenderedPage($chapter, $page);
            abort_unless($pageData !== null, 404);

            return new Response(
                $exporter->fromHtml($pageData['htmlContent'], $pageData['title']),
                200,
                ['Content-Type' => 'text/markdown; charset=UTF-8'],
            );
        }

        $pageData = $service->getRenderedPage($chapter, $page);

        abort_unless($pageData !== null, 404);

        $docsPrefix = config('pergament.docs.url_prefix', 'docs');
        $canonicalUrl = UrlGenerator::url($docsPrefix, $chapter, $page);
        $seo = $seoService->resolve($pageData['meta'], $pageData['title'], $canonicalUrl);

        return view('docs.show', [
            'page' => $pageData,
            'navigation' => $service->getNavigation(),
            'currentChapter' => $chapter,
            'currentPage' => $page,
            'seo' => $seo,
        ]);
    }

    public function media(string $path, DocumentationService $service): Response
    {
        $filePath = $service->resolveMediaPath($path);

        abort_unless($filePath !== null, 404);

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        return response(file_get_contents($filePath), 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
