<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WikiCategory;
use App\Models\WikiPage;
use Illuminate\Http\Request;

class WikiController extends Controller
{
    /**
     * Get all published wiki pages grouped by category.
     */
    public function index(Request $request)
    {
        $query = WikiPage::where('is_published', true)->with('category');

        if ($request->has('search') && !empty($request->search)) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('title', 'LIKE', "%{$s}%")
                  ->orWhere('content', 'LIKE', "%{$s}%");
            });
            
            return response()->json([
                'pages' => $query->get()
            ]);
        }

        $categories = WikiCategory::with(['pages' => function($q) {
                $q->where('is_published', true)->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        $standalonePages = WikiPage::where('is_published', true)
            ->whereNull('wiki_category_id')
            ->orderBy('order')
            ->get();

        // Return a flat list of pages as well for backward compatibility (e.g., WikiDetail side nav)
        $allPages = WikiPage::where('is_published', true)
            ->with('category')
            ->orderBy('wiki_category_id')
            ->orderBy('order')
            ->get();

        return response()->json([
            'categories' => $categories,
            'standalone_pages' => $standalonePages,
            'pages' => $allPages // Compatibility fix
        ]);
    }

    /**
     * Get a specific wiki page by slug.
     */
    public function show($slug)
    {
        $page = WikiPage::where('is_published', true)
            ->where('slug', $slug)
            ->with('category')
            ->firstOrFail();

        return response()->json([
            'page' => $page
        ]);
    }
}
