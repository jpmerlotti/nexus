<?php

use Livewire\Component;

new class extends Component {
    //
}; ?>

<div>
    <h3 class="text-lg font-bold mb-4">Mapa e Localização</h3>
    <div class="space-y-4">
        <div class="w-full aspect-square bg-zinc-200 dark:bg-zinc-800 rounded-lg flex items-center justify-center">
            <flux:icon.map class="w-12 h-12 text-zinc-400" />
        </div>
        <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-center">
            <div class="font-medium">Localização Desconhecida</div>
            <div class="text-xs text-zinc-500 mt-1">Clima: Indefinido</div>
        </div>
    </div>
</div>