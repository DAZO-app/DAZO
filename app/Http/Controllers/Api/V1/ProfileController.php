<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (empty($user->custom_views)) {
            $user->custom_views = [
                ['id' => 'my-proposals', 'label' => 'Mes propositions', 'icon' => 'fa-solid fa-bullhorn', 'filters' => ['role' => 'author']],
                ['id' => 'pending-actions', 'label' => 'Réactions attendues', 'icon' => 'fa-solid fa-clock', 'filters' => ['action' => 'pending']],
            ];
            $user->save();
        }
        return response()->json([
            'user' => $user
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return response()->json([
            'message' => 'Profil mis à jour.',
            'user' => $user->fresh()
        ]);
    }

    public function updatePassword(\App\Http\Requests\Auth\UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Mot de passe mis à jour avec succès.'
        ]);
    }

    public function getNotificationPreferences(Request $request): JsonResponse
    {
        $user = $request->user();
        $prefs = \App\Models\NotificationPreference::where('user_id', $user->id)->get();
        return response()->json([
            'preferences' => $prefs
        ]);
    }

    public function updateNotificationPreferences(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'preferences' => ['required', 'array'],
            'preferences.*.category' => ['required', 'string'],
            'preferences.*.email_enabled' => ['required', 'boolean'],
            'preferences.*.web_enabled' => ['required', 'boolean'],
        ]);

        foreach ($validated['preferences'] as $prefData) {
            \App\Models\NotificationPreference::updateOrCreate(
                ['user_id' => $user->id, 'category' => $prefData['category']],
                ['email_enabled' => $prefData['email_enabled'], 'web_enabled' => $prefData['web_enabled']]
            );
        }

        return response()->json([
            'message' => 'Préférences de notification mises à jour.',
            'preferences' => \App\Models\NotificationPreference::where('user_id', $user->id)->get()
        ]);
    }
}
