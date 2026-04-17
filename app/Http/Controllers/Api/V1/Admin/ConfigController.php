<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\ConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function __construct(private ConfigService $configService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->configService->all());
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'configs' => ['required', 'array'],
        ]);

        $this->configService->setMultiple($validated['configs']);

        return response()->json([
            'message' => 'Configuration updated successfully',
            'configs' => $this->configService->all()
        ]);
    }
}
