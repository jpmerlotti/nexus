<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

new class extends Component {
    public string $ai_driver_preference = 'platform';
    public string $custom_api_key = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->ai_driver_preference = $user->ai_driver_preference ?? 'platform';
        $this->custom_api_key = $user->custom_api_key ?? '';
    }

    /**
     * Update the AI settings for the currently authenticated user.
     */
    public function updateAiSettings(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'ai_driver_preference' => ['required', 'in:platform,byok'],
            'custom_api_key' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'ai_driver_preference' => $this->ai_driver_preference,
            'custom_api_key' => $this->custom_api_key,
        ]);

        $this->dispatch(
            'notify',
            title: __('Configurações de IA'),
            message: __('Suas preferências foram salvas com sucesso!'),
            type: 'success'
        );

        $this->dispatch('settings-updated');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Nexus AI Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Nexus AI')" :subheading="__('Gerencie como você interage com a inteligência do Nexus.')">
        <form wire:submit="updateAiSettings" class="my-6 w-full space-y-6">

            <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 mb-6">
                <div class="flex items-center gap-3 mb-2">
                    <x-nex-icon size="sm" />
                    <flux:heading size="sm" class="!text-amber-500">Saldo Atual do Nexus</flux:heading>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-black text-amber-600 dark:text-amber-400 font-mono">
                        {{ number_format(auth()->user()->nex_balance) }}
                    </span>
                    <span class="text-sm font-bold text-amber-700 dark:text-amber-500 pb-1">NX (Nex)</span>
                </div>
                <flux:text size="sm" class="mt-2 text-amber-800/70 dark:text-amber-400/50">
                    Acabe com seus créditos ou use sua própria chave abaixo para interações ilimitadas.
                </flux:text>
            </div>

            <flux:radio.group wire:model="ai_driver_preference" :label="__('Preferência de Driver')" variant="cards"
                class="flex flex-col gap-4">
                <flux:radio value="platform" :label="__('Nexus Platform (Consome Nex)')"
                    :description="__('Use a inteligência nativa do Nexus. Rápido, estável e pronto para uso.')" />
                <flux:radio value="byok" :label="__('Bring Your Own Key (BYOK)')"
                    :description="__('Use sua própria chave da API do Google Gemini. Não consome seus créditos Nex.')" />
            </flux:radio.group>

            <div x-show="$wire.ai_driver_preference === 'byok'" x-cloak x-transition
                class="space-y-4 pt-4 border-t border-white/5">
                <flux:field>
                    <flux:label>{{ __('Gemini API Key') }}</flux:label>
                    <flux:input wire:model="custom_api_key" type="password" viewable placeholder="AIza..." />
                    <flux:description>
                        Sua chave é armazenada de forma criptografada. Você pode obter uma chave gratuita no
                        <flux:link href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio
                        </flux:link>.
                    </flux:description>
                </flux:field>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">
                        {{ __('Salvar Configurações') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="settings-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-pages::settings.layout>
</section>