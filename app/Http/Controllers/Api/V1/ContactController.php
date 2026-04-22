<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\ContactAdminMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Envoie un email à l'administrateur sélectionné.
     */
    public function sendToAdmin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'admin_id' => 'required|uuid|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $admin = User::findOrFail($validated['admin_id']);
        $sender = $request->user();

        // Vérifier que c'est bien un admin
        if (!in_array($admin->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => "L'utilisateur sélectionné n'est pas un administrateur."], 403);
        }

        Mail::to($admin->email)->send(new ContactAdminMail(
            $sender,
            $validated['subject'],
            $validated['message']
        ));

        return response()->json([
            'message' => 'Votre message a été envoyé avec succès.'
        ]);
    }
}
