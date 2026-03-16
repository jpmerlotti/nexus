<?php

namespace App\AI\Memory;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\StoryLog;
use Illuminate\Support\Facades\Cache;

class ContextBuilder
{
    protected Campaign $campaign;

    protected ?Character $character;

    public function __construct(Campaign $campaign, ?Character $character = null)
    {
        $this->campaign = $campaign;
        $this->character = $character;
    }

    /**
     * Build the full array of messages to send to the LLM.
     * This includes the System Prompt, Short-Term Memory, and the immediate user prompt.
     */
    public function build(string $userMessage, string $channel = 'ic'): array
    {
        $messages = [];

        // 1. Core System Prompt
        $messages[] = [
            'role' => 'system',
            'content' => $this->generateSystemPrompt($channel),
        ];

        // 2. Short Term Memory (Recent Chat History for this channel)
        $history = $this->getRecentHistory($channel);
        foreach ($history as $log) {
            $msg = [
                'role' => $log->role,
                'content' => $log->content,
            ];

            // If it had metadata involving tools, reconstruct it
            if ($log->metadata) {
                if (isset($log->metadata['tool_calls'])) {
                    $msg['tool_calls'] = $log->metadata['tool_calls'];
                }
                if ($log->role === 'tool' && isset($log->metadata['name'])) {
                    $msg['name'] = $log->metadata['name'];
                }
            }

            $messages[] = $msg;
        }

        // 3. New User Message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        return $messages;
    }

    protected function generateSystemPrompt(string $channel = 'ic'): string
    {
        $cacheKey = "campaign_{$this->campaign->id}_{$channel}_system_prompt";

        $basePrompt = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($channel) {
            if ($channel === 'meta') {
                $prompt = "You are the 'Nexus Architect' - a creative partner and Meta-DM for a Fantasy RPG.\n";
                $prompt .= "Your goal is to help the player plan cool narrative events, align character development, and brainstorm story beats OUT-OF-CHARACTER.\n";
            } else {
                $prompt = "You are the Dungeon Master for a Fantasy RPG Campaign.\n";
                
                // Detection logic for Session 0 in system prompt
                $hasHistory = StoryLog::where('campaign_id', $this->campaign->id)->exists();
                if (!$hasHistory) {
                    $prompt .= "The campaign is currently in 'Session 0'. Your goal is to welcome the player, set the initial mood, and wait for them to say 'Começar' (Start) to begin the actual game. Focus on establishing the premise and answering questions about the world.\n";
                }
            }

            $prompt .= "Campaign Title: {$this->campaign->title}\n";
            $prompt .= "Premise: {$this->campaign->description}\n\n";

            return $prompt;
        });

        $dynamicPrompt = $basePrompt;

        if ($this->character) {
            $dynamicPrompt .= "Focus Character: '{$this->character->name}' ({$this->character->race->value} {$this->character->class->value}, Level {$this->character->level}).\n";
        }

        if ($channel === 'meta') {
            $dynamicPrompt .= "\nSTRATEGIC GOAL: Help the player build a legendary story. Discuss mechanics, multiclassing pacts, and upcoming plot twists. Be collaborative, encouraging, and creative.\n";
            $dynamicPrompt .= "When the player agrees on a plan, summarize it clearly so it can be 'implemented' in the main story.\n";
        } else {
            // Main DM Prompt
            $dynamicPrompt .= "Narrate the adventure. Be immersive, descriptive, and reactive.\n";

            // Crucial: Mention Meta-plans if they exist
            $metaPlan = $this->getMetaPlanSummary();
            if ($metaPlan) {
                $dynamicPrompt .= "\nRECENT META-PLANNING (OOC): Use this to guide the narrative:\n{$metaPlan}\n";
            }

            $dynamicPrompt .= "\nRULES:\n";
            $dynamicPrompt .= "- Do not assume success. Use tools for rolls and inventory.\n";
            $dynamicPrompt .= "- Reply in Markdown.\n";
        }

        return $dynamicPrompt;
    }

    /**
     * Get a summary of recent Meta-Chat discussions to inform the DM.
     */
    protected function getMetaPlanSummary(): ?string
    {
        $recentMeta = StoryLog::where('campaign_id', $this->campaign->id)
            ->where('channel', 'meta')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        if ($recentMeta->isEmpty()) {
            return null;
        }

        return $recentMeta->reverse()->map(fn ($log) => "- {$log->role}: {$log->content}")->implode("\n");
    }

    /**
     * Retrieve the last 20 messages for the specific channel.
     */
    protected function getRecentHistory(string $channel = 'ic')
    {
        return StoryLog::where('campaign_id', $this->campaign->id)
            ->where('channel', $channel)
            ->when($this->character, fn ($q) => $q->where('character_id', $this->character->id))
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->reverse()
            ->values();
    }
}
