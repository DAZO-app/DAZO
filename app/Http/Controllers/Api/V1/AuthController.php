<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private \App\Services\ConfigService $configService) {}

    private function verifyRecaptcha(?string $token): bool
    {
        $secret = $this->configService->get('recaptcha_secret_key');
        if (empty($secret)) {
            return true; // No recaptcha configured, skip
        }

        if (empty($token)) {
            return false;
        }

        $response = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $token,
        ]);

        return $response->json('success') === true;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        if (filter_var($this->configService->get('public_registration', 'true'), FILTER_VALIDATE_BOOLEAN) !== true) {
            return response()->json(['message' => 'Les inscriptions sont actuellement fermées.'], 403);
        }

        if (!$this->verifyRecaptcha($request->input('recaptcha_token'))) {
            throw ValidationException::withMessages(['recaptcha' => ['La validation reCAPTCHA a échoué.']]);
        }

        $requireApproval = filter_var($this->configService->get('require_admin_approval', 'false'), FILTER_VALIDATE_BOOLEAN);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => !$requireApproval,
        ]);

        $user->sendEmailVerificationNotification();

        if ($requireApproval) {
            return response()->json([
                'message' => 'Votre compte a bien été créé, mais doit être approuvé par un administrateur avant de pouvoir vous connecter.',
                'require_approval' => true
            ], 201);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!$this->verifyRecaptcha($request->input('recaptcha_token'))) {
            throw ValidationException::withMessages(['recaptcha' => ['La validation reCAPTCHA a échoué.']]);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Ce compte est inactif ou en attente d\'approbation par un administrateur.'
            ], 403);
        }

        // Si expiration d'instance configurée, Sanctum s'en chargera via config('sanctum.expiration')
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $token = $user->currentAccessToken();
            if ($token && method_exists($token, 'delete')) {
                $token->delete();
            }
            Auth::guard('web')->logout();
        }

        return response()->json([
            'message' => 'Déconnexion effectuée avec succès.'
        ]);
    }

    public function verifyEmail(Request $request, string $id, string $hash): JsonResponse
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Lien de vérification invalide.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié.']);
        }

        $user->markEmailAsVerified();

        return response()->json(['message' => 'Email vérifié avec succès.']);
    }

    public function resendEmailVerification(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié.']);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Lien de vérification renvoyé.']);
    }
}
