<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use App\Models\Campaign;
use App\Models\Character;
use App\Models\StoryLog;
use App\AI\Orchestrator;
use App\AI\Drivers\GeminiDriver;
use App\AI\Tools\DiceRollerTool;
use App\AI\Tools\InventoryManagerTool;
use App\AI\Tools\ContextQueryTool;
use Illuminate\Support\Facades\Auth;

new #[Title('AI Sandbox')] class extends Component {
    public ?int $campaign_id = null;
    public ?int $character_id = null;
    public string $message = '';
    public $chat = [];

    public function mount()
    {
        $campaign = Auth::user()->campaigns()->first();
        if ($campaign) {
            $this->campaign_id = $campaign->id;
            $this->loadChat();
        }
    }

    public function updatedCampaignId()
    {
        $this->loadChat();
    }

    public function updatedCharacterId()
    {
        $this->loadChat();
    }

    public function loadChat()
    {
        if (!$this->campaign_id)
            return;

        $this->chat = StoryLog::where('campaign_id', $this->campaign_id)
            ->when($this->character_id, fn($q) => $q->where('character_id', $this->character_id))
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'required|string',
            'campaign_id' => 'required',
        ]);

        $campaign = Campaign::findOrFail($this->campaign_id);
        $character = $this->character_id ? Character::find($this->character_id) : null;

        $orchestrator = new Orchestrator(new GeminiDriver(), $campaign, $character);

        $orchestrator->registerTool(new DiceRollerTool())
            ->registerTool(new InventoryManagerTool())
            ->registerTool(new ContextQueryTool()); // These assume they exist in App\AI\Tools

        $orchestrator->interact($this->message);

        $this->message = '';
        $this->loadChat();
    }

    public function clearLogs()
    {
        if ($this->campaign_id) {
            StoryLog::where('campaign_id', $this->campaign_id)
                ->when($this->character_id, fn($q) => $q->where('character_id', $this->character_id))
                ->delete();
            $this->loadChat();
        }
    }

    #[Computed]
    public function campaigns()
    {
        return Auth::user()->campaigns;
    }

    #[Computed]
    public function characters()
    {
        return Auth::user()->characters;
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-6 max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold text-stone-900 dark:text-white mb-2">{{ __('AI Orchestrator Sandbox') }}</h1>
            <p class="text-stone-600 dark:text-stone-400">
                {{ __('Test the Multi-Agent LLM interaction in an isolated environment.') }}</p>
        </div>
        <flux:button wire:click="clearLogs" wire:confirm="Clear all logs for this context?" variant="danger"
            icon="trash">{{ __('Clear Chat') }}</flux:button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Controls -->
        <div class="space-y-6">
            <div class="bg-glass p-6 rounded-xl border border-stone-200/50 dark:border-stone-800/50">
                <h3 class="text-lg font-bold mb-4">{{ __('Context Selectors') }}</h3>
                <div class="space-y-4">
                    <flux:select wire:model.live="campaign_id" label="{{ __('Target Campaign') }}">
                        <flux:select.option value="">-- {{ __('Select a Campaign') }} --</flux:select.option>
                        @foreach($this->campaigns as $camp)
                            <flux:select.option value="{{ $camp->id }}">{{ $camp->title }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model.live="character_id" label="{{ __('Target Character (Optional)') }}">
                        <flux:select.option value="">-- {{ __('DM / Global Mode') }} --</flux:select.option>
                        @foreach($this->characters as $char)
                            <flux:select.option value="{{ $char->id }}">{{ $char->name }} (Lv.{{ $char->level }}
                                {{ $char->class }})</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <div
                class="bg-amber-500/10 p-6 rounded-xl border border-amber-500/20 text-amber-900 dark:text-amber-200 text-sm">
                <p><strong>Note:</strong> This sandbox utilizes the actual LLM Driver and injected Tools. It consumes
                    credits if using external APIs.</p>
            </div>
        </div>

        <!-- Chat Area -->
        <div
            class="col-span-2 flex flex-col bg-glass rounded-xl border border-stone-200/50 dark:border-stone-800/50 h-[600px] overflow-hidden">
            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                @forelse($chat as $log)
                    <div class="w-full flex {{ $log['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div
                            class="max-w-[80%] rounded-xl p-4 {{ $log['role'] === 'user' ? 'bg-amber-600 text-white' : 'bg-stone-100 dark:bg-stone-900 border border-stone-300 dark:border-stone-700' }}">
                            @if($log['role'] !== 'user' && !empty($log['metadata']['tool_calls']))
                                <div class="mb-2 text-xs opacity-75 border-b border-Current pb-2">
                                    <strong>Tools Called:</strong>
                                    <ul class="list-disc list-inside">
                                        @foreach($log['metadata']['tool_calls'] as $tool)
                                            <li>{{ $tool['name'] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="prose prose-sm dark:prose-invert">
                                {!! Str::markdown($log['content']) !!}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex items-center justify-center text-stone-500 italic">
                        {{ __('No history found for this context. Send a message to wake up the DM.') }}
                    </div>
                @endforelse
            </div>

            <div class="p-4 border-t border-stone-200 dark:border-stone-800 bg-stone-50 dark:bg-stone-950">
                <form wire:submit="sendMessage" class="flex gap-2">
                    <flux:input wire:model="message" class="flex-1"
                        placeholder="{{ __('What do you want to do/say?') }}" required />
                    <flux:button type="submit" variant="primary" icon="paper-airplane" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="sendMessage">{{ __('Send') }}</span>
                        <span wire:loading wire:target="sendMessage">{{ __('Thinking...') }}</span>
                    </flux:button>
                </form>
            </div>
        </div>
    </div>
</div>