<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagicLoginController extends Controller
{
    /**
     * Authenticate a user via a signed URL and redirect to a target location.
     */
    public function login(Request $request, User $user)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Ce lien de connexion a expiré ou est invalide.');
        }

        // Authenticate the user
        Auth::login($user);

        // Get redirect path or default to dashboard
        $redirect = $request->query('redirect', '/dashboard');

        return redirect($redirect);
    }
}
