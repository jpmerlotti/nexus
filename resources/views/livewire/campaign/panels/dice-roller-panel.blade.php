<?php

use Livewire\Component;

new class extends Component {
    //
}; ?>

<div>
    <h3 class="text-lg font-bold mb-4">Rolador de Dados</h3>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach ([4, 6, 8, 10, 12, 20, 100] as $sides)
            <button x-on:click="$wire.$parent.rollDice('1d{{ $sides }}')"
                class="relative flex flex-col items-center justify-center p-4 rounded-2xl bg-stone-100 dark:bg-stone-900 border border-stone-200 dark:border-stone-800 hover:border-amber-500/50 hover:bg-stone-200 dark:hover:bg-stone-800 transition-all group overflow-hidden shadow-sm">

                <div
                    class="absolute -inset-1 bg-gradient-to-tr from-amber-500/0 via-amber-500/5 to-amber-500/0 opacity-0 group-hover:opacity-100 blur-md transition-opacity">
                </div>

                <div class="relative z-10 flex flex-col items-center">
                    <span
                        class="text-2xl mb-1 group-hover:scale-110 transition-transform grayscale group-hover:grayscale-0">🎲</span>
                    <span
                        class="text-[10px] font-bold text-stone-500 uppercase tracking-widest group-hover:text-amber-500 transition-colors">d{{ $sides }}</span>
                </div>
            </button>
        @endforeach
    </div>

    <div class="mt-8">
        <div class="text-[10px] font-bold text-stone-500 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
            <div class="w-1 h-1 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></div>
            Resultados Mensurados
        </div>

        <div
            class="p-4 bg-stone-950/50 rounded-2xl border border-white/5 space-y-3 min-h-[100px] flex flex-col items-center justify-center text-center">
            <p class="text-[11px] text-stone-500 font-serif italic max-w-[200px]">
                Os resultados dos dados rolados aqui serão registrados na mesa central para todos os jogadores.
            </p>
        </div>
    </div>
</div>