<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Campaign;

new #[Title('Chronicles')] class extends Component {
    public function delete(int $id)
    {
        $campaign = Auth::user()->campaigns()->findOrFail($id);
        $campaign->delete();

        $this->dispatch(
            'notify',
            title: __('Crônica Apagada'),
            message: __('A história foi encerrada com sucesso.'),
            type: 'danger'
        );
    }

    public function with()
    {
        return [
            'campaigns' => Auth::user()->campaigns()->withCount('characters')->latest()->get(),
        ];
    }
};
?>
<div class="flex h-full w-full flex-1 flex-col gap-8 rounded-xl max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
        <div>
            <h1 class="text-3xl font-bold text-stone-900 dark:text-white mb-2">{{ __('Your Chronicles') }}</h1>
            <p class="text-stone-600 dark:text-stone-400">{{ __('Manage the adventures you are part of.') }}</p>
        </div>
        <flux:button href="{{ route('campaigns.create') }}" variant="primary" icon="plus" wire:navigate>
            {{ __('New Chronicle') }}
        </flux:button>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($campaigns as $campaign)
            <div
                class="rounded-xl bg-glass p-6 border border-stone-200/50 dark:border-stone-800/50 transition-all hover:border-amber-500/50 flex flex-col h-full relative group">
                <div class="flex justify-between items-start mb-2 gap-4">
                    <h3 class="text-xl font-bold text-stone-900 dark:text-white line-clamp-1"
                        title="{{ $campaign->title }}">{{ $campaign->title }}</h3>
                    <div class="shrink-0 -mr-2 -mt-2">
                        <flux:dropdown position="bottom-end">
                            <flux:button icon="ellipsis-horizontal" variant="ghost" size="sm"
                                class="text-stone-400 hover:text-stone-900 dark:hover:text-white" />
                            <flux:menu>
                                <flux:menu.item href="{{ route('campaigns.show', ['campaign' => $campaign]) }}" icon="eye">
                                    {{ __('Summary') }}
                                </flux:menu.item>
                                <flux:menu.item href="{{ route('campaigns.edit', ['campaign' => $campaign]) }}"
                                    icon="pencil">{{ __('Edit') }}</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item wire:click="delete({{ $campaign->id }})"
                                    wire:confirm="{{ __('Are you sure you want to delete this chronicle? This action cannot be undone.') }}"
                                    icon="trash" variant="danger">{{ __('Delete') }}</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>

                <p class="text-sm text-stone-500 mb-6 line-clamp-3 flex-1">
                    {{ $campaign->description ?: __('No description provided.') }}
                </p>

                <div class="flex justify-between items-center text-xs text-stone-400 mb-6 mt-auto">
                    <span>{{ __('Lv.') }} {{ $campaign->starting_level }}</span>
                    <span class="capitalize">{{ str_replace('_', ' ', $campaign->play_style ?? __('Mixed')) }}</span>
                </div>

                @if($campaign->characters_count > 0)
                    <flux:button class="w-full mt-auto" href="{{ route('campaigns.play', ['campaign' => $campaign]) }}"
                        variant="primary" icon="play">
                        {{ __('Play') }}
                    </flux:button>
                @else
                    <flux:button class="w-full mt-auto opacity-70" variant="primary" icon="play" disabled
                        tooltip="{{ __('Requires at least 1 character to play') }}">
                        {{ __('Play (Needs Characters)') }}
                    </flux:button>
                @endif
            </div>
        @empty
            <div
                class="col-span-full py-16 flex flex-col items-center justify-center border border-dashed border-stone-300 dark:border-stone-700 rounded-xl bg-stone-50/50 dark:bg-stone-900/50 text-center">
                <flux:icon.book-open class="size-12 text-stone-300 mb-4" />
                <h3 class="text-lg font-medium text-stone-900 dark:text-white mb-1">{{ __('No chronicles found') }}</h3>
                <p class="text-sm text-stone-500 mb-6 px-4">
                    {{ __('You haven\'t joined or created any chronicles yet. Start your journey!') }}
                </p>
                <flux:button href="{{ route('campaigns.create') }}" variant="primary" wire:navigate>
                    {{ __('Start an Adventure') }}
                </flux:button>
            </div>
        @endforelse
    </div>
</div>