<?php

use Livewire\Component;
use App\Models\Campaign;
use App\Models\Character;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $campaigns;
    public $characters;

    public function mount()
    {
        $this->campaigns = Auth::user()->campaigns()->withCount('characters')->latest()->take(5)->get();
        $this->characters = Auth::user()->characters()->latest()->take(5)->get();
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-8 rounded-xl">
    <!-- Welcome Section -->
    <div>
        <h1 class="text-3xl font-bold text-stone-900 dark:text-white mb-2">{{ __('Welcome back,') }}
            {{ auth()->user()->name }}
        </h1>
        <p class="text-stone-600 dark:text-stone-400">{{ __('Your chronicles await. Where shall we begin today?') }}</p>
    </div>

    <!-- Main Grid -->
    <div class="grid gap-6 md:grid-cols-3">
        <!-- Chronicles Widget -->
        <div
            class="relative overflow-hidden rounded-xl bg-glass p-6 group hover:border-amber-500/50 transition-all flex flex-col">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all">
            </div>

            <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-4 flex items-center gap-2">
                <flux:icon.book-open class="size-5 text-amber-500" /> {{ __('Chronicles') }}
            </h3>

            <div class="space-y-4">
                @forelse($campaigns as $campaign)
                    @if($campaign->characters_count > 0)
                        <a href="{{ route('campaigns.play', ['campaign' => $campaign]) }}"
                            class="flex items-center p-3 rounded-lg bg-stone-100/50 dark:bg-stone-950/50 border border-stone-200/50 dark:border-stone-800/50 hover:bg-stone-200 dark:hover:bg-stone-900 hover:border-amber-500/50 transition-colors group">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-stone-900 dark:text-white line-clamp-1">
                                    {{ $campaign->title }}
                                </div>
                                <div class="text-xs text-stone-500">Lv. {{ $campaign->starting_level }} •
                                    {{ str_replace('_', ' ', $campaign->play_style ?? 'Mixed') }}
                                </div>
                            </div>
                            <flux:icon.play-circle
                                class="size-6 text-amber-500 opacity-50 group-hover:opacity-100 transition-opacity" />
                        </a>
                    @else
                        <div class="flex items-center p-3 rounded-lg bg-stone-100/50 dark:bg-stone-950/50 border border-stone-200/50 dark:border-stone-800/50 opacity-70 group"
                            title="{{ __('Requires at least 1 character to play') }}">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-stone-900 dark:text-white line-clamp-1">
                                    {{ $campaign->title }}
                                </div>
                                <div class="text-xs text-amber-600 dark:text-amber-500 font-medium">{{ __('Needs Characters') }}
                                </div>
                            </div>
                            <flux:icon.users class="size-6 text-stone-400 group-hover:text-amber-500 transition-colors" />
                        </div>
                    @endif
                @empty
                    <div
                        class="p-3 rounded-lg bg-stone-100/50 dark:bg-stone-950/50 border border-stone-200/50 dark:border-stone-800/50 text-center text-sm text-stone-500">
                        {{ __('No campaigns yet.') }}
                    </div>
                @endforelse
            </div>

            <a href="{{ route('campaigns.index') }}"
                class="mt-auto w-full py-2 block text-center rounded-lg bg-amber-600/20 hover:bg-amber-600 text-amber-600 dark:text-amber-500 hover:text-white font-medium text-sm transition-all border border-amber-500/20 hover:border-amber-500 cursor-pointer">
                {{ __('Manage Chronicles') }}
            </a>
        </div>

        <!-- Nexus Status Widget -->
        <div
            class="relative overflow-hidden rounded-xl bg-glass p-6 group hover:border-amber-500/50 transition-all flex flex-col">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all">
            </div>
            <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-4 flex items-center gap-2">
                <x-nex-icon size="xs" /> {{ __('Nexus Status') }}
            </h3>

            <div class="flex flex-col gap-4 py-2">
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-black text-amber-600 dark:text-amber-400 font-mono">
                        {{ number_format(auth()->user()->nex_balance) }}
                    </span>
                    <span class="text-sm font-bold text-amber-700 dark:text-amber-500 pb-1">NX</span>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-stone-500 uppercase font-bold tracking-tighter">{{ __('AI Driver') }}</span>
                        <span
                            class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold border border-amber-500/20">
                            {{ auth()->user()->ai_driver_preference === 'byok' ? 'BYOK' : 'Platform' }}
                        </span>
                    </div>
                </div>

                <flux:text size="sm" class="text-stone-500 leading-tight">
                    {{ auth()->user()->ai_driver_preference === 'byok'
    ? __('Usando sua própria chave. Créditos Nex preservados.')
    : __('Conectado à infraestrutura Nexus. Nex consumido por ação.') }}
                </flux:text>
            </div>

            <a href="{{ route('nexus.edit') }}"
                class="mt-auto w-full py-2 block text-center rounded-lg bg-stone-200 dark:bg-stone-800 hover:bg-stone-300 dark:hover:bg-stone-700 text-stone-700 dark:text-stone-300 hover:text-stone-900 dark:hover:text-white font-medium text-sm transition-all cursor-pointer">
                {{ __('Configurar IA') }}
            </a>
        </div>

        <!-- The Party Widget (Characters) -->
        <div
            class="relative overflow-hidden rounded-xl bg-glass p-6 group hover:border-purple-500/50 transition-all flex flex-col">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition-all">
            </div>

            <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-4 flex items-center gap-2">
                <flux:icon.users class="size-5 text-purple-500" /> {{ __('My Characters') }}
            </h3>

            @if($characters->isNotEmpty())
                <div class="flex -space-x-2 overflow-hidden py-2 justify-center mb-4">
                    @foreach($characters->take(4) as $character)
                        <img class="inline-block h-10 w-10 rounded-full ring-2 ring-stone-100 dark:ring-stone-900"
                            src="https://ui-avatars.com/api/?name={{ urlencode($character->name) }}&background=1c1917&color=fff"
                            alt="{{ $character->name }}" title="{{ $character->name }}" />
                    @endforeach
                    @if($characters->count() > 4)
                        <div
                            class="h-10 w-10 rounded-full ring-2 ring-stone-100 dark:ring-stone-900 bg-stone-200 dark:bg-stone-800 flex items-center justify-center text-xs text-stone-500 dark:text-stone-400">
                            +{{ $characters->count() - 4 }}
                        </div>
                    @endif
                </div>
            @else
                <div class="py-4 text-center text-sm text-stone-500">
                    {{ __('No characters created.') }}
                </div>
            @endif

            <div class="space-y-2 mb-4">
                @foreach($characters->take(3) as $character)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-stone-900 dark:text-white font-medium truncate">{{ $character->name }}</span>
                        <span class="text-stone-500 text-xs">{{ $character->race }} {{ $character->class }}
                            Lv.{{ $character->level }}</span>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('characters.index') }}"
                class="mt-auto w-full py-2 block text-center rounded-lg bg-stone-200 dark:bg-stone-800 hover:bg-stone-300 dark:hover:bg-stone-700 text-stone-700 dark:text-stone-300 hover:text-stone-900 dark:hover:text-white font-medium text-sm transition-all cursor-pointer">
                {{ __('Manage Characters') }}
            </a>
        </div>
    </div>
</div>