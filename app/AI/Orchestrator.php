<?php

namespace App\AI;

use App\AI\Contracts\Tool;
use App\AI\Memory\ContextBuilder;
use App\Models\Campaign;
use App\Models\Character;
use App\Models\StoryLog;
use App\Models\UsageLog;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Orchestrator
{
    protected LLMDriverInterface $driver;

    protected array $tools = [];

    protected Campaign $campaign;

    protected ?Character $character;

    public function __construct(LLMDriverInterface $driver, Campaign $campaign, ?Character $character = null)
    {
        $this->driver = $driver;
        $this->campaign = $campaign;
        $this->character = $character;

        $this->configureDriver();
    }

    protected function configureDriver(): void
    {
        $user = $this->campaign->user;

        if ($user->ai_driver_preference === 'byok' && ! empty($user->custom_api_key)) {
            if (method_exists($this->driver, 'setApiKey')) {
                $this->driver->setApiKey($user->custom_api_key);
            }
        }
    }

    /**
     * Register a tool for the orchestrator to use.
     */
    public function registerTool(Tool $tool): self
    {
        $this->tools[$tool->name()] = $tool;

        return $this;
    }

    /**
     * Process a user message and return the assistant's response.
     */
    public function interact(string $userMessage, string $channel = 'ic'): string
    {
        $user = $this->campaign->user;

        // 0. Check Credits (Meta-chat is currently free to encourage planning, IC costs 1 Nex)
        $isMeta = $channel === 'meta';
        if (! $isMeta && ! $user->hasEnoughNex(1)) {
            throw new Exception('You have insufficient Nex to perform this action. Complete quests or top up your balance!');
        }

        // Log the initial user message
        $this->logInteraction('user', $userMessage, [], $channel);

        // 1. Build the context history
        $contextBuilder = new ContextBuilder($this->campaign, $this->character);
        $messages = $contextBuilder->build($userMessage, $channel);

        $maxIterations = 5;
        $iteration = 0;
        $totalUsage = [
            'prompt_tokens' => 0,
            'candidates_tokens' => 0,
            'total_tokens' => 0,
        ];

        while ($iteration < $maxIterations) {
            $iteration++;

            // 2. Call the LLM (In Meta mode, we might disable tools if not needed, but keeping them for now)
            $response = $this->driver->generate($messages, array_values($this->tools));

            $text = $response['text'] ?? '';
            $toolCalls = $response['tool_calls'] ?? [];
            $usage = $response['usage'] ?? [];

            // Track usage
            $totalUsage['prompt_tokens'] += $usage['prompt_tokens'] ?? 0;
            $totalUsage['candidates_tokens'] += $usage['candidates_tokens'] ?? 0;
            $totalUsage['total_tokens'] += $usage['total_tokens'] ?? 0;

            // Log this step to StoryLog
            $this->logInteraction('assistant', $text, ['tool_calls' => $toolCalls], $channel);

            // If there's text, we want to append it to messages for history
            $messages[] = [
                'role' => 'assistant',
                'content' => $text,
                'tool_calls' => $toolCalls,
            ];

            // 3. Handle Tool Calls
            if (! empty($toolCalls)) {
                foreach ($toolCalls as $call) {
                    $toolName = $call['name'];
                    $args = $call['arguments'];

                    if (is_string($args)) {
                        $args = json_decode($args, true) ?? [];
                    }

                    if (isset($this->tools[$toolName])) {
                        try {
                            $result = $this->tools[$toolName]->handle($args);
                        } catch (Exception $e) {
                            $result = 'Error executing tool: '.$e->getMessage();
                            Log::error('Tool execution failed', ['tool' => $toolName, 'error' => $e->getMessage()]);
                        }
                    } else {
                        $result = "Tool not found: {$toolName}";
                    }

                    $toolResultContent = is_string($result) ? $result : json_encode($result);

                    // Log tool result to StoryLog
                    $this->logInteraction('tool', $toolResultContent, ['name' => $toolName], $channel);

                    // Append the tool result back to the messages to send to the LLM
                    $messages[] = [
                        'role' => 'tool',
                        'name' => $toolName,
                        'content' => $toolResultContent,
                    ];
                }

                // Loop back to let the LLM see the tool results and form a final answer
                continue;
            }

            // 4. No more tool calls, we have the final narrative text

            DB::transaction(function () use ($user, $iteration, $totalUsage, $isMeta) {
                // SaaS: Deduct Credits and log Usage
                $isByok = $user->ai_driver_preference === 'byok' && ! empty($user->custom_api_key);
                $nexToDeduct = ($isByok || $isMeta) ? 0 : 1;

                if (! $isByok && ! $isMeta) {
                    $user->decrement('nex_balance', $nexToDeduct);
                }

                UsageLog::create([
                    'user_id' => $user->id,
                    'campaign_id' => $this->campaign->id,
                    'action_type' => $isMeta ? 'planning' : 'interact',
                    'tokens_input' => $totalUsage['prompt_tokens'],
                    'tokens_output' => $totalUsage['candidates_tokens'],
                    'nex_spent' => $nexToDeduct,
                    'driver_used' => $isByok ? 'byok' : 'platform',
                    'metadata' => ['iterations' => $iteration],
                ]);
            });

            return $text;
        }

        throw new Exception('Orchestrator exceeded maximum tool call iterations.');
    }

    protected function logInteraction(string $role, string $content, array $metadata = [], string $channel = 'ic'): void
    {
        StoryLog::create([
            'campaign_id' => $this->campaign->id,
            'character_id' => $this->character?->id,
            'role' => $role,
            'channel' => $channel,
            'content' => $content,
            'metadata' => empty($metadata) ? null : $metadata,
        ]);
    }
}
