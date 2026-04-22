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
            'config' => ['required', 'array'],
        ]);

        $this->configService->setMultiple($validated['config']);

        return response()->json([
            'message' => 'Configuration updated successfully',
            'config' => $this->configService->all()
        ]);
    }

    public function uploadLogo(Request $request): JsonResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'max:2048'],
        ]);

        $path = $this->configService->uploadLogo($request->file('logo'));

        return response()->json([
            'message' => 'Logo uploaded successfully',
            'url' => asset('storage/' . $path),
            'config' => $this->configService->all()
        ]);
    }
}
