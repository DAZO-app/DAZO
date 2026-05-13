<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\UserLayout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LayoutController extends Controller
{
    public function show(string $viewName): JsonResponse
    {
        $layout = UserLayout::where('user_id', auth()->id())
            ->where('view_name', $viewName)
            ->first();

        return response()->json([
            'layout' => $layout ? $layout->layout_data : null
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'view_name' => 'required|string',
            'layout_data' => 'required|array',
        ]);

        $layout = UserLayout::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'view_name' => $request->view_name
            ],
            [
                'layout_data' => $request->layout_data
            ]
        );

        return response()->json([
            'message' => 'Layout saved',
            'layout' => $layout->layout_data
        ]);
    }
}
