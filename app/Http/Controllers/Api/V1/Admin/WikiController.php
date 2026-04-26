<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\WikiCategory;
use App\Models\WikiPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class WikiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'categories' => WikiCategory::with(['pages' => function($q) {
                $q->orderBy('order');
            }])->orderBy('order')->get(),
            'standalone_pages' => WikiPage::whereNull('wiki_category_id')->orderBy('order')->get()
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
            'category_name' => 'nullable|string|max:255',
            'wiki_category_id' => 'nullable|exists:wiki_categories,id',
            'is_published' => 'boolean',
            'slug' => 'nullable|string|unique:wiki_pages,slug'
        ]);

        if (!empty($validated['category_name']) && empty($validated['wiki_category_id'])) {
            $category = WikiCategory::firstOrCreate(
                ['name' => $validated['category_name']],
                ['slug' => Str::slug($validated['category_name'])]
            );
            $validated['wiki_category_id'] = $category->id;
        }

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
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
        ], 211);
    }

    /**
     * Display the specified resource.
     */
    public function show(WikiPage $wiki)
    {
        $wiki->load('category');
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
            'category_name' => 'nullable|string|max:255',
            'wiki_category_id' => 'nullable|exists:wiki_categories,id',
            'is_published' => 'boolean',
            'slug' => 'required|string|unique:wiki_pages,slug,' . $wiki->id
        ]);

        if (!empty($validated['category_name'])) {
            $category = WikiCategory::firstOrCreate(
                ['name' => $validated['category_name']],
                ['slug' => Str::slug($validated['category_name'])]
            );
            $validated['wiki_category_id'] = $category->id;
        }

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

    /**
     * Search for categories (AJAX auto-suggest).
     */
    public function searchCategories(Request $request)
    {
        $query = $request->get('q', '');
        if (empty(trim($query))) {
            return response()->json(['categories' => []]);
        }

        $normalizedQuery = Str::slug($query, '');

        $categories = WikiCategory::all()->map(function ($category) use ($normalizedQuery) {
            $normalizedName = Str::slug($category->name, '');
            $category->score = 0;

            if (Str::contains($normalizedName, $normalizedQuery)) {
                $category->score = 100; // Perfect substring match
            } else {
                similar_text($normalizedQuery, $normalizedName, $percent);
                $category->score = $percent;
            }
            return $category;
        })->filter(function ($category) {
            return $category->score > 45; // Flexible threshold for typos
        })->sortByDesc('score')->take(10)->values();

        // Remove the temporary score attribute before returning
        $categories->each->makeHidden('score');

        return response()->json(['categories' => $categories]);
    }

    /**
     * Update category name.
     */
    public function updateCategory(Request $request, WikiCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name'])
        ]);

        return response()->json(['message' => 'Catégorie mise à jour.']);
    }

    /**
     * Delete a category.
     */
    public function destroyCategory(WikiCategory $category)
    {
        // Pages will have their wiki_category_id set to null automatically due to onDelete('set null')
        $category->delete();
        return response()->json(['message' => 'Catégorie supprimée.']);
    }

    /**
     * Reorder categories and pages.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:wiki_categories,id',
            'categories.*.order' => 'required|integer',
            'categories.*.pages' => 'nullable|array',
            'standalone_pages' => 'nullable|array'
        ]);

        DB::transaction(function() use ($request) {
            foreach ($request->categories as $catData) {
                WikiCategory::where('id', $catData['id'])->update(['order' => $catData['order']]);
                
                if (!empty($catData['pages'])) {
                    foreach ($catData['pages'] as $index => $pageData) {
                        WikiPage::where('id', $pageData['id'])->update([
                            'order' => $index,
                            'wiki_category_id' => $catData['id']
                        ]);
                    }
                }
            }

            if (!empty($request->standalone_pages)) {
                foreach ($request->standalone_pages as $index => $pageData) {
                    WikiPage::where('id', $pageData['id'])->update([
                        'order' => $index,
                        'wiki_category_id' => null
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Ordre mis à jour avec succès.']);
    }
}
