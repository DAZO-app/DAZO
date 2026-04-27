<?php

namespace App\Services;

use App\Models\SocialAccount;
use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Illuminate\Support\Str;

class SocialAuthService
{
    /**
     * Allowed OAuth providers.
     */
    public const PROVIDERS = [
        'google',
        'github',
        'facebook',
        'twitter',       // X (Twitter) — Socialite still uses 'twitter'
        'linkedin-openid', // LinkedIn uses linkedin-openid driver
        'gitlab',
        'microsoft',
        'apple',
        'franceconnect',
    ];

    /**
     * Human-readable labels for display in the UI.
     */
    public const PROVIDER_LABELS = [
        'google'          => 'Google',
        'github'          => 'GitHub',
        'facebook'        => 'Facebook',
        'twitter'         => 'X (Twitter)',
        'linkedin-openid' => 'LinkedIn',
        'gitlab'          => 'GitLab',
        'microsoft'       => 'Microsoft',
        'apple'           => 'Apple',
        'franceconnect'   => 'FranceConnect',
    ];

    /**
     * Find or create a user from a Socialite user.
     * 
     * Logic:
     * 1. If a SocialAccount with this provider+provider_id exists → return linked user
     * 2. If a User with this email exists → link the social account to that user
     * 3. Otherwise → create a new User + SocialAccount
     *    - If no invitation token → set is_active = false (requires admin approval)
     *    - If invitation token provided → set is_active = true
     *
     * @param string $provider The OAuth provider name
     * @param SocialiteUser $socialUser The user data from the provider
     * @param string|null $invitationToken An invitation token if the user came via an invite link
     * @return array{user: User, is_new: bool, needs_approval: bool}
     */
    public function findOrCreateUser(string $provider, SocialiteUser $socialUser, ?string $invitationToken = null): array
    {
        // 1. Check if this social account already exists
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            // Update tokens
            $socialAccount->update([
                'provider_token'         => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken ?? null,
            ]);

            $user = $socialAccount->user;

            // Update avatar if missing
            if (!$user->avatar_url && $socialUser->getAvatar()) {
                $user->update(['avatar_url' => $socialUser->getAvatar()]);
            }

            return [
                'user'           => $user,
                'is_new'         => false,
                'needs_approval' => false,
            ];
        }

        // 2. Check if a user with this email already exists (auto-merge)
        $email = $socialUser->getEmail();
        $user = $email ? User::where('email', $email)->first() : null;

        if ($user) {
            // Link the social account to the existing user
            $this->createSocialAccount($user, $provider, $socialUser);

            // Update avatar if missing
            if (!$user->avatar_url && $socialUser->getAvatar()) {
                $user->update(['avatar_url' => $socialUser->getAvatar()]);
            }

            return [
                'user'           => $user,
                'is_new'         => false,
                'needs_approval' => false,
            ];
        }

        // 3. Create a new user
        $needsApproval = empty($invitationToken);

        $user = User::create([
            'name'              => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Utilisateur',
            'email'             => $email ?? $provider . '_' . $socialUser->getId() . '@oauth.local',
            'password'          => null, // OAuth-only, no password
            'avatar_url'        => $socialUser->getAvatar(),
            'role'              => 'user',
            'is_active'         => !$needsApproval,
            'email_verified_at' => now(), // OAuth providers verify emails
        ]);

        $this->createSocialAccount($user, $provider, $socialUser);

        return [
            'user'           => $user,
            'is_new'         => true,
            'needs_approval' => $needsApproval,
        ];
    }

    /**
     * Create a social account record linked to a user.
     */
    private function createSocialAccount(User $user, string $provider, SocialiteUser $socialUser): SocialAccount
    {
        return SocialAccount::create([
            'user_id'                => $user->id,
            'provider'               => $provider,
            'provider_id'            => $socialUser->getId(),
            'provider_token'         => $socialUser->token,
            'provider_refresh_token' => $socialUser->refreshToken ?? null,
            'provider_data'          => [
                'name'     => $socialUser->getName(),
                'email'    => $socialUser->getEmail(),
                'nickname' => $socialUser->getNickname(),
                'avatar'   => $socialUser->getAvatar(),
            ],
        ]);
    }

    /**
     * Unlink a social account from a user.
     * Prevents unlinking if it's the only auth method (no password + only 1 social account).
     */
    public function unlinkProvider(User $user, string $provider): bool
    {
        $socialAccount = $user->socialAccounts()->where('provider', $provider)->first();

        if (!$socialAccount) {
            return false;
        }

        // Safety: don't allow unlinking if no password and this is the last social account
        if (!$user->hasPassword() && $user->socialAccounts()->count() <= 1) {
            return false;
        }

        $socialAccount->delete();
        return true;
    }

    /**
     * Validate that a provider is supported.
     */
    public static function isValidProvider(string $provider): bool
    {
        return in_array($provider, self::PROVIDERS);
    }
}
