<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Accessible by any active user
    public function index(): JsonResponse
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories]);
    }
}
