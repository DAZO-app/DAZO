<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Récupère le profil de l'utilisateur courant.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $request->user()]);
    }

    /**
     * Recherche des utilisateurs par nom ou email (pour inviter un animateur externe au cercle).
     * Retourne les 15 premiers résultats correspondants.
     */
    public function search(Request $request): JsonResponse
    {
        $q = $request->string('q')->trim();

        if ($q->length() < 2) {
            return response()->json(['users' => []]);
        }

        $users = User::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'ilike', "%{$q}%")
                    ->orWhere('email', 'ilike', "%{$q}%");
            })
            ->select('id', 'name', 'email', 'avatar_url')
            ->limit(15)
            ->get();

        return response()->json(['users' => $users]);
    }

    /**
     * Liste tous les administrateurs du site.
     */
    public function admins(): JsonResponse
    {
        $admins = User::whereIn('role', ['admin', 'superadmin'])
            ->where('is_active', true)
            ->select('id', 'name', 'email', 'avatar_url')
            ->get();

        return response()->json(['admins' => $admins]);
    }
}
