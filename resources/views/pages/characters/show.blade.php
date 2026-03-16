<?php

namespace App\Livewire\Pages\Characters;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\Character;
use Illuminate\Support\Facades\Auth;

new #[Title('Character Sheet')] class extends Component {
    public Character $character;

    public function mount(Character $character)
    {
        if ($character->user_id !== Auth::id()) {
            abort(403);
        }

        $this->character = $character->load('campaigns');
    }
};
?>
<div class="flex h-full w-full flex-1 flex-col gap-8 rounded-xl max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div>
        <flux:breadcrumbs class="mb-4">
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" wire:navigate>{{ __('Dashboard') }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('characters.index') }}" wire:navigate>{{ __('Characters') }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $character->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left Column: Main Content -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Header Card -->
            <div class="bg-glass p-8 rounded-2xl border border-stone-200/50 dark:border-stone-800/50 relative overflow-hidden flex flex-col md:flex-row gap-8 items-start">
                <div class="absolute -left-10 -top-10 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="shrink-0 relative z-10 w-32 h-32 md:w-40 md:h-40">
                    <img class="w-full h-full rounded-2xl object-cover ring-4 ring-white dark:ring-[#0c0a09] shadow-lg"
                        src="https://ui-avatars.com/api/?name={{ urlencode($character->name) }}&background=f59e0b&color=fff&size=256"
                        alt="{{ $character->name }}" />
                    <div class="absolute -bottom-3 -right-3 bg-amber-500 text-white w-12 h-12 rounded-full flex flex-col items-center justify-center border-4 border-white dark:border-[#0c0a09] shadow-xl">
                        <span class="text-[10px] leading-none uppercase font-black">Lvl</span>
                        <span class="text-lg leading-none font-bold">{{ $character->level }}</span>
                    </div>
                </div>

                <div class="flex-1 relative z-10 w-full">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-6">
                        <div>
                            <h1 class="text-4xl font-black text-stone-900 dark:text-white leading-tight">
                                {{ $character->name }}
                            </h1>
                            <div class="flex flex-wrap items-center gap-2 mt-2 text-sm font-medium">
                                <span class="bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-3 py-1 rounded-full border border-amber-200 dark:border-amber-800">
                                    {{ $character->race }}
                                </span>
                                @if(is_array($character->classes))
                                    @foreach($character->classes as $c)
                                        <span class="bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 px-3 py-1 rounded-full border border-stone-200 dark:border-stone-700">
                                            {{ $c['class'] ?? '' }}
                                        </span>
                                    @endforeach
                                @endif
                                <span class="bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 px-3 py-1 rounded-full border border-stone-200 dark:border-stone-700">
                                    {{ $character->alignment }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 shrink-0">
                            <flux:button href="{{ route('characters.edit', ['character' => $character]) }}" 
                                variant="primary" 
                                icon="pencil"
                                class="!bg-amber-500 hover:!bg-amber-600 border-none"
                            >
                                {{ __('Edit') }}
                            </flux:button>
                        </div>
                    </div>

                    <!-- Vitals -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/40 dark:bg-stone-950/40 p-4 rounded-xl border border-stone-100 dark:border-stone-800">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] uppercase tracking-widest text-stone-500 font-black">{{ __('Health') }}</span>
                                <flux:icon.heart class="size-4 text-red-500" />
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-black text-stone-900 dark:text-white">{{ $character->current_hp }}</span>
                                <span class="text-stone-400 text-sm italic">/ {{ $character->max_hp }} HP</span>
                            </div>
                            <div class="w-full bg-stone-200 dark:bg-stone-800 h-1.5 rounded-full mt-2 overflow-hidden">
                                <div class="bg-red-500 h-full rounded-full" style="width: {{ ($character->current_hp / $character->max_hp) * 100 }}%"></div>
                            </div>
                        </div>

                        <div class="bg-white/40 dark:bg-stone-950/40 p-4 rounded-xl border border-stone-100 dark:border-stone-800">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] uppercase tracking-widest text-stone-500 font-black">{{ __('Experience') }}</span>
                                <flux:icon.bolt class="size-4 text-amber-500" />
                            </div>
                            <div class="text-2xl font-black text-stone-900 dark:text-white">
                                {{ number_format($character->current_xp) }}
                                <span class="text-stone-400 text-sm font-normal italic">XP</span>
                            </div>
                            <div class="w-full bg-stone-200 dark:bg-stone-800 h-1.5 rounded-full mt-2 overflow-hidden">
                                <div class="bg-amber-500 h-full rounded-full" style="width: 45%"></div> <!-- Mock XP progress -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Tabs -->
            <x-tabs default="attributes">
                <div class="flex border-b border-stone-200 dark:border-stone-800 mb-6 overflow-x-auto no-scrollbar">
                    <x-tabs.tab name="attributes" icon="presentation-chart-bar">{{ __('Atributos') }}</x-tabs.tab>
                    <x-tabs.tab name="story" icon="book-open">{{ __('História') }}</x-tabs.tab>
                    <x-tabs.tab name="appearance" icon="sparkles">{{ __('Aparência') }}</x-tabs.tab>
                    <x-tabs.tab name="inventory" icon="beaker">{{ __('Inventário') }}</x-tabs.tab>
                    <x-tabs.tab name="social" icon="users">{{ __('Relacionamentos') }}</x-tabs.tab>
                </div>

                <!-- Attributes Panel -->
                <x-tabs.panel name="attributes">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        @php
                            $stats = [
                                ['label' => 'Força', 'key' => 'strength', 'icon' => 'hand-raised'],
                                ['label' => 'Destreza', 'key' => 'dexterity', 'icon' => 'bolt'],
                                ['label' => 'Constituição', 'key' => 'constitution', 'icon' => 'shield-check'],
                                ['label' => 'Inteligência', 'key' => 'intelligence', 'icon' => 'academic-cap'],
                                ['label' => 'Sabedoria', 'key' => 'wisdom', 'icon' => 'eye'],
                                ['label' => 'Carisma', 'key' => 'charisma', 'icon' => 'chat-bubble-bottom-center-text'],
                            ];

                            if (!function_exists('calcMod')) {
                                function calcMod($score) {
                                    $mod = floor(($score - 10) / 2);
                                    return $mod >= 0 ? "+{$mod}" : $mod;
                                }
                            }
                        @endphp

                        @foreach($stats as $stat)
                            <div class="bg-stone-50 dark:bg-stone-900/60 border border-stone-200 dark:border-stone-800 rounded-2xl p-6 transition-all hover:border-amber-500/50 group">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-2 bg-stone-100 dark:bg-stone-800 rounded-lg text-stone-400 group-hover:text-amber-500 transition-colors">
                                        <flux:icon :name="$stat['icon']" class="size-5" />
                                    </div>
                                    <span class="text-3xl font-black text-amber-500">{{ calcMod($character->{$stat['key']}) }}</span>
                                </div>
                                <div class="text-[10px] uppercase tracking-widest text-stone-500 font-black mb-1">{{ __($stat['label']) }}</div>
                                <div class="text-2xl font-black text-stone-900 dark:text-white">{{ $character->{$stat['key']} }}</div>
                            </div>
                        @endforeach
                    </div>
                </x-tabs.panel>

                <!-- Story Panel -->
                <x-tabs.panel name="story">
                    <div class="prose dark:prose-invert max-w-none bg-stone-50 dark:bg-stone-900/60 p-8 rounded-2xl border border-stone-200 dark:border-stone-800">
                        @if($character->backstory)
                            {!! $character->backstory !!}
                        @else
                            <div class="text-center py-12 text-stone-500 italic">
                                {{ __('Nenhuma história registrada para este personagem.') }}
                            </div>
                        @endif
                    </div>
                    
                    @if($character->notes)
                        <div class="mt-8">
                            <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-4 flex items-center gap-2">
                                <flux:icon.pencil-square class="size-5 text-amber-500" />
                                {{ __('Anotações do Jogador') }}
                            </h3>
                            <div class="bg-amber-50/30 dark:bg-amber-900/10 border border-amber-200/50 dark:border-amber-800/50 p-6 rounded-2xl text-stone-700 dark:text-stone-300 whitespace-pre-line">
                                {{ $character->notes }}
                            </div>
                        </div>
                    @endif
                </x-tabs.panel>

                <!-- Appearance Panel -->
                <x-tabs.panel name="appearance">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-stone-900 dark:text-white border-b border-stone-200 dark:border-stone-800 pb-2">
                                {{ __('Características Físicas') }}
                            </h3>
                            
                            <div class="grid grid-cols-1 gap-4">
                                @php
                                    $traits = [
                                        ['label' => 'Olhos', 'value' => $character->appearance['eyes'] ?? null, 'icon' => 'eye'],
                                        ['label' => 'Pele', 'value' => $character->appearance['skin'] ?? null, 'icon' => 'sparkles'],
                                        ['label' => 'Orelhas', 'value' => $character->appearance['ears'] ?? null, 'icon' => 'variable'],
                                        ['label' => 'Cauda', 'value' => $character->appearance['tail'] ?? null, 'icon' => 'swatch'],
                                        ['label' => 'Chifres', 'value' => $character->appearance['horns'] ?? null, 'icon' => 'stop'],
                                    ];
                                @endphp

                                @foreach($traits as $trait)
                                    <div class="flex items-center justify-between p-4 bg-stone-50 dark:bg-stone-900/40 rounded-xl border border-stone-100 dark:border-stone-800">
                                        <div class="flex items-center gap-3">
                                            <flux:icon :name="$trait['icon']" class="size-4 text-stone-400" />
                                            <span class="text-sm font-bold text-stone-600 dark:text-stone-400">{{ $trait['label'] }}</span>
                                        </div>
                                        <span class="text-sm font-bold text-stone-900 dark:text-white">{{ $trait['value'] ?: '--' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-stone-900 dark:text-white border-b border-stone-200 dark:border-stone-800 pb-2">
                                {{ __('Detalhes Visuais') }}
                            </h3>
                            <div class="bg-stone-50 dark:bg-stone-900/40 p-6 rounded-2xl border border-stone-100 dark:border-stone-800 text-sm text-stone-600 dark:text-stone-400 leading-relaxed italic">
                                {{ $character->appearance['description'] ?? __('Nenhum detalhe visual extra fornecido.') }}
                            </div>
                        </div>
                    </div>
                </x-tabs.panel>

                <!-- Inventory Panel -->
                <x-tabs.panel name="inventory">
                    @if(empty($character->inventory))
                        <div class="py-12 text-center text-stone-500 border border-dashed border-stone-300 dark:border-stone-700 rounded-2xl bg-stone-50/50 dark:bg-stone-900/30">
                            <flux:icon.archive-box class="size-12 mx-auto mb-4 text-stone-300 dark:text-stone-700" />
                            <p class="text-lg font-medium">{{ __('Inventário vazio.') }}</p>
                            <p class="text-sm text-stone-400">{{ __('Este personagem não possui itens registrados.') }}</p>
                        </div>
                    @else
                        <div class="bg-white dark:bg-stone-900/40 border border-stone-200 dark:border-stone-800 rounded-2xl overflow-hidden shadow-sm">
                            <table class="w-full text-sm text-left">
                                <thead class="text-[10px] uppercase tracking-widest text-stone-500 bg-stone-50 dark:bg-stone-950/50 border-b border-stone-200 dark:border-stone-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 font-black">{{ __('Item') }}</th>
                                        <th scope="col" class="px-6 py-4 font-black text-center w-24">{{ __('Qtd') }}</th>
                                        <th scope="col" class="px-6 py-4 font-black text-right w-24">{{ __('Peso') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-100 dark:divide-stone-800">
                                    @foreach($character->inventory as $key => $item)
                                        <tr class="hover:bg-stone-50/50 dark:hover:bg-stone-800/50 transition-colors group">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="size-8 rounded-lg bg-stone-100 dark:bg-stone-800 flex items-center justify-center text-stone-400 group-hover:text-amber-500 transition-colors">
                                                        <flux:icon.briefcase class="size-4" />
                                                    </div>
                                                    <span class="font-bold text-stone-900 dark:text-white">{{ $item['name'] ?? ucfirst($key) }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center font-mono font-bold text-stone-600 dark:text-stone-400">
                                                {{ $item['quantity'] ?? 1 }}
                                            </td>
                                            <td class="px-6 py-4 text-right font-mono text-stone-600 dark:text-stone-400 italic text-xs">
                                                {{ $item['weight'] ?? '--' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </x-tabs.panel>

                <!-- Social Panel -->
                <x-tabs.panel name="social">
                    @if(empty($character->relationships))
                        <div class="py-12 text-center text-stone-500 border border-dashed border-stone-300 dark:border-stone-700 rounded-2xl bg-stone-50/50 dark:bg-stone-900/30">
                            <flux:icon.user-plus class="size-12 mx-auto mb-4 text-stone-300 dark:text-stone-700" />
                            <p class="text-lg font-medium">{{ __('Sem relacionamentos.') }}</p>
                            <p class="text-sm text-stone-400">{{ __('Nenhum laço social ou inimizade registrada.') }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($character->relationships as $relation)
                                <div class="bg-white dark:bg-stone-900/40 p-6 rounded-2xl border border-stone-200 dark:border-stone-800 flex items-start gap-4">
                                    <div class="shrink-0 size-12 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center font-black text-lg">
                                        {{ substr($relation['name'] ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-black text-stone-900 dark:text-white leading-tight mb-1">{{ $relation['name'] ?? 'Unknown' }}</h4>
                                        <span class="text-[10px] uppercase font-bold text-amber-500 tracking-widest">{{ $relation['type'] ?? 'Relation' }}</span>
                                        <p class="mt-2 text-sm text-stone-600 dark:text-stone-400 italic">
                                            {{ $relation['description'] ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-tabs.panel>
            </x-tabs>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Campaign Status -->
            <div class="bg-glass p-6 rounded-2xl border border-stone-200/50 dark:border-stone-800/50 space-y-6">
                <div>
                    <h3 class="text-sm font-black text-stone-500 dark:text-stone-400 uppercase tracking-widest mb-4">{{ __('Status Atual') }}</h3>
                    <div class="flex items-center gap-3 bg-white/50 dark:bg-stone-950/50 p-4 rounded-xl border border-stone-100 dark:border-stone-800">
                        <div @class([
                            'size-3 rounded-full animate-pulse-glow',
                            'bg-green-500' => $character->status === 'Active',
                            'bg-red-500' => $character->status === 'Dead',
                            'bg-stone-500' => !in_array($character->status, ['Active', 'Dead']),
                        ])></div>
                        <span class="font-black text-stone-900 dark:text-white uppercase tracking-wider">{{ __($character->status) }}</span>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-black text-stone-500 dark:text-stone-400 uppercase tracking-widest mb-4">{{ __('Campanha Ativa') }}</h3>
                    @forelse($character->campaigns as $campaign)
                        <a href="{{ route('campaigns.play', $campaign) }}" class="block group">
                            <div class="bg-white/50 dark:bg-stone-950/50 p-4 rounded-xl border border-stone-100 dark:border-stone-800 group-hover:border-amber-500/50 transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-lg font-black text-stone-900 dark:text-white group-hover:text-amber-500 transition-colors">{{ $campaign->title }}</span>
                                    <flux:icon.arrow-right class="size-4 text-stone-400 group-hover:text-amber-500 transition-all translate-x-0 group-hover:translate-x-1" />
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-2 py-0.5 rounded font-bold uppercase">{{ $campaign->play_style }}</span>
                                    <span class="text-[10px] text-stone-400 font-bold uppercase tracking-tighter">{{ $campaign->difficulty }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8 bg-stone-50/50 dark:bg-stone-900/30 rounded-xl border border-dashed border-stone-200 dark:border-stone-800">
                            <p class="text-xs text-stone-500 font-bold uppercase tracking-widest">{{ __('Fora de Campanha') }}</p>
                            <p class="text-[10px] text-stone-400 mt-1">{{ __('Este personagem está livre no momento.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Info -->
            <div class="bg-stone-900 text-white p-8 rounded-2xl relative overflow-hidden group">
                <div class="absolute -right-8 -bottom-8 size-32 bg-amber-500/20 rounded-full blur-2xl group-hover:bg-amber-500/40 transition-all duration-700"></div>
                <h3 class="text-sm font-black text-amber-500/50 uppercase tracking-widest mb-6 relative z-10">{{ __('Perfil Rápido') }}</h3>
                
                <div class="space-y-4 relative z-10 font-mono text-xs">
                    <div class="flex justify-between border-b border-white/5 pb-2">
                        <span class="text-stone-500 uppercase">{{ __('Antecedente') }}</span>
                        <span class="font-bold">{{ $character->background }}</span>
                    </div>
                    <div class="flex justify-between border-b border-white/5 pb-2">
                        <span class="text-stone-500 uppercase">{{ __('Classe') }}</span>
                        <span class="font-bold">
                            @if(is_array($character->classes))
                                {{ collect($character->classes)->pluck('class')->implode(' / ') }}
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-white/5 pb-2">
                        <span class="text-stone-500 uppercase">{{ __('Iniciativa') }}</span>
                        <span class="text-amber-500 font-black">+{{ floor(($character->dexterity - 10) / 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500 uppercase">{{ __('CA') }}</span>
                        <span class="text-amber-500 font-black text-lg">{{ 10 + floor(($character->dexterity - 10) / 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>