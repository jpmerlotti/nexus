<?php

use Livewire\Component;
use App\Models\Character;

new class extends Component {
    public ?int $characterId = null;
    public ?Character $character = null;

    public function mount(?int $characterId = null)
    {
        $this->characterId = $characterId;
        $this->loadCharacter();
    }

    public function loadCharacter()
    {
        if ($this->characterId) {
            $this->character = Character::find($this->characterId);
        }
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="p-6 text-center text-stone-500 animate-pulse">
            Carregando ficha...
        </div>
        HTML;
    }
}; ?>

<div>
    <h3 class="text-lg font-bold mb-4">Personagem</h3>

    @if($character)
        <div class="space-y-4">
            <div
                class="p-4 bg-stone-100 dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 shadow-sm">
                <div class="flex items-center gap-4 mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($character->name) }}&background=1c1917&color=fbbf24"
                        class="w-12 h-12 rounded-full border border-amber-500/30" />
                    <div>
                        <div class="text-xs text-stone-500 uppercase tracking-widest font-bold">Lenda Nível 1</div>
                        <div class="text-xl font-serif text-white">{{ $character->name }}</div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center text-xs text-stone-400 uppercase tracking-tighter">
                        <span>Pontos de Vida</span>
                        <span>{{ $character->current_hp }} / {{ $character->max_hp }}</span>
                    </div>
                    <div class="w-full h-2 bg-stone-800 rounded-full overflow-hidden border border-white/5">
                        <div class="h-full bg-gradient-to-r from-emerald-600 to-emerald-400 shadow-[0_0_10px_rgba(16,185,129,0.3)] transition-all duration-500"
                            style="width: {{ ($character->current_hp / $character->max_hp) * 100 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 bg-stone-100 dark:bg-stone-900 rounded-xl border border-stone-200 dark:border-stone-800">
                    <div class="text-[10px] text-stone-500 uppercase font-bold mb-1">Raça</div>
                    <div class="text-sm font-serif text-amber-500">{{ $character->race?->getLabel() ?? 'Humano' }}</div>
                </div>
                <div class="p-3 bg-stone-100 dark:bg-stone-900 rounded-xl border border-stone-200 dark:border-stone-800">
                    <div class="text-[10px] text-stone-500 uppercase font-bold mb-1">Classe</div>
                    @php
                        $classLabel = collect($character->classes ?? [])
                            ->map(fn($c) => \App\Enums\CharacterClass::tryFrom($c['class'] ?? '')?->getLabel() ?? ($c['class'] ?? ''))
                            ->implode(' / ') ?: 'Guerreiro';
                    @endphp
                    <div class="text-sm font-serif text-amber-500">{{ $classLabel }}</div>
                </div>
            </div>

            <div
                class="p-4 bg-stone-950/30 rounded-2xl border border-white/5 text-[11px] text-stone-500 text-center italic font-serif leading-relaxed">
                As estatísticas de combate e perícias serão atualizadas automaticamente durante a narrativa.
            </div>
        </div>
    @else
        <div
            class="text-sm text-zinc-500 text-center py-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-lg">
            Nenhum personagem vinculado.
        </div>
    @endif
</div>