<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Character;
use Filament\Notifications\Notification;

new #[Title('My Characters')] class extends Component {
    public function delete(int $id)
    {
        $character = Auth::user()->characters()->findOrFail($id);
        $character->delete();

        Notification::make()
            ->title(__('Character deleted.'))
            ->success()
            ->send();
    }

    public function with()
    {
        return [
            'characters' => Auth::user()->characters()->latest()->get(),
        ];
    }
};
?>
<div class="flex h-full w-full flex-1 flex-col gap-8 rounded-xl max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
        <div>
            <h1 class="text-3xl font-bold text-stone-900 dark:text-white mb-2">{{ __('My Characters') }}</h1>
            <p class="text-stone-600 dark:text-stone-400">{{ __('The heroes you\'ll take into the Nexus.') }}</p>
        </div>
        <flux:button href="{{ route('characters.create') }}" variant="primary" icon="plus" wire:navigate>
            {{ __('New Character') }}
        </flux:button>
    </div>

    <div class="grid gap-6 md:grid-cols-3 lg:grid-cols-4">
        @forelse($characters as $character)
            <div
                class="rounded-xl flex flex-col bg-glass p-6 border border-stone-200/50 dark:border-stone-800/50 transition-all hover:border-purple-500/50">
                <div class="flex items-center gap-4 mb-4">
                    <img class="h-12 w-12 rounded-full ring-2 ring-stone-100 dark:ring-stone-900"
                        src="https://ui-avatars.com/api/?name={{ urlencode($character->name) }}&background=1c1917&color=fff"
                        alt="{{ $character->name }}" />
                    <div>
                        <h3 class="text-lg font-bold text-stone-900 dark:text-white leading-tight">{{ $character->name }}
                        </h3>
                        <p class="text-xs text-stone-500">
                            {{ $character->race }}
                            @if(is_array($character->classes))
                                {{ collect($character->classes)->map(fn($c) => ($c['class'] ?? '') . ' ' . ($c['level'] ?? ''))->implode(' / ') }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-6">
                    <div class="bg-stone-100 dark:bg-stone-900 rounded p-2 text-center">
                        <div class="text-[10px] uppercase text-stone-500 font-bold">{{ __('Level') }}</div>
                        <div class="font-mono text-stone-900 dark:text-white">{{ $character->level }}</div>
                    </div>
                    <div class="bg-stone-100 dark:bg-stone-900 rounded p-2 text-center">
                        <div class="text-[10px] uppercase text-stone-500 font-bold">{{ __('HP') }}</div>
                        <div class="font-mono text-stone-900 dark:text-white">
                            {{ $character->current_hp }}/{{ $character->max_hp }}
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2 mt-auto">
                    <flux:button class="w-full" href="{{ route('characters.show', ['character' => $character]) }}" variant="primary">{{ __('View Sheet') }}</flux:button>
                    <div class="flex gap-2 w-full">
                        <flux:button class="w-1/2" href="{{ route('characters.edit', ['character' => $character]) }}" variant="subtle">{{ __('Edit') }}</flux:button>
                        <flux:button class="w-1/2" variant="danger" wire:click="delete({{ $character->id }})" wire:confirm="{{ __('Are you sure you want to delete this character? This action cannot be undone.') }}">{{ __('Delete') }}</flux:button>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="col-span-full py-16 flex flex-col items-center justify-center border border-dashed border-stone-300 dark:border-stone-700 rounded-xl bg-stone-50/50 dark:bg-stone-900/50 text-center">
                <flux:icon.users class="size-12 text-stone-300 mb-4" />
                <h3 class="text-lg font-medium text-stone-900 dark:text-white mb-1">{{ __('No characters found') }}</h3>
                <p class="text-sm text-stone-500 mb-6 px-4">
                    {{ __('Your party awaits its heroes. Create your first character!') }}</p>
                <flux:button href="{{ route('characters.create') }}" variant="primary" wire:navigate>
                    {{ __('Create Character') }}
                </flux:button>
            </div>
        @endforelse
    </div>
</div>