<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Campaign;
use App\AI\Orchestrator;
use App\AI\Drivers\GeminiDriver;
use App\AI\Tools\DiceRollerTool;
use App\AI\Tools\InventoryManagerTool;
use App\AI\Tools\ContextQueryTool;
use App\Models\StoryLog;

new #[Title('Campaign Play')] #[Layout('layouts.campaign')] class extends Component {
    public Campaign $campaign;
    public ?int $characterId = null;
    public int $nexBalance = 0;
    public string $activePanel = 'chat';
    public bool $isSessionZero = false;

    // Controls which channel we are chatting in
    public string $currentChannel = 'ic';

    public string $newMessage = '';
    public bool $isAiThinking = false;
    public array $chatMessages = [];
    public array $metaMessages = [];

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;

        // If there is only one character, auto-select it
        if ($campaign->characters()->count() === 1) {
            $this->characterId = $campaign->characters()->first()->id;
        }

        $this->nexBalance = auth()->user()->fresh()->nex_balance;
        $this->loadMessages();
        
        if (empty($this->chatMessages) && empty($this->metaMessages)) {
            $this->isSessionZero = true;
        }
    }

    public function loadMessages()
    {
        $allMessages = StoryLog::where('campaign_id', $this->campaign->id)
            ->whereIn('role', ['user', 'assistant'])
            ->orderBy('created_at', 'asc')
            ->get();

        $this->chatMessages = $allMessages->where('channel', 'ic')
            ->map(fn($log) => ['role' => $log->role, 'content' => ['text' => $log->content]])
            ->toArray();

        $this->metaMessages = $allMessages->where('channel', 'meta')
            ->map(fn($log) => ['role' => $log->role, 'content' => ['text' => $log->content]])
            ->toArray();
    }

    public function sendMessage()
    {
        $text = trim($this->newMessage);
        if (empty($text))
            return;

        $user = auth()->user();

        // Handle Slash Commands
        if (str_starts_with($text, '/roll')) {
            $expression = trim(str_replace('/roll', '', $text));
            $this->rollDice($expression ?: '1d20');
            $this->newMessage = '';
            return;
        }

        if (strtolower($text) === 'começar' && $this->isSessionZero) {
             $this->isSessionZero = false;
             // We can trigger a special start message here or just proceed
        }

        if ($this->currentChannel === 'ic' && !$user->hasEnoughNex(1)) {
            $this->dispatch('notify', 
                title: __('Sem Nex suficiente!'), 
                message: __('Você precisa de Nex para continuar a história. Complete missões ou use sua própria chave API.'), 
                type: 'warning'
            );
            return;
        }

        // Just show the user message locally first
        if ($this->currentChannel === 'ic') {
            $this->chatMessages[] = ['role' => 'user', 'content' => ['text' => $text]];
        } else {
            $this->metaMessages[] = ['role' => 'user', 'content' => ['text' => $text]];
        }

        $this->newMessage = '';
        $this->isAiThinking = true;

        $this->dispatch('generateAiResponseContent', message: $text, channel: $this->currentChannel);
        $this->dispatch('chat-updated');
    }

    #[\Livewire\Attributes\On('generateAiResponseContent')]
    public function generateAiResponse(string $message, string $channel = 'ic')
    {
        try {
            $character = $this->characterId ? \App\Models\Character::find($this->characterId) : null;
            $driver = new GeminiDriver();
            $orchestrator = new Orchestrator($driver, $this->campaign, $character);

            // Register Tools
            $orchestrator->registerTool(new DiceRollerTool())
                ->registerTool(new InventoryManagerTool())
                ->registerTool(new ContextQueryTool());

            $response = $orchestrator->interact($message, $channel);

            // Update balance after interaction
            $this->nexBalance = auth()->user()->fresh()->nex_balance;

            $this->loadMessages();
        } catch (\Exception $e) {
            $this->dispatch('notify', 
                title: __('Erro no Nexus'), 
                message: $e->getMessage(), 
                type: 'danger'
            );

            Log::error("AI Interaction Error: " . $e->getMessage());
        }

        $this->isAiThinking = false;
        $this->dispatch('chat-updated');
    }

    public function switchChannel(string $channel)
    {
        $this->currentChannel = $channel;
        $this->dispatch('chat-updated');
    }

    public function selectCharacter(int $id)
    {
        $this->characterId = $id;
        $this->dispatch('notify', 
            title: __('Personagem Selecionado'), 
            message: __('Sua ficha foi carregada com sucesso.'), 
            type: 'success'
        );

        $this->loadMessages();
    }

    public function openPanel(string $panel)
    {
        $this->activePanel = $panel;
        $this->dispatch('chat-updated');
    }

    public function rollDice(string $expression)
    {
        $this->isAiThinking = true;
        
        try {
            $tool = new DiceRollerTool();
            $result = $tool->handle(['expression' => $expression]);
            
            if ($result['success']) {
                $content = "🎲 **Resultado do Dado:** {$result['message']}";
                
                StoryLog::create([
                    'campaign_id' => $this->campaign->id,
                    'character_id' => $this->characterId,
                    'role' => 'assistant', // System message
                    'channel' => $this->currentChannel,
                    'content' => $content,
                    'metadata' => ['dice_roll' => $result],
                ]);
                
                $this->loadMessages();
            } else {
                $this->dispatch('notify', 
                    title: __('Erro ao rolar dados'), 
                    message: $result['message'], 
                    type: 'danger'
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', 
                title: __('Erro no Nexus'), 
                message: $e->getMessage(), 
                type: 'danger'
            );
        }
        
        $this->isAiThinking = false;
        $this->dispatch('chat-updated');
    }
}; ?>

