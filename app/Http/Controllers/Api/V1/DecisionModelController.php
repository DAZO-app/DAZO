<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\DecisionModel\CreateDecisionModelRequest;
use App\Http\Requests\DecisionModel\UpdateDecisionModelRequest;
use App\Models\DecisionModel;
use Illuminate\Http\JsonResponse;

class DecisionModelController extends Controller
{
    public function index(): JsonResponse
    {
        $models = DecisionModel::where('is_active', true)->get();
        return response()->json(['models' => $models]);
    }

    public function store(CreateDecisionModelRequest $request): JsonResponse
    {
        $model = DecisionModel::create($request->validated());

        return response()->json([
            'message' => 'Modèle de décision créé.',
            'model' => $model,
        ], 201);
    }

    public function show(DecisionModel $model): JsonResponse
    {
        $this->authorize('view', $model);
        
        return response()->json(['model' => $model->load('helpTexts')]);
    }

    public function update(UpdateDecisionModelRequest $request, DecisionModel $model): JsonResponse
    {
        $model->update($request->validated());

        return response()->json([
            'message' => 'Modèle mis à jour.',
            'model' => $model->fresh(),
        ]);
    }

    public function destroy(DecisionModel $model): JsonResponse
    {
        $this->authorize('delete', $model);
        
        $model->delete(); // Soft delete

        return response()->json(['message' => 'Modèle supprimé.']);
    }
}
