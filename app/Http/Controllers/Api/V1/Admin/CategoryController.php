<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('name')->get();

        return response()->json(['categories' => $categories]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Catégorie créée.',
            'category' => $category
        ], 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Catégorie mise à jour.',
            'category' => $category
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        try {
            $category->delete();
            return response()->json(['message' => 'Catégorie supprimée.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Impossible de supprimer cette catégorie car elle est utilisée.'], 403);
        }
    }
}
