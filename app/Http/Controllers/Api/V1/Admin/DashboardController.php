<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Circle;
use App\Models\Decision;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'stats' => [
                'users_count' => User::count(),
                'decisions_count' => Decision::count(),
                'circles_count' => Circle::count(),
                'categories_count' => Category::count(),
            ]
        ]);
    }
}
