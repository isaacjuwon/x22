<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\SearchService;
use App\Services\SeoService;

final class SearchController
{
    public function __invoke(Request $request, SearchService $searchService, SeoService $seoService): View|JsonResponse
    {
        $query = mb_trim((string) $request->query('q', ''));
        $results = $query !== '' ? $searchService->search($query) : collect();

        if ($request->wantsJson()) {
            if ($query === '') {
                $results = $searchService->suggestions();
            }

            return response()->json($results->values());
        }

        $seo = $seoService->resolve([], 'Search');

        return view('search.results', [
            'query' => $query,
            'results' => $results,
            'seo' => $seo,
        ]);
    }
}
