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
            ->with([
                'author', 
                'previousVersion', 
                'attachments',
                'feedbacks.author',
                'feedbacks.messages.author',
                'consents.user'
            ])
            ->firstOrFail();

        // Si ce n'est pas la version courante, on considère l'état comme "complet" (Objection)
        // pour le calcul des stats historiques, sauf si la décision est déjà adoptée.
        $statusOverride = $version->is_current ? null : \App\Enums\DecisionStatus::OBJECTION->value;
        if (!$version->is_current && ($decision->status->value === \App\Enums\DecisionStatus::ADOPTED->value || $decision->status->value === \App\Enums\DecisionStatus::ADOPTED_OVERRIDE->value)) {
            $statusOverride = $decision->status->value;
        }

        $stats = $this->decisionService->getParticipationStats($decision, $version, $statusOverride);

        return response()->json([
            'version' => $version,
            'participation_stats' => $stats
        ]);
    }

    public function store(CreateDecisionVersionRequest $request, string $decisionId): JsonResponse
    {
        $decision = Decision::findOrFail($decisionId);
        
        $newVersion = $this->decisionService->createNewVersion(
            $decision, 
            $request->content, 
            $request->user(),
            $request->attachment_ids ?? []
        );

        return response()->json([
            'message' => 'Nouvelle version soumise. La décision est repassée en clarification.',
            'version' => $newVersion->load('author'),
            'decision_status' => $decision->fresh()->status->value,
        ], 201);
    }
}