<div class="flex h-screen overflow-hidden bg-stone-900 text-stone-100 relative" 
     x-data="campaignPlay"
     @chat-updated.window="scrollToBottom()">

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('campaignPlay', () => ({
                mobileMenuOpen: false,
                activePanel: @entangle('activePanel'),
                currentChannel: @entangle('currentChannel'),
                commandPaletteOpen: false,
                commandIndex: 0,
                commands: [
                    { name: '/roll', desc: 'Rola dados de RPG (ex: 1d20+5)', syntax: '/roll ' },
                    { name: '/help', desc: 'Mostra ajuda sobre o Nexus', syntax: '/help' },
                    { name: '/session', desc: 'Configurações de sessão', syntax: '/session ' },
                    { name: 'Começar', desc: 'Inicia a narrativa oficial', syntax: 'Começar' }
                ],
                get filteredCommands() {
                    const val = (this.$wire.newMessage || '').toLowerCase();
                    if (val.startsWith('/')) {
                        return this.commands.filter(c => c.name.toLowerCase().startsWith(val));
                    }
                    if (val.length > 0) {
                        return this.commands.filter(c => c.name.toLowerCase().includes(val));
                    }
                    return [];
                },
                init() {
                    this.$watch('filteredCommands', (val) => { 
                        this.commandPaletteOpen = val.length > 0 && (this.$wire.newMessage || '').length > 0 
                    });
                    this.$nextTick(() => { this.scrollToBottom(); });
                },
                selectCommand(cmd) {
                    this.$wire.newMessage = cmd.syntax;
                    this.commandPaletteOpen = false;
                    this.commandIndex = 0;
                    this.$nextTick(() => this.$refs.chatInput.focus());
                },
                scrollToBottom() {
                    const chat = this.$refs.chatContainer;
                    if (chat) {
                        setTimeout(() => { chat.scrollTop = chat.scrollHeight; }, 50);
                    }
                },
                switchTool(panelId, channel = null) {
                    if (channel) {
                        this.currentChannel = channel;
                        this.activePanel = 'chat';
                    } else {
                        this.activePanel = panelId;
                    }
                    this.mobileMenuOpen = false;
                    this.scrollToBottom();
                }
            }))
        })
    </script>

    <style>
        [x-cloak] { display: none !important; }
        @media (min-width: 1024px) {
            .desktop-sidebar { display: flex !important; }
            .mobile-only { display: none !important; }
            .desktop-only { display: flex !important; }
            .main-content-mobile-header { display: none !important; }
        }
        @media (max-width: 1023px) {
            .desktop-sidebar { display: none !important; }
            aside.desktop-sidebar { display: none !important; }
            .mobile-only { display: flex !important; }
            .desktop-only { display: none !important; }
            .main-content-mobile-header { display: flex !important; }
        }
    </style>

    <!-- Character Selection Overlay (Same as before) -->
    @if(!$characterId)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-xl"></div>
            <!-- ... content omitted for brevity ... -->
        </div>
    @endif

    <!-- Magical Background Effects -->
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden bg-[url('/images/texture-noise.png')] opacity-10 mix-blend-overlay"></div>
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-purple-600/20 rounded-full blur-[120px] animate-float"></div>
        <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-amber-600/10 rounded-full blur-[150px] animate-float-delayed"></div>
    </div>

    <!-- Left Sidebar (Desktop Navigation) - PERSISTENT -->
    <aside class="desktop-sidebar flex-col w-64 border-r border-white/10 bg-black/40 backdrop-blur-xl z-20 shadow-2xl relative shrink-0">
        <div class="absolute inset-y-0 right-0 w-px bg-gradient-to-b from-transparent via-amber-500/20 to-transparent"></div>

        <div class="p-5 font-bold border-b border-white/5 flex items-center justify-between">
            <span class="truncate text-amber-500 tracking-wider font-serif">{{ $campaign?->title ?? 'O Reino de Nexus' }}</span>
        </div>

        <div class="p-2 border-b border-white/5">
            <a href="{{ route('campaigns.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-stone-500 hover:text-amber-400 transition-colors">
                <flux:icon.arrow-left-start-on-rectangle class="w-4 h-4" />
                {{ __('Abandonar Sessão') }}
            </a>
        </div>

        <!-- Nex Balance -->
        <div class="px-5 py-3 border-b border-white/5 bg-amber-500/5 flex items-center justify-between group">
            <div class="flex items-center gap-2">
                <x-nex-icon size="xs" />
                <span class="text-xs font-serif tracking-widest text-amber-200/70 uppercase">Saldo Nex</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-sm font-bold text-amber-400 font-mono">{{ number_format($nexBalance) }}</span>
                <span class="text-[10px] text-amber-600 font-bold">NX</span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto p-4 space-y-2">
            @php
                $navItems = [
                    ['id' => 'chat', 'label' => 'Mesa Central', 'icon' => 'chat-bubble-left-ellipsis'],
                    ['id' => 'meta', 'label' => 'Nexus Architect', 'icon' => 'sparkles'],
                    ['id' => 'character', 'label' => 'Personagem', 'icon' => 'user'],
                    ['id' => 'inventory', 'label' => 'Inventário', 'icon' => 'briefcase'],
                    ['id' => 'spells', 'label' => 'Grimório', 'icon' => 'academic-cap'],
                    ['id' => 'questlog', 'label' => 'Diário', 'icon' => 'book-open'],
                    ['id' => 'map', 'label' => 'Pergaminho', 'icon' => 'map'],
                    ['id' => 'dice', 'label' => 'Dados', 'icon' => 'cube']
                ];
            @endphp

            @foreach($navItems as $item)
                @php 
                    $isMetaData = $item['id'] === 'meta';
                    $isChat = $item['id'] === 'chat';
                @endphp
                <button 
                    @click="switchTool('{{ $item['id'] }}', '{{ $isMetaData ? 'meta' : ($isChat ? 'ic' : '') }}')"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 relative overflow-hidden group"
                    x-bind:class="(activePanel === 'chat' && currentChannel === '{{ $isMetaData ? 'meta' : 'ic' }}' && ('{{ $item['id'] }}' === 'chat' || '{{ $item['id'] }}' === 'meta')) || (activePanel === '{{ $item['id'] }}' && !{{ $isMetaData || $isChat ? 'true' : 'false' }}) ? 'text-amber-300 bg-white/5 shadow-[inset_0_1px_0_0_rgba(255,255,255,0.1)]' : 'text-stone-400 hover:text-stone-200 hover:bg-white/5'">
                    <flux:icon name="{{ $item['icon'] }}" variant="micro" class="w-4 h-4 transition-transform group-hover:scale-110" 
                        x-bind:class="(activePanel === 'chat' && currentChannel === '{{ $isMetaData ? 'meta' : 'ic' }}' && ('{{ $item['id'] }}' === 'chat' || '{{ $item['id'] }}' === 'meta')) || (activePanel === '{{ $item['id'] }}' && !{{ $isMetaData || $isChat ? 'true' : 'false' }}) ? 'text-amber-400' : 'text-stone-500'" />
                    {{ $item['label'] }}
                    <div x-show="(activePanel === 'chat' && currentChannel === '{{ $isMetaData ? 'meta' : 'ic' }}' && ('{{ $item['id'] }}' === 'chat' || '{{ $item['id'] }}' === 'meta')) || (activePanel === '{{ $item['id'] }}' && !{{ $isMetaData || $isChat ? 'true' : 'false' }})"
                        class="absolute inset-y-0 left-0 w-1 bg-amber-500 rounded-r-full shadow-[0_0_10px_rgba(245,158,11,0.5)]"></div>
                </button>
            @endforeach
        </nav>
    </aside>

    <!-- Main Content (Chat Central) -->
    <main class="flex-1 flex flex-col h-full relative z-10 min-w-0" x-show="activePanel === 'chat'" x-transition>
        <!-- Mobile Header -->
        <header class="main-content-mobile-header items-center justify-between p-4 border-b border-white/10 bg-black/60 backdrop-blur-md">
            <div class="font-bold truncate font-serif text-amber-500">{{ $campaign?->title ?? 'O Reino de Nexus' }}</div>
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-stone-400 hover:text-white transition-colors">
                <flux:icon.bars-3 class="w-6 h-6" />
            </button>
        </header>

        <!-- Channel Indicator (Desktop only) -->
        <div class="desktop-only px-8 py-2 bg-black/20 border-b border-white/5 gap-4">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full" :class="currentChannel === 'ic' ? 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]' : 'bg-stone-700'"></div>
                <span class="text-[10px] uppercase tracking-[0.2em] font-bold" :class="currentChannel === 'ic' ? 'text-amber-400' : 'text-stone-500'">Mesa de Jogo (IC)</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full" :class="currentChannel === 'meta' ? 'bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.5)]' : 'bg-stone-700'"></div>
                <span class="text-[10px] uppercase tracking-[0.2em] font-bold" :class="currentChannel === 'meta' ? 'text-purple-400' : 'text-stone-500'">Alinhamento Meta (OOC)</span>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-8 scroll-smooth" x-ref="chatContainer">
            @php $activeMessages = $currentChannel === 'ic' ? $chatMessages : $metaMessages; @endphp

            @if(empty($activeMessages))
                <div class="flex flex-col items-center justify-center h-full text-center space-y-8 py-12">
                    @if($isSessionZero)
                        <!-- Session 0 Welcome Card -->
                        <div class="max-w-md bg-stone-900/40 backdrop-blur-xl p-8 rounded-3xl border border-amber-500/30 shadow-[0_0_50px_rgba(245,158,11,0.1)] animate-in fade-in slide-in-from-bottom-8 duration-700 mx-auto">
                            <div class="w-16 h-16 bg-amber-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <flux:icon.sparkles class="w-8 h-8 text-amber-500 animate-pulse" />
                            </div>
                            <h3 class="text-2xl font-serif text-amber-500 font-bold tracking-widest uppercase mb-4">Sessão Zero</h3>
                            <p class="text-stone-300 font-serif leading-relaxed mb-6">
                                Bem-vindo ao portal de Nexus. Este é o momento de alinhar as expectativas com o **Arquiteto**. 
                                Defina o tom da aventura, as regras da casa ou tire dúvidas sobre o mundo.
                            </p>
                            <div class="p-4 bg-stone-950/50 rounded-xl border border-white/5 text-xs text-stone-500 font-mono tracking-wider italic mb-6 text-center">
                                Use o comando <strong>"Começar"</strong> para iniciar a narrativa oficial.
                            </div>
                            <button wire:click="$set('newMessage', 'Começar')" class="w-full py-3 bg-amber-600 hover:bg-amber-500 text-stone-950 font-bold rounded-xl transition-colors uppercase tracking-widest text-xs">
                                Começar Campanha
                            </button>
                        </div>
                    @else
                        <div class="opacity-30 flex flex-col items-center gap-4">
                            <flux:icon.sparkles class="w-12 h-12 text-amber-500" />
                            <p class="text-sm font-serif tracking-widest text-stone-400 uppercase">O destino aguarda o seu primeiro passo...</p>
                        </div>
                    @endif
                </div>
            @endif

            @foreach($activeMessages as $msg)
                @if($msg['role'] === 'user')
                    @php 
                        $activeChar = $this->characterId ? \App\Models\Character::find($this->characterId) : null;
                    @endphp
                    <div class="flex justify-end group gap-4 items-start">
                        <div class="relative bg-gradient-to-br {{ $currentChannel === 'ic' ? 'from-indigo-600/90 to-purple-700/90 shadow-[0_0_20px_rgba(79,70,229,0.2)]' : 'from-stone-700 to-stone-800' }} backdrop-blur-sm text-white max-w-[85%] md:max-w-2xl px-5 py-4 rounded-2xl rounded-tr-sm shadow-xl border border-white/10 text-sm md:text-base leading-relaxed">
                            {{ $msg['content']['text'] ?? '' }}
                        </div>
                        <div class="hidden md:flex flex-shrink-0 w-10 h-10 rounded-xl overflow-hidden border border-white/10 bg-stone-800 shadow-lg">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($activeChar->name ?? auth()->user()->name) }}&background=1c1917&color=fbbf24&size=64" alt="User" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                        </div>
                    </div>
                @elseif($msg['role'] === 'assistant')
                    <div class="flex justify-start group relative">
                        <div class="hidden md:flex flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-tr {{ $currentChannel === 'ic' ? 'from-amber-600 to-yellow-400 shadow-[0_0_15px_rgba(245,158,11,0.3)]' : 'from-purple-600 to-indigo-500 shadow-[0_0_15px_rgba(168,85,247,0.3)]' }} items-center justify-center mr-4 mt-2">
                            @if($currentChannel === 'ic') <flux:icon.sparkles class="w-4 h-4 text-white" /> @else <flux:icon.cpu-chip class="w-4 h-4 text-white" /> @endif
                        </div>
                        <div class="relative bg-black/40 backdrop-blur-xl text-stone-200 max-w-[85%] md:max-w-3xl px-6 py-5 rounded-2xl rounded-tl-sm shadow-2xl border border-white/5 prose prose-stone prose-invert break-words leading-loose font-serif">
                            {{ $msg['content']['text'] ?? '' }}
                        </div>
                    </div>
                @endif
            @endforeach

            @if($isAiThinking)
                <div class="flex justify-start" wire:key="ai-thinking">
                    <div class="hidden md:flex flex-shrink-0 w-8 h-8 rounded-full bg-stone-800 items-center justify-center mr-4 mt-1 opacity-50">
                        <flux:icon.sparkles class="w-4 h-4 text-stone-500" />
                    </div>
                    <div class="bg-black/30 backdrop-blur-md px-5 py-4 rounded-2xl rounded-tl-sm shadow-sm border border-white/5 flex items-center space-x-2 h-14 w-24 justify-center">
                        <div class="w-2 h-2 rounded-full bg-amber-500/50 animate-bounce shadow-[0_0_8px_rgba(245,158,11,0.5)]" style="animation-delay: 0ms"></div>
                        <div class="w-2 h-2 rounded-full bg-amber-500/50 animate-bounce shadow-[0_0_8px_rgba(245,158,11,0.5)]" style="animation-delay: 150ms"></div>
                        <div class="w-2 h-2 rounded-full bg-amber-500/50 animate-bounce shadow-[0_0_8px_rgba(245,158,11,0.5)]" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Input Area + Command Palette -->
        <div class="relative z-20 m-4 md:m-8 max-w-4xl mx-auto w-full px-4 sm:px-0">
            <!-- Command Palette Overlay -->
            <div x-show="commandPaletteOpen" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute bottom-full mb-2 w-full bg-stone-900/95 backdrop-blur-xl border border-white/10 rounded-xl shadow-2xl overflow-hidden z-30"
                 @keydown.window.escape="commandPaletteOpen = false"
                 @click.away="commandPaletteOpen = false">
                <div class="p-2 border-b border-white/5 bg-black/20">
                    <span class="text-[10px] text-stone-500 uppercase font-bold tracking-widest px-2">Comandos do Nexus</span>
                </div>
                <div class="max-h-60 overflow-y-auto">
                    <template x-for="(cmd, index) in filteredCommands" :key="cmd.name">
                        <button type="button" 
                                @click="selectCommand(cmd)"
                                @mouseenter="commandIndex = index"
                                class="w-full flex items-center justify-between px-4 py-3 text-left transition-colors group"
                                :class="commandIndex === index ? 'bg-amber-500/10' : 'hover:bg-white/5'">
                            <div class="flex items-center gap-3">
                                <span class="font-mono text-amber-500 font-bold" x-text="cmd.name"></span>
                                <span class="text-xs text-stone-400" x-text="cmd.desc"></span>
                            </div>
                            <flux:icon.arrow-right-start-on-rectangle class="w-3 h-3 text-stone-600 group-hover:text-amber-500 transition-colors" />
                        </button>
                    </template>
                </div>
            </div>

            <form wire:submit.prevent="sendMessage" class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-amber-500/20 via-purple-600/20 to-indigo-500/20 rounded-2xl blur-md opacity-50 group-hover:opacity-100 transition duration-700 pointer-events-none"></div>
                <div class="relative flex items-end gap-2 bg-stone-900/80 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl p-2 transition-all focus-within:border-amber-500/50">
                    <div class="hidden sm:flex flex-shrink-0 w-10 h-10 rounded-full bg-stone-800 border border-white/5 items-center justify-center mb-1 ml-1 overflow-hidden">
                        <flux:icon.pencil class="w-5 h-5 text-amber-500/70" />
                    </div>
                    <textarea 
                        x-ref="chatInput"
                        wire:model="newMessage"
                        x-on:keydown.down.prevent="if(commandPaletteOpen) { commandIndex = (commandIndex + 1) % filteredCommands.length }"
                        x-on:keydown.up.prevent="if(commandPaletteOpen) { commandIndex = (commandIndex - 1 + filteredCommands.length) % filteredCommands.length }"
                        x-on:keydown.enter.prevent="if(commandPaletteOpen) { selectCommand(filteredCommands[commandIndex]) } else if(!$event.shiftKey) { $wire.sendMessage() }"
                        class="flex-1 field-sizing-content resize-none bg-transparent border-0 focus:ring-0 text-stone-200 placeholder-stone-500/70 text-base py-3 px-2 sm:px-4 max-h-[40vh] min-h-[52px] w-full overflow-y-auto font-serif"
                        rows="1" :placeholder="currentChannel === 'ic' ? 'O que o seu personagem faz?' : 'Planeje sua história...'"></textarea>
                    <button type="submit" class="flex-shrink-0 w-12 h-12 mb-0.5 mr-0.5 rounded-xl bg-gradient-to-br from-amber-600 to-amber-800 text-amber-100 shadow-lg flex items-center justify-center transition-all active:scale-95">
                        <flux:icon.paper-airplane class="w-5 h-5" />
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Right Sidebar (Context Panel) - UNIFIED FOR DESKTOP AND MOBILE -->
    <aside class="flex-col w-80 lg:w-96 border-l border-white/10 bg-stone-950 lg:bg-black/60 lg:backdrop-blur-2xl z-[100] lg:z-20 shadow-2xl shrink-0 transition-transform duration-300"
           :class="activePanel !== 'chat' ? 'fixed inset-0 lg:static lg:flex' : 'hidden lg:flex'">
        
        <!-- Desktop Header -->
        <div class="hidden lg:flex p-5 font-bold border-b border-white/5 bg-black/20 items-center justify-between">
            <span class="text-xs uppercase tracking-[0.2em] text-amber-500 font-serif">Interface de Suporte</span>
            <flux:icon.cpu-chip class="w-4 h-4 text-stone-600" />
        </div>

        <!-- Mobile Header (Visible only when overlay is active on small screens) -->
        <header class="flex lg:hidden items-center justify-between p-4 border-b border-white/10 bg-black/40">
            <button @click="activePanel = 'chat'" class="flex items-center gap-2 text-stone-400 hover:text-white transition-colors">
                <flux:icon.arrow-left class="w-5 h-5" />
                <span class="text-xs uppercase tracking-widest">Voltar ao Chat</span>
            </button>
            <div class="font-bold font-serif text-amber-500 capitalize" x-text="activePanel">Painel</div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 scroll-smooth">
            <!-- Dynamic Panels based on activePanel or default to character -->
            <div x-show="activePanel === 'character' || ['chat', 'meta'].includes(activePanel)" x-cloak>
                <livewire:campaign.panels.character-panel :characterId="$characterId" />
            </div>
            <div x-show="activePanel === 'inventory'" x-cloak>
                <livewire:campaign.panels.inventory-panel :characterId="$characterId" />
            </div>
            <div x-show="activePanel === 'spells'" x-cloak>
                <livewire:campaign.panels.spells-panel :characterId="$characterId" />
            </div>
            <div x-show="activePanel === 'questlog'" x-cloak>
                <livewire:campaign.panels.quest-log-panel :characterId="$characterId" />
            </div>
            <div x-show="activePanel === 'map'" x-cloak>
                <livewire:campaign.panels.map-panel :characterId="$characterId" />
            </div>
            <div x-show="activePanel === 'dice'" x-cloak>
                <livewire:campaign.panels.dice-roller-panel :wire:key="'dice-panel-'.$campaign->id" />
            </div>
        </div>
    </aside>



    <!-- Mobile Navigation Menu Overlay -->
    <div x-show="mobileMenuOpen" class="fixed inset-0 z-[60] bg-black/80 backdrop-blur-sm mobile-only" @click="mobileMenuOpen = false" x-transition.opacity></div>
    <div x-show="mobileMenuOpen"
        class="fixed inset-y-0 right-0 z-[70] w-72 bg-stone-900 border-l border-white/10 shadow-2xl mobile-only flex flex-col"
        x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
        <div class="p-5 flex justify-between items-center border-b border-white/5 bg-black/20">
            <span class="font-bold font-serif text-amber-500 tracking-wider">Acesso Rápido</span>
            <button @click="mobileMenuOpen = false" class="text-stone-400 hover:text-white"><flux:icon.x-mark
                    class="w-6 h-6" /></button>
        </div>
        <nav class="p-4 space-y-2 flex-1 overflow-y-auto">
            @foreach($navItems as $item)
                @php 
                    $isMetaData = $item['id'] === 'meta';
                    $isChat = $item['id'] === 'chat';
                @endphp
                <button 
                    @click="switchTool('{{ $item['id'] }}', '{{ $isMetaData ? 'meta' : ($isChat ? 'ic' : '') }}')"
                    class="w-full flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-colors"
                    x-bind:class="(activePanel === 'chat' && currentChannel === '{{ $isMetaData ? 'meta' : 'ic' }}' && ('{{ $item['id'] }}' === 'chat' || '{{ $item['id'] }}' === 'meta')) || (activePanel === '{{ $item['id'] }}' && !{{ $isMetaData || $isChat ? 'true' : 'false' }}) ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-stone-300 hover:bg-white/5'">
                    <flux:icon name="{{ $item['icon'] }}" class="w-5 h-5 opacity-70"
                        x-bind:class="(activePanel === 'chat' && currentChannel === '{{ $isMetaData ? 'meta' : 'ic' }}' && ('{{ $item['id'] }}' === 'chat' || '{{ $item['id'] }}' === 'meta')) || (activePanel === '{{ $item['id'] }}' && !{{ $isMetaData || $isChat ? 'true' : 'false' }}) ? 'text-amber-400 opacity-100' : ''" />
                    {{ $item['label'] }}
                </button>
            @endforeach
            <hr class="border-white/10 my-4">
            <a href="{{ route('campaigns.index') }}"
                class="flex items-center gap-3 w-full px-3 py-3 rounded-lg text-sm font-medium text-red-500 hover:bg-red-500/10 transition-colors">
                <flux:icon.arrow-left-start-on-rectangle class="w-5 h-5 opacity-70" />
                Abandonar Sessão
            </a>
        </nav>
    </div>
</div>