<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'avatar_url', 'role', 'is_global_animator', 'is_active', 'custom_views'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, CanResetPasswordContract
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes, CanResetPassword;

    public function sendPasswordResetNotification($token): void
    {
        $url = config('app.url') . '/reset-password?token=' . $token . '&email=' . urlencode($this->email);
        $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($url));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_global_animator' => 'boolean',
            'is_active' => 'boolean',
            'custom_views' => 'array',
        ];
    }

    public function notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * Compatibility accessors for views using first_name/last_name
     */
    public function getFirstNameAttribute(): string
    {
        return explode(' ', $this->name)[0] ?? $this->name;
    }

    public function getLastNameAttribute(): string
    {
        $parts = explode(' ', $this->name);
        return count($parts) > 1 ? end($parts) : '';
    }

    public function circles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\CircleMember::class);
    }

    public function authoredDecisions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\DecisionParticipant::class)->where('role', 'author');
    }

    public function decisionSettings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\DecisionUserSetting::class);
    }

    public function socialAccounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\SocialAccount::class);
    }

    /**
     * Check if the user has a linked social account for a given provider.
     */
    public function hasSocialAccount(string $provider): bool
    {
        return $this->socialAccounts()->where('provider', $provider)->exists();
    }

    /**
     * Check if the user has a local password set (not OAuth-only).
     */
    public function hasPassword(): bool
    {
        return !empty($this->password);
    }
}

