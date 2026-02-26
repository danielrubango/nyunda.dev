<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * @var list<string>
     */
    private const SUPPORTED_PROVIDERS = ['google', 'linkedin'];

    public function redirect(string $provider): RedirectResponse
    {
        $normalizedProvider = $this->normalizeProvider($provider);

        return Socialite::driver($this->resolveDriverName($normalizedProvider))
            ->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        $normalizedProvider = $this->normalizeProvider($provider);

        try {
            $socialUser = Socialite::driver($this->resolveDriverName($normalizedProvider))->user();
        } catch (Throwable $throwable) {
            report($throwable);

            return redirect()
                ->route('login')
                ->with('oauth_error', __('ui.auth.social.callback_error'));
        }

        $providerUserId = (string) $socialUser->getId();

        if ($providerUserId === '') {
            return redirect()
                ->route('login')
                ->with('oauth_error', __('ui.auth.social.callback_error'));
        }

        $user = $this->resolveOrCreateUser(
            provider: $normalizedProvider,
            providerUserId: $providerUserId,
            socialUser: $socialUser,
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(config('fortify.home'));
    }

    private function normalizeProvider(string $provider): string
    {
        $normalizedProvider = Str::lower(trim($provider));

        abort_unless(in_array($normalizedProvider, self::SUPPORTED_PROVIDERS, true), 404);

        return $normalizedProvider;
    }

    private function resolveDriverName(string $provider): string
    {
        return match ($provider) {
            'linkedin' => 'linkedin-openid',
            default => $provider,
        };
    }

    private function resolveOrCreateUser(string $provider, string $providerUserId, SocialiteUser $socialUser): User
    {
        $providerEmail = $this->normalizeProviderEmail($socialUser->getEmail());
        $providerName = $socialUser->getName();
        $avatarUrl = $socialUser->getAvatar();

        $socialAccount = SocialAccount::query()
            ->with('user')
            ->where('provider', $provider)
            ->where('provider_user_id', $providerUserId)
            ->first();

        if ($socialAccount !== null) {
            $socialAccount->forceFill([
                'provider_email' => $providerEmail,
                'provider_name' => $providerName,
                'avatar_url' => $avatarUrl,
            ])->save();

            return $socialAccount->user;
        }

        $user = $this->resolveUserBySessionOrEmail($providerEmail);

        if ($user === null) {
            $user = $this->createUserFromSocialIdentity(
                provider: $provider,
                providerUserId: $providerUserId,
                providerEmail: $providerEmail,
                providerName: $providerName,
            );
        }

        if ($providerEmail !== null && $user->email_verified_at === null) {
            $user->forceFill([
                'email_verified_at' => now(),
            ])->save();
        }

        SocialAccount::query()
            ->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'provider' => $provider,
                ],
                [
                    'provider_user_id' => $providerUserId,
                    'provider_email' => $providerEmail,
                    'provider_name' => $providerName,
                    'avatar_url' => $avatarUrl,
                ],
            );

        return $user;
    }

    private function resolveUserBySessionOrEmail(?string $providerEmail): ?User
    {
        $authenticatedUser = auth()->user();

        if ($authenticatedUser instanceof User) {
            return $authenticatedUser;
        }

        if ($providerEmail === null) {
            return null;
        }

        return User::query()
            ->where('email', $providerEmail)
            ->first();
    }

    private function createUserFromSocialIdentity(
        string $provider,
        string $providerUserId,
        ?string $providerEmail,
        ?string $providerName,
    ): User {
        $resolvedEmail = $providerEmail ?? $this->generatePlaceholderEmail($provider, $providerUserId);

        $user = User::query()->create([
            'name' => $this->resolveUserName($providerName, $provider),
            'email' => $resolvedEmail,
            'password' => Hash::make(Str::random(40)),
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        return $user;
    }

    private function normalizeProviderEmail(?string $email): ?string
    {
        if (! is_string($email) || trim($email) === '') {
            return null;
        }

        return Str::lower(trim($email));
    }

    private function resolveUserName(?string $providerName, string $provider): string
    {
        $candidate = trim((string) $providerName);

        if ($candidate !== '') {
            return Str::limit($candidate, 255, '');
        }

        return Str::headline($provider).' User';
    }

    private function generatePlaceholderEmail(string $provider, string $providerUserId): string
    {
        $slug = Str::slug($providerUserId);

        if ($slug === '') {
            $slug = Str::random(12);
        }

        $baseEmail = "{$provider}_{$slug}@oauth.local";
        $resolvedEmail = Str::lower($baseEmail);
        $suffix = 1;

        while (User::query()->where('email', $resolvedEmail)->exists()) {
            $suffix++;
            $resolvedEmail = Str::lower("{$provider}_{$slug}_{$suffix}@oauth.local");
        }

        return $resolvedEmail;
    }
}
