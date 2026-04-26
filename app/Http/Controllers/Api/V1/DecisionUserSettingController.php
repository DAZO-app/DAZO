<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DecisionNotificationLevel;
use App\Http\Controllers\Controller;
use App\Models\Decision;
use App\Models\DecisionUserSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DecisionUserSettingController extends Controller
{
    public function toggleFavorite(string $id): JsonResponse
    {
        $decision = Decision::findOrFail($id);
        $user = auth()->user();

        $setting = DecisionUserSetting::firstOrCreate(
            ['user_id' => $user->id, 'decision_id' => $decision->id],
            ['is_favorite' => false, 'notification_level' => DecisionNotificationLevel::ALL]
        );

        $setting->update([
            'is_favorite' => !$setting->is_favorite
        ]);

        return response()->json([
            'message' => $setting->is_favorite ? 'Ajouté aux favoris.' : 'Retiré des favoris.',
            'is_favorite' => $setting->is_favorite
        ]);
    }

    public function setNotificationLevel(Request $request, string $id): JsonResponse
    {
        $decision = Decision::findOrFail($id);
        $user = auth()->user();

        $validated = $request->validate([
            'level' => ['required', Rule::enum(DecisionNotificationLevel::class)]
        ]);

        $setting = DecisionUserSetting::updateOrCreate(
            ['user_id' => $user->id, 'decision_id' => $decision->id],
            ['notification_level' => $validated['level']]
        );

        return response()->json([
            'message' => 'Préférence de notification mise à jour.',
            'notification_level' => $setting->notification_level
        ]);
    }
}
