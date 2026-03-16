<?php

use Livewire\Component;

new class extends Component {
    public ?int $characterId = null;

    public function mount(?int $characterId = null)
    {
        $this->characterId = $characterId;
    }

    public function getInventoryProperty()
    {
        if (!$this->characterId) {
            return [];
        }
        return \App\Models\Character::find($this->characterId)?->inventory ?? [];
    }
}; ?>

<div>
    <h3 class="text-lg font-bold mb-4">Inventário</h3>
    <div class="space-y-3">
        <div class="p-3 bg-amber-500/5 border border-amber-500/10 rounded-xl flex justify-between items-center group">
            <div class="flex items-center gap-2">
                <flux:icon.banknotes class="w-4 h-4 text-amber-500" />
                <span class="text-xs font-serif tracking-widest text-amber-200/50 uppercase">Riquezas</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="text-sm font-bold text-amber-400 font-mono">0</span>
                <span class="text-[10px] text-amber-600 font-bold uppercase">PO</span>
            </div>
        </div>

        @forelse($this->inventory as $item)
            <div class="p-3 bg-stone-100 dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl group hover:border-amber-500/30 transition-colors"
                wire:key="item-{{ $loop->index }}">
                <div class="flex justify-between items-start mb-1">
                    <div
                        class="text-sm font-bold text-stone-200 group-hover:text-amber-400 transition-colors font-serif uppercase tracking-wider">
                        {{ $item['name'] ?? 'Item Desconhecido' }}
                    </div>
                    <span
                        class="text-[9px] px-1.5 py-0.5 bg-stone-800 text-stone-500 rounded border border-white/5 uppercase font-bold">{{ $item['type'] ?? 'Item' }}</span>
                </div>
                @if(isset($item['description']))
                    <div class="text-[11px] text-stone-500 leading-relaxed font-serif">{{ $item['description'] }}</div>
                @endif
                @if($item['equipped'] ?? false)
                    <div class="mt-2 flex items-center gap-1.5">
                        <div class="w-1 h-1 rounded-full bg-amber-500 animate-pulse"></div>
                        <span class="text-[9px] text-amber-500/70 uppercase tracking-widest font-bold">Equipado</span>
                    </div>
                @endif
            </div>
        @empty
            <div
                class="text-center py-12 border-2 border-dashed border-stone-800 rounded-3xl opacity-40 grayscale space-y-3">
                <flux:icon.briefcase class="w-8 h-8 mx-auto text-stone-500" />
                <p class="text-[10px] font-serif tracking-[.2em] uppercase text-stone-400">Sacola de Viagem Vazia</p>
            </div>
        @endforelse
    </div>
</div>