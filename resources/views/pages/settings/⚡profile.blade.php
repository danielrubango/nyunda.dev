<?php

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';
    public ?string $headline = null;
    public ?string $bio = null;
    public ?string $location = null;
    public ?string $website_url = null;
    public ?string $linkedin_url = null;
    public ?string $x_url = null;
    public ?string $github_url = null;
    public bool $is_profile_public = false;
    public ?string $public_profile_slug = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->headline = $user->headline;
        $this->bio = $user->bio;
        $this->location = $user->location;
        $this->website_url = $user->website_url;
        $this->linkedin_url = $user->linkedin_url;
        $this->x_url = $user->x_url;
        $this->github_url = $user->github_url;
        $this->is_profile_public = (bool) $user->is_profile_public;
        $this->public_profile_slug = $user->public_profile_slug;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        /** @var User $user */
        $user = Auth::user();

        foreach (['headline', 'bio', 'location', 'website_url', 'linkedin_url', 'x_url', 'github_url', 'public_profile_slug'] as $field) {
            $value = $this->{$field};

            if (! is_string($value)) {
                continue;
            }

            $value = trim($value);
            $this->{$field} = $value === '' ? null : $value;
        }

        $validated = $this->validate($this->profileRules($user->id));

        if ((bool) $validated['is_profile_public'] === true) {
            $validated['public_profile_slug'] = trim((string) ($validated['public_profile_slug'] ?? ''));

            if ($validated['public_profile_slug'] === '') {
                $validated['public_profile_slug'] = Str::slug((string) $validated['name']);
            }
        } else {
            $validated['public_profile_slug'] = null;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('home', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <flux:input wire:model="headline" label="Headline" type="text" autocomplete="organization-title" />
            <flux:textarea wire:model="bio" label="Bio" rows="5" />
            <flux:input wire:model="location" label="Location" type="text" autocomplete="address-level2" />

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="website_url" label="Website URL" type="url" />
                <flux:input wire:model="linkedin_url" label="LinkedIn URL" type="url" />
                <flux:input wire:model="x_url" label="X URL" type="url" />
                <flux:input wire:model="github_url" label="GitHub URL" type="url" />
            </div>

            <flux:checkbox wire:model="is_profile_public" label="Rendre mon profil public" />

            <flux:input
                wire:model="public_profile_slug"
                label="Slug public"
                type="text"
                :disabled="! $is_profile_public"
                placeholder="mon-profil-public"
            />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
