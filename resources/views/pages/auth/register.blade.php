<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input name="name" :label="__('Full Name')" :value="old('name')" type="text" required autofocus
                autocomplete="name" :placeholder="__('Full name')" />

            <!-- Username -->
            <flux:input name="username" :label="__('Username')" :value="old('username')" type="text" required
                autocomplete="username" placeholder="username" />

            <!-- Phone -->
            <flux:field>
                <flux:label>{{ __('Phone (Optional)') }}</flux:label>

                <div class="flex gap-2">
                    <flux:select name="country_code" class="w-24" placeholder="Code">
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

                    <flux:input name="phone" :value="old('phone')" type="tel" autocomplete="tel"
                        placeholder="(00) 00000-0000" class="flex-1" />
                </div>
            </flux:field>

            <!-- Email Address -->
            <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required
                autocomplete="email" placeholder="email@example.com" />

            <!-- Password -->
            <flux:input name="password" :label="__('Password')" type="password" required autocomplete="new-password"
                :placeholder="__('Password')" viewable />

            <!-- Confirm Password -->
            <flux:input name="password_confirmation" :label="__('Confirm password')" type="password" required
                autocomplete="new-password" :placeholder="__('Confirm password')" viewable />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>