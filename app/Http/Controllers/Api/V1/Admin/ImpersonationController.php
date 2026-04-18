<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImpersonationController extends Controller
{
    /**
     * Génère un token d'impersonation pour l'utilisateur cible.
     */
    public function impersonate(Request $request, User $user): JsonResponse
    {
        // Une vérification de sécurité additionnelle, bien que le middleware 'admin' protège déjà la route.
        if (! in_array($request->user()->role->value, [\App\Enums\UserRole::ADMIN->value, \App\Enums\UserRole::SUPERADMIN->value])) {
            abort(403, 'Accès non autorisé.');
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Impossible de simuler un compte inactif.'], 400);
        }

        // Créer un token spécifique pour l'impersonation.
        $token = $user->createToken('impersonation_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'message' => "Impersonation de l'utilisateur {$user->name} réussie."
        ]);
    }
}
