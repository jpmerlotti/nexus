<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;

new #[Title('New Chronicle')] class extends Component {
    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|string')]
    public $narration_detail_level = 'standard';

    #[Validate('required|string')]
    public $difficulty = 'normal';

    #[Validate('required|integer|min:1|max:20')]
    public $starting_level = 1;

    #[Validate('required|string')]
    public $play_style = 'mixed';

    #[Validate('required|string')]
    public $progression_type = 'milestone';

    public function save()
    {
        $validated = $this->validate();
        $validated['user_id'] = Auth::id();

        Campaign::create($validated);
        $campaign = Campaign::where('user_id', Auth::id())->latest()->first();

        $this->dispatch(
            'notify',
            title: __('Crônica Criada'),
            message: __('Sua nova aventura começa agora!'),
            type: 'success'
        );

        return $this->redirectRoute('campaigns.link-character', ['campaign' => $campaign->id], navigate: true);
    }
    public function fillPreset($type, $index)
    {
        $presets = static::getPresetsData();
        if (isset($presets[$type][$index])) {
            $preset = $presets[$type][$index];
            $this->title = __($preset['title']);
            $this->description = __($preset['desc']);
            $this->narration_detail_level = $preset['narration'];
            $this->difficulty = $preset['difficulty'];
            $this->starting_level = $preset['level'];
            $this->play_style = $preset['style'];
            $this->progression_type = $preset['progression'];

            $this->dispatch(
                'notify',
                title: __('Ajuste Aplicado'),
                message: __('Use como base e personalize como quiser!'),
                type: 'info'
            );
        }
    }

    public static function getPresetsData()
    {
        return [
            'easy' => [
                ['title' => 'O Mistério da Mina de Phandelver', 'desc' => 'Os heróis são contratados para escoltar uma carroça até uma vila fronteiriça, mas descobrem uma conspiração goblinóide envolvendo antigas forjas mágicas.', 'narration' => 'descriptive', 'difficulty' => 'story', 'level' => 1, 'style' => 'mixed', 'progression' => 'xp'],
                ['title' => 'Sombras sobre a Feira da Colheita', 'desc' => 'Durante o festival anual, crianças começam a desaparecer. Uma investigação leve leva a fadas preguiçosas pregando peças na floresta encantada.', 'narration' => 'standard', 'difficulty' => 'story', 'level' => 1, 'style' => 'roleplay_heavy', 'progression' => 'milestone'],
                ['title' => 'O Despertar do Rei Rato', 'desc' => 'Os esgotos da grande capital estão transbordando com ratos gigantes mutantes após o vazamento de um laboratório alquímico. Uma clássica missão de extermínio e masmorra introdutória.', 'narration' => 'concise', 'difficulty' => 'normal', 'level' => 1, 'style' => 'combat_heavy', 'progression' => 'milestone']
            ],
            'medium' => [
                ['title' => 'Aliança Quebrada de Prata', 'desc' => 'Duas cidades-estado élficas estão à beira da guerra civil após o roubo de uma relíquia sagrada. Os jogadores precisam atuar como diplomatas e espiões para descobrir o verdadeiro culpado.', 'narration' => 'descriptive', 'difficulty' => 'normal', 'level' => 3, 'style' => 'roleplay_heavy', 'progression' => 'milestone'],
                ['title' => 'Maldição do Navio Fantasma "Leviatã"', 'desc' => 'Uma embarcação infestada de mortos-vivos ressurge na baía costeira trazendo uma névoa letal. Os jogadores devem invadir o navio no mar e quebrar a âncora necromântica.', 'narration' => 'epic', 'difficulty' => 'hard', 'level' => 5, 'style' => 'exploration', 'progression' => 'milestone'],
                ['title' => 'O Labirinto do Minotauro Louco', 'desc' => 'Um rei exilado construiu um labirinto em constante mutação, cheio de armadilhas mortais e quebra-cabeças, escondendo um tesouro capaz de derrubar o atual império.', 'narration' => 'standard', 'difficulty' => 'normal', 'level' => 4, 'style' => 'exploration', 'progression' => 'xp']
            ],
            'hard' => [
                ['title' => 'Túmulo dos Deuses Esquecidos', 'desc' => 'Uma exploração brutal a uma pirâmide invertida no deserto de areias negras. Envolve demônios de alto escalão, armadilhas insta-kill e escassez extrema de suprimentos.', 'narration' => 'descriptive', 'difficulty' => 'lethal', 'level' => 10, 'style' => 'exploration', 'progression' => 'milestone'],
                ['title' => 'A Trama da Rainha Dragão', 'desc' => 'Um culto dracônico já infiltrou toda a alta nobreza bélica do continente. Os heróis estão sendo caçados pelo estado e precisam liderar uma resistência armada contra Dragões Anciões.', 'narration' => 'epic', 'difficulty' => 'hard', 'level' => 15, 'style' => 'mixed', 'progression' => 'milestone'],
                ['title' => 'Noite Eterna sobre Ravenloft', 'desc' => 'Os aventureiros são sugados para um semi-plano de terror governado por um vampiro milenar. Sobrevivência de terror psicológico, recursos escassos e a corrupção inevitável do alinhamento.', 'narration' => 'descriptive', 'difficulty' => 'lethal', 'level' => 8, 'style' => 'roleplay_heavy', 'progression' => 'milestone']
            ],
            'br' => [
                ['title' => 'A Febre do Ouro Amaldiçoado', 'desc' => 'Situado nas serras de "Minas de Ouro". O grupo investiga o caso do "Corpo-Seco", uma entidade necromântica que secou um rio inteiro e paralisou o garimpo local. Contém fadas tropicais e exploração de selva.', 'narration' => 'standard', 'difficulty' => 'story', 'level' => 1, 'style' => 'exploration', 'progression' => 'milestone'],
                ['title' => 'O Uivo da Besta Pampa', 'desc' => 'Nas frias planícies do sul profundo, estâncias estão sendo atacadas durante as luas de sangue. Uma guilda de caçadores (os "Peões") contrata os heróis para investigar a lenda do Lobisomem de Sete Pueblos, onde até mesmo o Capitão local parece esconder uma maldição.', 'narration' => 'descriptive', 'difficulty' => 'normal', 'level' => 5, 'style' => 'roleplay_heavy', 'progression' => 'milestone'],
                ['title' => 'O Império de Boitatá e o Fogo Fátuo', 'desc' => 'No coração de uma floresta equivalente à Amazônia mítica, bandeirantes arcanos tentam escravizar entidades da natureza. O grupo deve caçar a Mãe-do-Ouro e enfrentar xamãs corrompidos e a serpente de fogo primordial (com estatísticas de um Tarrasque/Dragão) para impedir a queima do pulmão do mundo.', 'narration' => 'epic', 'difficulty' => 'lethal', 'level' => 14, 'style' => 'combat_heavy', 'progression' => 'milestone']
            ]
        ];
    }
};
?>
<div>
    <div class="flex h-full w-full flex-1 flex-col gap-8 rounded-xl max-w-3xl mx-auto">
        <div>
            <flux:breadcrumbs class="mb-4">
                <flux:breadcrumbs.item href="{{ route('dashboard') }}" wire:navigate>{{ __('Dashboard') }}
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item href="{{ route('campaigns.index') }}" wire:navigate>{{ __('Chronicles') }}
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('Create') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <h1 class="text-3xl font-bold text-stone-900 dark:text-white mt-4 mb-2">{{ __('Create New Chronicle') }}
            </h1>
            <p class="text-stone-600 dark:text-stone-400">{{ __('Set the stage for your next great adventure.') }}</p>
        </div>

        <form wire:submit="save"
            class="space-y-6 bg-glass p-8 rounded-xl border border-stone-200/50 dark:border-stone-800/50 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-32 h-32 bg-amber-500/10 rounded-full blur-3xl pointer-events-none">
            </div>

            <div x-data="{ showPresets: false }" class="mb-4 text-sm relative z-10">
                <button type="button" @click="showPresets = !showPresets"
                    class="flex items-center gap-2 text-amber-600 dark:text-amber-500 font-medium hover:underline transition-all">
                    <flux:icon.sparkles class="size-4" />
                    {{ __("Don't know where to start? See some inspirations here.") }}
                </button>

                <div x-show="showPresets" x-collapse x-cloak class="mt-4">
                    <div x-data="{ activeTab: null }" class="space-y-3">

                        @php
                            $categories = [
                                'easy' => ['label' => 'Classic: Beginner (Easy)', 'color' => 'bg-green-500'],
                                'medium' => ['label' => 'Classic: Intermediate (Medium)', 'color' => 'bg-yellow-500'],
                                'hard' => ['label' => 'Classic: Advanced (Hard / Lethal)', 'color' => 'bg-red-500'],
                                'br' => ['label' => 'Brazilian Folklore (Custom Settings)', 'color' => 'bg-emerald-500'],
                            ];
                        @endphp

                        @foreach($categories as $type => $cat)
                            <div
                                class="bg-stone-50 dark:bg-stone-900/50 rounded-xl border border-stone-200 dark:border-stone-800 overflow-hidden">
                                <button type="button"
                                    @click="activeTab = activeTab === '{{ $type }}' ? null : '{{ $type }}'"
                                    class="w-full flex items-center justify-between p-4 text-left font-semibold text-stone-900 dark:text-white hover:bg-stone-100 dark:hover:bg-stone-800 transition-colors">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full {{ $cat['color'] }}"></div>
                                        {{ __($cat['label']) }}
                                    </div>
                                    <flux:icon.chevron-down class="size-4 transition-transform duration-200"
                                        x-bind:class="activeTab === '{{ $type }}' ? 'rotate-180' : ''" />
                                </button>
                                <div x-show="activeTab === '{{ $type }}'" x-collapse>
                                    <div class="p-4 pt-0 space-y-3">
                                        @foreach(static::getPresetsData()[$type] as $index => $preset)
                                            <div class="bg-white dark:bg-[#0c0a09] p-3 rounded-lg border border-stone-200 dark:border-stone-800 group hover:border-amber-500/50 transition-colors cursor-pointer"
                                                wire:click="fillPreset('{{ $type }}', {{ $index }}); showPresets = false">
                                                <h4
                                                    class="font-medium text-amber-600 dark:text-amber-500 group-hover:underline">
                                                    {{ __($preset['title']) }}
                                                </h4>
                                                <p class="text-stone-600 dark:text-stone-400 mt-1">{{ __($preset['desc']) }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <flux:input wire:model="title" label="{{ __('Campaign Title') }}" required />

            <flux:textarea wire:model="description" label="{{ __('Premise / Description') }}" rows="4" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:select wire:model="narration_detail_level" label="{{ __('Narration Detail') }}">
                    <flux:select.option value="concise">{{ __('Concise') }}</flux:select.option>
                    <flux:select.option value="standard">{{ __('Standard') }}</flux:select.option>
                    <flux:select.option value="descriptive">{{ __('Descriptive') }}</flux:select.option>
                    <flux:select.option value="epic">{{ __('Epic') }}</flux:select.option>
                </flux:select>

                <flux:select wire:model="difficulty" label="{{ __('Difficulty') }}">
                    <flux:select.option value="story">{{ __('Story Mode (Easy)') }}</flux:select.option>
                    <flux:select.option value="normal">{{ __('Normal') }}</flux:select.option>
                    <flux:select.option value="hard">{{ __('Hard (Tactical)') }}</flux:select.option>
                    <flux:select.option value="lethal">{{ __('Lethal') }}</flux:select.option>
                </flux:select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <flux:input wire:model="starting_level" label="{{ __('Starting Level') }}" type="number" min="1"
                    max="20" required />

                <flux:select wire:model="play_style" label="{{ __('Play Style') }}">
                    <flux:select.option value="combat_heavy">{{ __('Combat Focus') }}</flux:select.option>
                    <flux:select.option value="roleplay_heavy">{{ __('Roleplay Focus') }}</flux:select.option>
                    <flux:select.option value="exploration">{{ __('Exploration Focus') }}</flux:select.option>
                    <flux:select.option value="mixed">{{ __('Mixed') }}</flux:select.option>
                </flux:select>

                <flux:select wire:model="progression_type" label="{{ __('Progression') }}">
                    <flux:select.option value="milestone">{{ __('Milestone') }}</flux:select.option>
                    <flux:select.option value="xp">{{ __('Experience (XP)') }}</flux:select.option>
                </flux:select>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-stone-200 dark:border-stone-800">
                <flux:button href="{{ route('campaigns.index') }}" variant="ghost" wire:navigate>{{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">{{ __('Create Chronicle') }}</flux:button>
            </div>
        </form>
    </div>
</div>