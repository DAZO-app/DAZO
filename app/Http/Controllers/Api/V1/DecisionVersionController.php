<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Decision;
use App\Models\DecisionVersion;
use App\Http\Requests\Decision\CreateDecisionVersionRequest;
use App\Services\DecisionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DecisionVersionController extends Controller
{
    public function __construct(private DecisionService $decisionService)
    {
    }

    public function index(Request $request, string $decisionId): JsonResponse
    {
        $decision = Decision::findOrFail($decisionId);
        
        // Authorization check via the parent decision
        if ($request->user()->cannot('view', $decision)) {
            abort(403);
        }

        $versions = $decision->versions()->with('author')->orderBy('version_number', 'desc')->get();

        return response()->json(['versions' => $versions]);
    }

    public function show(Request $request, string $decisionId, string $versionId): JsonResponse
    {
        $decision = Decision::findOrFail($decisionId);
        
        if ($request->user()->cannot('view', $decision)) {
            abort(403);
        }

        $version = DecisionVersion::where('decision_id', $decision->id)
            ->where('id', $versionId)
            ->with(['author', 'previousVersion'])
            ->firstOrFail();

        return response()->json(['version' => $version]);
    }

    public function store(CreateDecisionVersionRequest $request, string $decisionId): JsonResponse
    {
        $decision = Decision::findOrFail($decisionId);
        
        $newVersion = $this->decisionService->createNewVersion($decision, $request->content, $request->user());

        return response()->json([
            'message' => 'Nouvelle version soumise. La décision est repassée en clarification.',
            'version' => $newVersion->load('author'),
            'decision_status' => $decision->fresh()->status->value,
        ], 201);
    }
}
