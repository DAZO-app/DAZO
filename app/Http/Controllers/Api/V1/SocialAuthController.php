<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function __construct(
        private SocialAuthService $socialAuthService
    ) {}

    /**
     * Redirect user to the OAuth provider's authorization page.
     * 
     * GET /api/v1/auth/social/{provider}/redirect
     * Optional query: ?invitation_token=xxx
     */
    public function redirect(string $provider, Request $request): JsonResponse
    {
        if (!SocialAuthService::isValidProvider($provider)) {
            return response()->json(['message' => 'Fournisseur OAuth non supporté.'], 422);
        }

        // Store invitation token in session so we can retrieve it in the callback
        if ($request->has('invitation_token')) {
            session(['oauth_invitation_token' => $request->invitation_token]);
        }

        // Store the provider in session for the callback
        session(['oauth_provider' => $provider]);

        $driver = Socialite::driver($provider);

        // For stateless API, we use stateless mode
        if (method_exists($driver, 'stateless')) {
            $driver->stateless();
        }

        $url = $driver->redirect()->getTargetUrl();

        return response()->json(['url' => $url]);
    }

    /**
     * Handle the callback from the OAuth provider.
     * 
     * GET /api/v1/auth/social/{provider}/callback
     * 
     * This endpoint is called by the provider after authorization.
     * It creates/links the user, generates a Sanctum token, 
     * and redirects back to the SPA with the token.
     */
    public function callback(string $provider): RedirectResponse
    {
        if (!SocialAuthService::isValidProvider($provider)) {
            return redirect(config('app.url') . '/login?error=provider_invalid');
        }

        try {
            $driver = Socialite::driver($provider);

            if (method_exists($driver, 'stateless')) {
                $driver->stateless();
            }

            $socialUser = $driver->user();
        } catch (\Exception $e) {
            \Log::error("OAuth callback error [{$provider}]: " . $e->getMessage());
            return redirect(config('app.url') . '/login?error=oauth_failed&provider=' . $provider);
        }

        // Retrieve invitation token from session (if any)
        $invitationToken = session('oauth_invitation_token');
        session()->forget('oauth_invitation_token');

        $result = $this->socialAuthService->findOrCreateUser($provider, $socialUser, $invitationToken);

        $user = $result['user'];

        // Check if the account needs admin approval
        if ($result['needs_approval']) {
            // TODO: Optionally send a notification to admins about the pending account
            return redirect(config('app.url') . '/login?error=account_pending&email=' . urlencode($user->email));
        }

        // Check if the account is active
        if (!$user->is_active) {
            return redirect(config('app.url') . '/login?error=account_inactive');
        }

        // Create a Sanctum token
        $token = $user->createToken('social_auth_' . $provider)->plainTextToken;

        // Redirect back to the SPA with the token
        $redirectUrl = config('app.url') . '/login?token=' . $token . '&provider=' . $provider;

        // If there was an invitation, redirect to accept it
        if ($invitationToken) {
            $redirectUrl .= '&invitation_token=' . $invitationToken;
        }

        return redirect($redirectUrl);
    }

    /**
     * Unlink a social account from the authenticated user.
     * 
     * DELETE /api/v1/auth/social/{provider}/unlink
     */
    public function unlink(string $provider, Request $request): JsonResponse
    {
        if (!SocialAuthService::isValidProvider($provider)) {
            return response()->json(['message' => 'Fournisseur OAuth non supporté.'], 422);
        }

        $user = $request->user();

        $success = $this->socialAuthService->unlinkProvider($user, $provider);

        if (!$success) {
            return response()->json([
                'message' => 'Impossible de supprimer cette liaison. C\'est peut-être votre seul moyen de connexion.'
            ], 422);
        }

        return response()->json([
            'message' => 'Compte ' . SocialAuthService::PROVIDER_LABELS[$provider] . ' délié avec succès.'
        ]);
    }

    /**
     * Get the list of linked social accounts for the authenticated user.
     * 
     * GET /api/v1/auth/social/accounts
     */
    public function accounts(Request $request): JsonResponse
    {
        $user = $request->user();
        $linked = $user->socialAccounts->pluck('provider')->toArray();

        $providers = [];
        foreach (SocialAuthService::PROVIDERS as $provider) {
            // Check if provider is configured (has client_id)
            if (!config("services.{$provider}.client_id")) {
                continue;
            }

            $providers[] = [
                'provider' => $provider,
                'label'    => SocialAuthService::PROVIDER_LABELS[$provider],
                'linked'   => in_array($provider, $linked),
            ];
        }

        return response()->json([
            'providers'    => $providers,
            'has_password' => $user->hasPassword(),
        ]);
    }
}
