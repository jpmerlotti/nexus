<?php

namespace App\Livewire\Pages\Campaigns;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\Campaign;
use App\Models\Character;
use Illuminate\Support\Facades\Auth;

new #[Title('Chronicle Details')] class extends Component {
    public Campaign $campaign;
    public $characterToAdd = '';

    public function mount(Campaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $this->campaign = $campaign;
    }

    public function addCharacter()
    {
        if (empty($this->characterToAdd))
            return;

        $this->campaign->characters()->attach($this->characterToAdd);
        $this->characterToAdd = '';

        $this->campaign->load('characters');

        $this->dispatch(
            'notify',
            title: __('Aliado Adicionado'),
            message: __('O personagem agora faz parte do grupo!'),
            type: 'success'
        );
    }

    public function removeCharacter($characterId)
    {
        $this->campaign->characters()->detach($characterId);
        $this->campaign->load('characters');

        $this->dispatch(
            'notify',
            title: __('Aliado Removido'),
            message: __('O personagem deixou o grupo.'),
            type: 'warning'
        );
    }

    public function with()
    {
        $userCharacters = Auth::user()->characters;

        return [
            'hasAnyCharacters' => $userCharacters->isNotEmpty(),
            'availableCharacters' => Auth::user()->characters()
                ->whereDoesntHave('campaigns', fn($q) => $q->where('campaigns.id', $this->campaign->id))
                ->get(),
        ];
    }
};
?>
<div class="flex h-full w-full flex-1 flex-col gap-8 rounded-xl max-w-4xl mx-auto">
    <div>
        <flux:breadcrumbs class="mb-4">
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" wire:navigate>{{ __('Dashboard') }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('campaigns.index') }}" wire:navigate>{{ __('Chronicles') }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $campaign->title }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="bg-glass p-8 rounded-xl border border-stone-200/50 dark:border-stone-800/50 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none">
        </div>

        <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-8">
            <div>
                <h1 class="text-4xl font-black text-stone-900 dark:text-white leading-tight mb-2">{{ $campaign->title }}
                </h1>
                <p
                    class="text-stone-600 dark:text-stone-400 max-w-2xl leading-relaxed mt-4 bg-stone-50/50 dark:bg-stone-900/30 p-4 rounded-lg border border-stone-100 dark:border-stone-800">
                    {{ $campaign->description ?: __('No description provided.') }}
                </p>
            </div>

            <div class="flex gap-2">
                @if($campaign->characters()->exists())
                    <flux:button href="{{ route('campaigns.play', ['campaign' => $campaign]) }}" variant="primary"
                        icon="play">
                        {{ __('Play') }}
                    </flux:button>
                @else
                    <flux:button variant="primary" icon="play" disabled
                        tooltip="{{ __('Requires at least 1 character to play') }}">
                        {{ __('Play') }}
                    </flux:button>
                @endif
                <flux:button href="{{ route('campaigns.edit', ['campaign' => $campaign]) }}" variant="subtle"
                    icon="pencil">
                    {{ __('Edit') }}
                </flux:button>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div
                class="bg-white/50 dark:bg-stone-900/50 p-4 rounded-xl border border-stone-100 dark:border-stone-800 text-center">
                <div class="text-[10px] uppercase tracking-wider text-stone-500 font-bold mb-1">
                    {{ __('Starting Level') }}
                </div>
                <div class="text-2xl font-mono text-amber-600 dark:text-amber-500">{{ $campaign->starting_level }}</div>
            </div>
            <div
                class="bg-white/50 dark:bg-stone-900/50 p-4 rounded-xl border border-stone-100 dark:border-stone-800 text-center">
                <div class="text-[10px] uppercase tracking-wider text-stone-500 font-bold mb-1">{{ __('Difficulty') }}
                </div>
                <div class="text-lg font-bold text-stone-700 dark:text-stone-300 capitalize">
                    {{ str_replace('_', ' ', $campaign->difficulty) }}
                </div>
            </div>
            <div
                class="bg-white/50 dark:bg-stone-900/50 p-4 rounded-xl border border-stone-100 dark:border-stone-800 text-center">
                <div class="text-[10px] uppercase tracking-wider text-stone-500 font-bold mb-1">{{ __('Narration') }}
                </div>
                <div class="text-lg font-bold text-stone-700 dark:text-stone-300 capitalize">
                    {{ str_replace('_', ' ', $campaign->narration_detail_level) }}
                </div>
            </div>
            <div
                class="bg-white/50 dark:bg-stone-900/50 p-4 rounded-xl border border-stone-100 dark:border-stone-800 text-center">
                <div class="text-[10px] uppercase tracking-wider text-stone-500 font-bold mb-1">{{ __('Play Style') }}
                </div>
                <div class="text-lg font-bold text-stone-700 dark:text-stone-300 capitalize">
                    {{ str_replace('_', ' ', $campaign->play_style) }}
                </div>
            </div>
        </div>

        <div class="border-t border-stone-200 dark:border-stone-800 pt-8 mt-4">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-stone-900 dark:text-white flex items-center gap-2">
                    <flux:icon.users class="size-5 text-amber-500" />
                    {{ __('Party Members') }}
                </h2>
                <div class="flex items-center gap-2">
                    @if($hasAnyCharacters)
                        @if(count($availableCharacters) > 0)
                            <flux:select wire:model.live="characterToAdd" placeholder="{{ __('Add a character...') }}"
                                class="max-w-xs">
                                @foreach($availableCharacters as $char)
                                    <flux:select.option value="{{ $char->id }}">{{ $char->name }} (Lv.{{ $char->level }}
                                        {{ $char->race->getLabel() }})
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:button icon="plus" wire:click="addCharacter" variant="subtle" />
                        @else
                            <span
                                class="text-xs text-stone-500 italic">{{ __('All your characters are in this chronicle') }}</span>
                        @endif
                    @else
                        <flux:button href="{{ route('characters.create') }}" variant="subtle" size="sm" icon="plus"
                            wire:navigate>
                            {{ __('Create your first character') }}
                        </flux:button>
                    @endif
                </div>
            </div>

            @if($campaign->characters ?? false && $campaign->characters->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($campaign->characters as $character)
                        <div
                            class="flex items-center gap-3 bg-stone-50 dark:bg-stone-900/40 p-3 rounded-lg border border-stone-100 dark:border-stone-800">
                            <img class="h-10 w-10 rounded-full ring-2 ring-stone-200 dark:ring-stone-700"
                                src="https://ui-avatars.com/api/?name={{ urlencode($character->name) }}&background=1c1917&color=fff"
                                alt="{{ $character->name }}" />
                            <div>
                                <h4 class="font-bold text-sm text-stone-900 dark:text-white">{{ $character->name }}</h4>
                                <p class="text-xs text-stone-500">{{ $character->race->getLabel() }} • {{ __('Lv.') }}
                                    {{ $character->level }}
                                </p>
                            </div>
                            <div class="ml-auto">
                                <flux:button icon="trash" size="sm" variant="ghost"
                                    wire:click="removeCharacter({{ $character->id }})"
                                    wire:confirm="{{ __('Remove this character from the chronicle?') }}" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div
                    class="py-8 text-center text-stone-500 border border-dashed border-stone-300 dark:border-stone-700 rounded-xl bg-stone-50/50 dark:bg-stone-900/30">
                    <p>{{ __('No characters have joined this chronicle yet.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>