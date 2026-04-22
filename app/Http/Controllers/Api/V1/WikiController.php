<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WikiPage;
use Illuminate\Http\Request;

class WikiController extends Controller
{
    /**
     * Get all published wiki pages with optional search.
     */
    public function index(Request $request)
    {
        $query = WikiPage::where('is_published', true);

        if ($request->has('search') && !empty($request->search)) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('title', 'LIKE', "%{$s}%")
                  ->orWhere('content', 'LIKE', "%{$s}%");
            });
        }

        $pages = $query->orderBy('category')->orderBy('title')->get();

        return response()->json([
            'pages' => $pages
        ]);
    }

    /**
     * Get a specific wiki page by slug.
     */
    public function show($slug)
    {
        $page = WikiPage::where('is_published', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'page' => $page
        ]);
    }
}
