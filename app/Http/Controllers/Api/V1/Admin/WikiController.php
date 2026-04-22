<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\WikiPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WikiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'pages' => WikiPage::orderBy('category')->orderBy('title')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'slug' => 'nullable|string|unique:wiki_pages,slug'
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure uniqueness if slug generated from title exists
            $originalSlug = $validated['slug'];
            $count = 1;
            while (WikiPage::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        $page = WikiPage::create($validated);

        return response()->json([
            'message' => 'Page Wiki créée avec succès.',
            'page' => $page
        ], 210);
    }

    /**
     * Display the specified resource.
     */
    public function show(WikiPage $wiki)
    {
        return response()->json(['page' => $wiki]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WikiPage $wiki)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'slug' => 'required|string|unique:wiki_pages,slug,' . $wiki->id
        ]);

        $wiki->update($validated);

        return response()->json([
            'message' => 'Page Wiki mise à jour.',
            'page' => $wiki
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WikiPage $wiki)
    {
        $wiki->delete();

        return response()->json([
            'message' => 'Page Wiki supprimée.'
        ]);
    }
}
