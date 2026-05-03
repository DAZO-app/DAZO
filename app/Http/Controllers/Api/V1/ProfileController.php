<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        if (empty($user->dashboard_widgets)) {
            $user->dashboard_widgets = [
                ['id' => 'stats', 'label' => 'Statistiques générales', 'enabled' => true, 'width' => 'full'],
                ['id' => 'tickets', 'label' => 'Mes tickets actifs', 'enabled' => true, 'width' => 'half'],
                ['id' => 'urgencies', 'label' => 'Urgences & Échéances', 'enabled' => true, 'width' => 'half'],
                ['id' => 'my_proposals', 'label' => 'Mes propositions', 'enabled' => true, 'width' => 'third'],
                ['id' => 'my_animated', 'label' => 'Mes animations', 'enabled' => true, 'width' => 'third'],
                ['id' => 'circles_watch', 'label' => 'À surveiller', 'enabled' => true, 'width' => 'third'],
            ];
            $user->save();
        }
        return response()->json([
            'user' => $user->fresh()
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

    public function updateAvatar(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists and is local
            if ($user->avatar_url && !str_starts_with($user->avatar_url, 'http')) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar_url' => $path]);

            return response()->json([
                'message' => 'Avatar mis à jour.',
                'avatar_url' => Storage::disk('public')->url($path),
                'user' => $user->fresh()
            ]);
        }

        return response()->json(['message' => 'Aucun fichier reçu.'], 422);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->password)
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
