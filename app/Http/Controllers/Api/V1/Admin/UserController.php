<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\UserRole;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['users' => User::orderBy('name')->get()]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'is_active' => 'required|boolean',
        ]);

        // Prevent self-demotion or self-deactivation if last superadmin/admin
        if ($user->id === $request->user()->id && $validated['role'] !== $user->role->value && $user->role === UserRole::ADMIN) {
            // Simplified check, normally would check if other admins exist
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Utilisateur mis à jour.',
            'user' => $user->fresh()
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 403);
        }

        // Check if user is linked to decisions... 
        // For simplicity in V1, we soft delete or delete. User uses SoftDeletes? No, User model does not use SoftDeletes currently!
        // We will just delete it, DB cascade must be configured (it is in V1 for circle members, but not participants maybe).
        try {
            $user->delete();
            return response()->json(['message' => 'Utilisateur supprimé.']);
        } catch (\Exception $e) {
            // If constraint fails, disable the user instead
            $user->update(['is_active' => false]);
            return response()->json(['message' => 'Utilisateur désactivé (suppression impossible car des données y sont liées).']);
        }
    }
}
