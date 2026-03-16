<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\Campaign;
use App\Models\Character;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

new #[Title('Select Your Hero')] class extends Component {
    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }
        $this->campaign = $campaign;
    }

    public function selectCharacter($characterId)
    {
        $character = Character::findOrFail($characterId);

        // Security check
        if ($character->user_id !== Auth::id()) {
            abort(403);
        }

        $this->campaign->characters()->attach($character);

        $this->dispatch(
            'notify',
            title: __('Destino Traçado'),
            message: __('Seu herói foi vinculado a esta crônica!'),
            type: 'success'
        );

        return $this->redirectRoute('campaigns.show', ['campaign' => $this->campaign->id], navigate: true);
    }

    public function with()
    {
        return [
            // Characters that are NOT in ANY campaign
            'availableCharacters' => Auth::user()->characters()
                ->whereDoesntHave('campaigns')
                ->get(),
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-8 rounded-xl max-w-4xl mx-auto px-4 py-8">
    <div class="text-center space-y-4">
        <h1 class="text-4xl font-black text-stone-900 dark:text-white">{{ __('Who will lead this story?') }}</h1>
        <p class="text-stone-600 dark:text-stone-400 text-lg">
            {{ __('Every great chronicle needs a hero. Select one of your available characters or forge a new legend.') }}
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        <!-- Create New Character Card -->
        <a href="{{ route('characters.create', ['redirect' => route('campaigns.link-character', $campaign)]) }}"
            wire:navigate
            class="group relative flex flex-col items-center justify-center p-8 bg-white dark:bg-stone-900 border-2 border-dashed border-stone-200 dark:border-stone-800 rounded-2xl hover:border-amber-500/50 transition-all duration-300">
            <div
                class="w-16 h-16 rounded-full bg-stone-100 dark:bg-stone-800 flex items-center justify-center mb-4 group-hover:bg-amber-500/10 transition-colors">
                <flux:icon.plus class="size-8 text-stone-400 group-hover:text-amber-500 transition-colors" />
            </div>
            <h3 class="text-xl font-bold text-stone-900 dark:text-white group-hover:text-amber-500 transition-colors">
                {{ __('Forge a New Legend') }}
            </h3>
            <p class="text-stone-500 text-center mt-2 text-sm">
                {{ __('Start fresh with a brand new character for this chronicle.') }}
            </p>
        </a>

        <!-- List available characters -->
        @foreach($availableCharacters as $char)
            <button wire:click="selectCharacter({{ $char->id }})"
                class="group relative flex items-center gap-6 p-6 bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-2xl hover:border-amber-500/50 hover:shadow-xl transition-all duration-300 text-left">
                <div class="relative flex-shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($char->name) }}&background=1c1917&color=fbbf24&size=100"
                        alt="{{ $char->name }}"
                        class="w-20 h-20 rounded-xl shadow-lg group-hover:scale-105 transition-transform duration-500">
                    <div
                        class="absolute -bottom-2 -right-2 bg-amber-500 text-stone-900 text-[10px] font-black px-2 py-0.5 rounded-md uppercase tracking-tighter">
                        Lv.{{ $char->level }}
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <h3
                        class="text-xl font-black text-stone-900 dark:text-white truncate group-hover:text-amber-500 transition-colors uppercase tracking-tight">
                        {{ $char->name }}
                    </h3>
                    <p class="text-stone-500 text-sm font-medium uppercase tracking-widest mt-1">
                        {{ $char->race->getLabel() }} • {{ $char->class }}
                    </p>
                    <div class="flex items-center gap-3 mt-3">
                        <div class="flex flex-col">
                            <span
                                class="text-[9px] text-stone-400 uppercase font-black tracking-tighter">{{ __('HP') }}</span>
                            <span
                                class="text-sm font-bold text-emerald-500">{{ $char->current_hp }}/{{ $char->max_hp }}</span>
                        </div>
                        <div class="w-px h-6 bg-stone-200 dark:bg-stone-800"></div>
                        <div class="flex flex-col">
                            <span
                                class="text-[9px] text-stone-400 uppercase font-black tracking-tighter">{{ __('XP') }}</span>
                            <span class="text-sm font-bold text-amber-500">{{ $char->current_xp }}</span>
                        </div>
                    </div>
                </div>

                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                    <flux:icon.chevron-right class="size-6 text-amber-500" />
                </div>
            </button>
        @endforeach
    </div>

    @if($availableCharacters->isEmpty())
        <div
            class="mt-8 p-12 text-center bg-stone-50 dark:bg-stone-900/50 rounded-2xl border border-stone-200 dark:border-stone-800">
            <p class="text-stone-500 italic">{{ __('You have no available characters to lead this chronicle.') }}</p>
            <p class="text-stone-500 text-sm mt-1">{{ __('Try creating a new legends above!') }}</p>
        </div>
    @endif

    <div class="mt-12 pt-8 border-t border-stone-200 dark:border-stone-800 flex justify-center">
        <flux:button href="{{ route('campaigns.show', $campaign) }}" variant="ghost" wire:navigate>
            {{ __('Decide later (Go to Chronicle details)') }}
        </flux:button>
    </div>
</div>