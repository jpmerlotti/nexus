<?php

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use ProfileValidationRules;
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $country_code = '';
    public $photo;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;

        // Split phone number into code and number if possible, or just assign to phone
        $fullPhone = Auth::user()->phone;
        // For now, let's just default to +55 if empty, or try to parse
        $this->country_code = '+55';

        if ($fullPhone) {
            // Basic parsing attempt (this is naive but works for the fixed list)
            $codes = ['+55', '+1', '+351', '+44', '+33', '+49', '+34', '+39', '+81', '+86'];
            foreach ($codes as $code) {
                if (str_starts_with($fullPhone, $code)) {
                    $this->country_code = $code;
                    $this->phone = substr($fullPhone, strlen($code));
                    break;
                }
            }
            if (!$this->phone)
                $this->phone = $fullPhone; // Fallback
        }
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'photo' => ['nullable', 'image', 'max:1024'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'country_code' => ['nullable', 'string', 'max:5'],
        ]);

        if (isset($this->photo)) {
            $user->update([
                'profile_photo_path' => $this->photo->storePublicly('profile-photos', ['disk' => 'public']),
            ]);
        }

        $user->fill([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->country_code . $this->phone,
        ]);

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
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return !Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">

            <div class="flex items-center gap-6">
                <div class="shrink-0">
                    @if ($this->photo)
                        <div class="h-20 w-20 rounded-full bg-cover bg-center bg-no-repeat"
                            style="background-image: url('{{ $this->photo->temporaryUrl() }}');"></div>
                    @else
                        <div class="h-20 w-20 rounded-full bg-cover bg-center bg-no-repeat"
                            style="background-image: url('{{ auth()->user()->profile_photo_url }}');"></div>
                    @endif
                </div>

                <div class="space-y-2">
                    <flux:label>{{ __('Profile Photo') }}</flux:label>
                    <flux:input type="file" wire:model="photo" accept="image/*" />
                </div>
            </div>

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
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Phone -->
            <flux:field>
                <flux:label>{{ __('Phone') }}</flux:label>

                <div class="flex gap-2">
                    <flux:select wire:model="country_code" class="w-24" placeholder="Code">
                        <flux:select.option value="+55">🇧🇷 +55</flux:select.option>
                        <flux:select.option value="+1">🇺🇸 +1</flux:select.option>
                        <flux:select.option value="+351">🇵🇹 +351</flux:select.option>
                        <flux:select.option value="+44">🇬🇧 +44</flux:select.option>
                        <flux:select.option value="+33">🇫🇷 +33</flux:select.option>
                        <flux:select.option value="+49">🇩🇪 +49</flux:select.option>
                        <flux:select.option value="+34">🇪🇸 +34</flux:select.option>
                        <flux:select.option value="+39">🇮🇹 +39</flux:select.option>
                        <flux:select.option value="+81">🇯🇵 +81</flux:select.option>
                        <flux:select.option value="+86">🇨🇳 +86</flux:select.option>
                    </flux:select>

                    <flux:input wire:model="phone" type="tel" autocomplete="tel" placeholder="(00) 00000-0000"
                        class="flex-1" />
                </div>
            </flux:field>

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