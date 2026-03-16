<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use App\Models\Character;
use Illuminate\Support\Facades\Auth;

new #[Title('Novo Personagem')] class extends Component {
    public ?string $redirect = null;

    public function mount()
    {
        $this->redirect = request()->query('redirect');
    }

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required')]
    public $race = '';

    #[Validate('nullable')]
    public $background = '';

    #[Validate('nullable')]
    public $alignment = '';

    #[Validate('required|string')]
    public $status = 'Active';

    public $notes = '';

    // Multi-class support
    public $classes = [
        ['class' => '', 'level' => 1]
    ];

    #[Validate('required|integer|min:1')]
    public $max_hp = 10;

    #[Validate('nullable|integer|min:0')]
    public $current_hp = 10;

    #[Validate('nullable|integer|min:0')]
    public $current_xp = 0;

    #[Validate('required|integer|min:1|max:30')]
    public $strength = 10;

    #[Validate('required|integer|min:1|max:30')]
    public $dexterity = 10;

    #[Validate('required|integer|min:1|max:30')]
    public $constitution = 10;

    #[Validate('required|integer|min:1|max:30')]
    public $intelligence = 10;

    #[Validate('required|integer|min:1|max:30')]
    public $wisdom = 10;

    #[Validate('required|integer|min:1|max:30')]
    public $charisma = 10;

    // Inventory
    public $inventory = [];

    // Relationships
    public $relationships = [];

    // Backstory & Appearance
    #[Validate('nullable|string')]
    public $backstory = '';

    public $appearance = [
        'eyes' => '',
        'skin' => '',
        'ears' => '',
        'tail' => '',
        'horns' => '',
        'other' => '',
    ];

    public function rules()
    {
        return [
            'race' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\CharacterRace::class)],
            'background' => ['nullable', \Illuminate\Validation\Rule::enum(\App\Enums\CharacterBackground::class)],
            'alignment' => ['nullable', 'string', 'max:255'],

            'classes.*.class' => 'required|string|max:255',
            'classes.*.level' => 'required|integer|min:1|max:20',

            'inventory.*.name' => 'required|string|max:255',
            'inventory.*.quantity' => 'required|integer|min:1',
            'inventory.*.weight' => 'nullable|numeric|min:0',
            'inventory.*.description' => 'nullable|string|max:500',
            'inventory.*.type' => 'required|string|in:Weapon,Armor,Gear,Magic,Potion,Other',
            'inventory.*.equipped' => 'boolean',

            'relationships.*.name' => 'required|string|max:255',
            'relationships.*.type' => 'required|string|max:255',
            'relationships.*.description' => 'nullable|string|max:1000',
        ];
    }

    public function addClass()
    {
        $this->classes[] = ['class' => '', 'level' => 1];
    }

    public function removeClass($index)
    {
        unset($this->classes[$index]);
        $this->classes = array_values($this->classes);
    }

    public function addItem()
    {
        $this->inventory[] = [
            'name' => '',
            'quantity' => 1,
            'weight' => 0,
            'description' => '',
            'type' => 'Gear',
            'equipped' => false
        ];
    }

    public function removeItem($index)
    {
        unset($this->inventory[$index]);
        $this->inventory = array_values($this->inventory);
    }

    public function addRelationship()
    {
        $this->relationships[] = ['name' => '', 'type' => '', 'description' => ''];
    }

    public function removeRelationship($index)
    {
        unset($this->relationships[$index]);
        $this->relationships = array_values($this->relationships);
    }

    public function save()
    {
        $validated = $this->validate();

        $validated['user_id'] = Auth::id();

        if (empty($validated['current_hp']) && $validated['current_hp'] !== 0 && $validated['current_hp'] !== '0') {
            $validated['current_hp'] = $validated['max_hp'];
        }

        // Compute total level automatically
        $totalLevel = 0;
        foreach ($this->classes as $c) {
            $totalLevel += (int) $c['level'];
        }
        $validated['level'] = $totalLevel > 0 ? $totalLevel : 1;
        $validated['classes'] = $this->classes;
        $validated['inventory'] = $this->inventory;
        $validated['backstory'] = $this->backstory;
        $validated['appearance'] = $this->appearance;
        $validated['relationships'] = $this->relationships;
        $validated['notes'] = $this->notes;
        $validated['status'] = $this->status;

        Character::create($validated);

        $this->dispatch(
            'notify',
            title: __('Ficha Forjada'),
            message: __('Seu personagem foi criado com sucesso!'),
            type: 'success'
        );

        if ($this->redirect) {
            return $this->redirect($this->redirect, navigate: true);
        }

        return $this->redirectRoute('characters.index', navigate: true);
    }
};
?>
<div class="flex h-full w-full flex-1 flex-col gap-8 rounded-xl max-w-4xl mx-auto px-4 py-8">
    <div>
        <flux:breadcrumbs class="mb-4">
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" wire:navigate>{{ __('Dashboard') }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ $redirect ?? route('characters.index') }}" wire:navigate>
                {{ __('Characters') }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('Novo') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <h1 class="text-3xl font-black text-stone-900 dark:text-white mt-4 mb-2">{{ __('Criar Novo Personagem') }}</h1>
        <p class="text-stone-600 dark:text-stone-400 font-medium">{{ __('Forje a lenda do seu próximo herói.') }}</p>
    </div>

    <form wire:submit="save"
        class="space-y-8 bg-glass p-8 rounded-2xl border border-stone-200/50 dark:border-stone-800/50 relative overflow-hidden shadow-xl">
        <div class="absolute -left-4 -top-4 w-32 h-32 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <x-tabs default="main">
            <div class="flex border-b border-stone-200 dark:border-stone-800 mb-6 overflow-x-auto no-scrollbar">
                <x-tabs.tab name="main" icon="user">{{ __('Principal') }}</x-tabs.tab>
                <x-tabs.tab name="appearance" icon="pencil-square">{{ __('História & Aparência') }}</x-tabs.tab>
                <x-tabs.tab name="inventory" icon="briefcase">{{ __('Inventário') }}</x-tabs.tab>
                <x-tabs.tab name="social" icon="users">{{ __('Social & Notas') }}</x-tabs.tab>
            </div>

            <x-tabs.panel name="main" class="space-y-8 pt-2">
                <div class="space-y-6">
                    <h2
                        class="text-lg font-black text-stone-900 dark:text-white border-b border-stone-200 dark:border-stone-800 pb-2 flex items-center gap-2">
                        <flux:icon.user class="size-5 text-amber-500" />
                        {{ __('Informações Básicas') }}
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model="name" label="{{ __('Character Name') }}" required />

                        <flux:select wire:model="status" label="{{ __('Status') }}" required>
                            <option value="Active">{{ __('Ativo') }}</option>
                            <option value="Dead">{{ __('Morto') }}</option>
                            <option value="Inactive">{{ __('Inativo') }}</option>
                            <option value="Retired">{{ __('Aposentado') }}</option>
                        </flux:select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @php
                            $raceOptions = array_map(function ($race) {
                                return [
                                    'value' => $race->value,
                                    'title' => $race->getLabel()
                                ];
                            }, \App\Enums\CharacterRace::cases());
                        @endphp
                        <x-rich-select wire:model="race" label="{{ __('Race') }}"
                            placeholder="{{ __('Select a Race') }}" :options="$raceOptions" />

                        @php
                            $backgroundOptions = array_map(function ($bg) {
                                return [
                                    'value' => $bg->value,
                                    'title' => $bg->getLabel(),
                                    'description' => $bg->getDescription()
                                ];
                            }, \App\Enums\CharacterBackground::cases());
                        @endphp
                        <x-rich-select wire:model="background" label="{{ __('Background') }}"
                            placeholder="{{ __('Select a Background') }}" :options="$backgroundOptions" />
                    </div>

                    <div class="pt-6 border-t border-stone-200 dark:border-stone-800">
                        <flux:radio.group wire:model="alignment" label="{{ __('Alignment') }}" variant="cards"
                            class="flex-col">
                            @php
                                $alignments = [
                                    ['value' => 'Lawful Good', 'label' => __('L. Good'), 'desc' => __('Ordem & Bondade')],
                                    ['value' => 'Neutral Good', 'label' => __('N. Good'), 'desc' => __('Fazer o Bem')],
                                    ['value' => 'Chaotic Good', 'label' => __('C. Good'), 'desc' => __('Rebelde')],
                                    ['value' => 'Lawful Neutral', 'label' => __('L. Neutral'), 'desc' => __('Juiz')],
                                    ['value' => 'True Neutral', 'label' => __('T. Neutral'), 'desc' => __('Equilíbrio')],
                                    ['value' => 'Chaotic Neutral', 'label' => __('C. Neutral'), 'desc' => __('Livre')],
                                    ['value' => 'Lawful Evil', 'label' => __('L. Evil'), 'desc' => __('Tirano')],
                                    ['value' => 'Neutral Evil', 'label' => __('N. Evil'), 'desc' => __('Egoísta')],
                                    ['value' => 'Chaotic Evil', 'label' => __('C. Evil'), 'desc' => __('Destruidor')],
                                ];
                            @endphp

                            <div class="grid grid-cols-3 gap-3">
                                @foreach($alignments as $align)
                                    <flux:radio value="{{ $align['value'] }}" label="{{ $align['label'] }}"
                                        description="{{ $align['desc'] }}" />
                                @endforeach
                            </div>
                        </flux:radio.group>
                    </div>
                </div>

                <div class="space-y-6 pt-6 border-t border-stone-200 dark:border-stone-800">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-md font-black text-stone-800 dark:text-stone-200 flex items-center gap-2">
                            <flux:icon.academic-cap class="size-5 text-amber-500" />
                            {{ __('Classes & Progressão') }}
                        </h3>
                        <flux:button size="sm" variant="subtle" icon="plus" wire:click="addClass" type="button"
                            class="!bg-stone-100 dark:!bg-stone-800">
                            {{ __('Add Class') }}
                        </flux:button>
                    </div>

                    <div class="space-y-4">
                        @php
                            $classOptions = array_map(function ($class) {
                                return [
                                    'value' => $class->value,
                                    'title' => $class->getLabel()
                                ];
                            }, \App\Enums\CharacterClass::cases());
                        @endphp
                        @foreach($classes as $index => $classItem)
                            <div class="flex items-end gap-4 p-4 bg-stone-50 dark:bg-stone-900/40 rounded-xl border border-stone-100 dark:border-stone-800 shadow-sm"
                                wire:key="class-{{ $index }}">
                                <div class="flex-1">
                                    <x-rich-select wire:model="classes.{{ $index }}.class" label="{{ __('Class Name') }}"
                                        placeholder="{{ __('Select a Class') }}" :options="$classOptions" />
                                </div>
                                <div class="w-32">
                                    <flux:input wire:model="classes.{{ $index }}.level" label="{{ __('Level') }}"
                                        type="number" min="1" max="20" required />
                                </div>
                                @if(count($classes) > 1)
                                    <div class="mb-1">
                                        <flux:button variant="danger" icon="trash" wire:click="removeClass({{ $index }})"
                                            type="button" class="!text-red-500 hover:!bg-red-500/10" />
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-stone-200 dark:border-stone-800">
                    <div class="space-y-4">
                        <h3 class="text-md font-black text-stone-800 dark:text-stone-200 flex items-center gap-2">
                            <flux:icon.heart class="size-5 text-red-500" />
                            {{ __('Vibração & Energia') }}
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input wire:model="max_hp" label="{{ __('Max HP') }}" type="number" required />
                            <flux:input wire:model="current_hp" label="{{ __('Current HP') }}" type="number" />
                        </div>
                        <flux:input wire:model="current_xp" label="{{ __('Experiência (XP)') }}" type="number" />
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-md font-black text-stone-800 dark:text-stone-200 flex items-center gap-2">
                            <flux:icon.presentation-chart-bar class="size-5 text-amber-500" />
                            {{ __('Atributos Base') }}
                        </h3>
                        <div class="grid grid-cols-3 gap-3">
                            <flux:input wire:model="strength" label="{{ __('FOR') }}" type="number" required />
                            <flux:input wire:model="dexterity" label="{{ __('DES') }}" type="number" required />
                            <flux:input wire:model="constitution" label="{{ __('CON') }}" type="number" required />
                            <flux:input wire:model="intelligence" label="{{ __('INT') }}" type="number" required />
                            <flux:input wire:model="wisdom" label="{{ __('SAB') }}" type="number" required />
                            <flux:input wire:model="charisma" label="{{ __('CAR') }}" type="number" required />
                        </div>
                    </div>
                </div>
            </x-tabs.panel>

            <x-tabs.panel name="appearance" class="space-y-8 pt-2">
                <div class="space-y-6">
                    <h2
                        class="text-lg font-black text-stone-900 dark:text-white border-b border-stone-200 dark:border-stone-800 pb-2 flex items-center gap-2">
                        <flux:icon.sparkles class="size-5 text-amber-500" />
                        {{ __('Aparência & Identidade') }}
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <flux:input wire:model="appearance.eyes" label="{{ __('Olhos') }}"
                            placeholder="{{ __('ex: Verdes, Brilhantes') }}" />
                        <flux:input wire:model="appearance.skin" label="{{ __('Pele') }}"
                            placeholder="{{ __('ex: Alva, Escamosa') }}" />
                        <flux:input wire:model="appearance.ears" label="{{ __('Orelhas') }}"
                            placeholder="{{ __('ex: Pontudas, Pequenas') }}" />
                        <flux:input wire:model="appearance.tail" label="{{ __('Cauda') }}"
                            placeholder="{{ __('ex: Felina, Longa') }}" />
                        <flux:input wire:model="appearance.horns" label="{{ __('Chifres') }}"
                            placeholder="{{ __('ex: Curvados, Curtos') }}" />
                        <flux:input wire:model="appearance.other" label="{{ __('Outros') }}"
                            placeholder="{{ __('ex: Tatuagens, Cicatrizes') }}" />
                    </div>
                </div>

                <div class="space-y-4 pt-6 border-t border-stone-200 dark:border-stone-800">
                    <h2
                        class="text-lg font-black text-stone-900 dark:text-white border-b border-stone-200 dark:border-stone-800 pb-2 flex items-center gap-2">
                        <flux:icon.book-open class="size-5 text-amber-500" />
                        {{ __('História do Personagem') }}
                    </h2>
                    <x-editor wire:model="backstory"
                        placeholder="{{ __('Conte a jornada do seu herói até este momento...') }}" />
                </div>
            </x-tabs.panel>

            <x-tabs.panel name="inventory" class="space-y-8 pt-2">
                <div class="space-y-6">
                    <div class="flex justify-between items-center border-b border-stone-200 dark:border-stone-800 pb-2">
                        <h2 class="text-lg font-black text-stone-900 dark:text-white flex items-center gap-2">
                            <flux:icon.beaker class="size-5 text-amber-500" />
                            {{ __('Equipamento & Recursos') }}
                        </h2>
                        <flux:button size="sm" variant="subtle" icon="plus" wire:click="addItem" type="button"
                            class="!bg-stone-100 dark:!bg-stone-800">
                            {{ __('Add Item') }}
                        </flux:button>
                    </div>

                    @if(count($inventory) === 0)
                        <div
                            class="py-12 text-center text-stone-500 border border-dashed border-stone-300 dark:border-stone-700 rounded-2xl bg-stone-50/50 dark:bg-stone-900/30">
                            <flux:icon.archive-box class="size-12 mx-auto mb-4 text-stone-300 dark:text-stone-700" />
                            <p class="font-bold">{{ __('Nenhum item carregado.') }}</p>
                            <p class="text-xs">{{ __('Este explorador está de mãos vazias.') }}</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-6">
                            @foreach($inventory as $index => $item)
                                <div class="p-6 bg-stone-50 dark:bg-stone-900/40 rounded-2xl border border-stone-100 dark:border-stone-800 relative group"
                                    wire:key="item-{{ $index }}">
                                    <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <flux:button variant="danger" size="sm" icon="trash"
                                            wire:click="removeItem({{ $index }})" type="button"
                                            class="!text-red-500 hover:!bg-red-500/10" />
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                                        <!-- Basic Info Row -->
                                        <div class="md:col-span-5">
                                            <flux:input wire:model="inventory.{{ $index }}.name" label="{{ __('Item Name') }}"
                                                placeholder="{{ __('ex: Espada Longa') }}" required />
                                        </div>
                                        <div class="md:col-span-3">
                                            <flux:select wire:model="inventory.{{ $index }}.type" label="{{ __('Tipo') }}">
                                                <option value="Weapon">{{ __('Arma') }}</option>
                                                <option value="Armor">{{ __('Armadura') }}</option>
                                                <option value="Gear">{{ __('Equipamento') }}</option>
                                                <option value="Magic">{{ __('Item Mágico') }}</option>
                                                <option value="Potion">{{ __('Poção') }}</option>
                                                <option value="Other">{{ __('Outro') }}</option>
                                            </flux:select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <flux:input wire:model="inventory.{{ $index }}.quantity" label="{{ __('Qtd') }}"
                                                type="number" min="1" required />
                                        </div>
                                        <div class="md:col-span-2">
                                            <flux:input wire:model="inventory.{{ $index }}.weight" label="{{ __('Peso') }}"
                                                type="number" step="0.1" min="0" />
                                        </div>

                                        <!-- Details Row -->
                                        <div class="md:col-span-10">
                                            <flux:textarea wire:model="inventory.{{ $index }}.description"
                                                label="{{ __('Descrição/Efeitos') }}" rows="2"
                                                placeholder="{{ __('Detalhes sobre o item, bônus, etc.') }}" />
                                        </div>
                                        <div class="md:col-span-2 pt-6">
                                            <div class="flex items-center gap-2">
                                                <flux:switch wire:model="inventory.{{ $index }}.equipped"
                                                    label="{{ __('Equip.') }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </x-tabs.panel>

            <x-tabs.panel name="social" class="space-y-8 pt-2">
                <div class="space-y-6">
                    <div class="flex justify-between items-center border-b border-stone-200 dark:border-stone-800 pb-2">
                        <h2 class="text-lg font-black text-stone-900 dark:text-white flex items-center gap-2">
                            <flux:icon.user-group class="size-5 text-amber-500" />
                            {{ __('Rede Social & Relacionamentos') }}
                        </h2>
                        <flux:button size="sm" variant="subtle" icon="plus-circle" wire:click="addRelationship"
                            type="button" class="!bg-stone-100 dark:!bg-stone-800">
                            {{ __('Adicionar Relacionamento') }}
                        </flux:button>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        @forelse($relationships as $index => $relation)
                            <div class="p-6 bg-stone-50 dark:bg-stone-900/40 rounded-2xl border border-stone-100 dark:border-stone-800 relative group"
                                wire:key="relation-{{ $index }}">
                                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <flux:button variant="danger" size="sm" icon="trash"
                                        wire:click="removeRelationship({{ $index }})" type="button"
                                        class="!text-red-500 hover:!bg-red-500/10" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <flux:input wire:model="relationships.{{ $index }}.name"
                                        label="{{ __('Nome do Contato/NPC') }}" required />
                                    <flux:input wire:model="relationships.{{ $index }}.type"
                                        label="{{ __('Natureza (ex: Aliado, Rival, Mentor)') }}" required />
                                    <div class="md:col-span-2">
                                        <flux:textarea wire:model="relationships.{{ $index }}.description"
                                            label="{{ __('Descrição do Laço') }}" rows="2" />
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="py-12 text-center text-stone-500 border border-dashed border-stone-300 dark:border-stone-700 rounded-2xl bg-stone-50/50 dark:bg-stone-900/30">
                                <flux:icon.users class="size-12 mx-auto mb-4 text-stone-300 dark:text-stone-700" />
                                <p class="font-bold">{{ __('Sem conexões registradas.') }}</p>
                                <p class="text-xs">{{ __('Este personagem caminha sozinho por enquanto.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="space-y-4 pt-6 border-t border-stone-200 dark:border-stone-800">
                    <h2
                        class="text-lg font-black text-stone-900 dark:text-white border-b border-stone-200 dark:border-stone-800 pb-2 flex items-center gap-2">
                        <flux:icon.pencil-square class="size-5 text-amber-500" />
                        {{ __('Anotações Confidenciais') }}
                    </h2>
                    <flux:textarea wire:model="notes"
                        placeholder="{{ __('Segredos, descobertas ou lembretes importantes...') }}" rows="6" />
                </div>
            </x-tabs.panel>
        </x-tabs>

        <div class="flex justify-end gap-3 pt-6 border-t border-stone-200 dark:border-stone-800">
            <flux:button href="{{ $redirect ?? route('characters.index') }}" variant="ghost" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary" class="!bg-amber-500 hover:!bg-amber-600 border-none px-8">
                {{ __('Forjar Lenda') }}
            </flux:button>
        </div>
    </form>
</div>