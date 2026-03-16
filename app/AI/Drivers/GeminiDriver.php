<?php

namespace App\AI\Drivers;

use App\AI\Contracts\Tool;
use App\AI\LLMDriverInterface;
use Exception;
use Illuminate\Support\Facades\Http;

class GeminiDriver implements LLMDriverInterface
{
    protected string $apiKey;

    protected string $model;

    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct(?string $apiKey = null)
    {
        $this->apiKey = $apiKey ?? config('services.gemini.api_key') ?? env('GEMINI_API_KEY', '');
        $this->model = config('services.gemini.model') ?? 'gemini-1.5-pro';
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function generate(array $messages, array $tools = []): array
    {
        $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

        $payload = $this->buildPayload($messages, $tools);

        $response = Http::timeout(60)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        if ($response->failed()) {
            throw new Exception('Gemini API Error: '.$response->body());
        }

        return $this->parseResponse($response->json());
    }

    /**
     * Translate our standard messages into Gemini's payload signature.
     */
    protected function buildPayload(array $messages, array $tools): array
    {
        $contents = [];
        $systemInstruction = null;

        foreach ($messages as $msg) {
            $role = strtolower($msg['role']);
            $content = $msg['content'] ?? '';

            if ($role === 'system') {
                $systemInstruction = [
                    'parts' => [['text' => $content]],
                ];

                continue;
            }

            // Gemini specific roles: "user" or "model"
            $geminiRole = $role === 'assistant' ? 'model' : 'user';

            // Handle tool calls in history
            if (isset($msg['tool_calls'])) {
                $parts = [];
                foreach ($msg['tool_calls'] as $call) {
                    $parts[] = [
                        'functionCall' => [
                            'name' => $call['name'],
                            'args' => is_string($call['arguments']) ? json_decode($call['arguments'], true) : $call['arguments'],
                        ],
                    ];
                }
                $contents[] = ['role' => $geminiRole, 'parts' => $parts];

                continue;
            }

            // Handle tool responses in history
            if ($role === 'tool' || isset($msg['tool_call_id'])) {
                $contents[] = [
                    'role' => 'user', // For Gemini, tool responses come from the user/client role mostly or functionResponse format
                    'parts' => [
                        [
                            'functionResponse' => [
                                'name' => $msg['name'],
                                'response' => ['result' => is_string($content) ? json_decode($content, true) ?? $content : $content],
                            ],
                        ],
                    ],
                ];

                continue;
            }

            $contents[] = [
                'role' => $geminiRole,
                'parts' => [['text' => is_array($content) ? json_encode($content) : $content]],
            ];
        }

        $payload = ['contents' => $contents];

        if ($systemInstruction) {
            $payload['system_instruction'] = $systemInstruction;
        }

        if (! empty($tools)) {
            $payload['tools'] = [['function_declarations' => $this->formatTools($tools)]];
        }

        return $payload;
    }

    /**
     * Map internal Tool definitions to Gemini function_declarations.
     */
    protected function formatTools(array $tools): array
    {
        return array_map(function (Tool $tool) {
            $params = $tool->parameters();

            return [
                'name' => $tool->name(),
                'description' => $tool->description(),
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => $params['properties'] ?? [],
                    'required' => $params['required'] ?? [],
                ],
            ];
        }, $tools);
    }

    /**
     * Parse the Gemini response into our standard array format.
     */
    protected function parseResponse(array $responseBody): array
    {
        $candidate = $responseBody['candidates'][0] ?? null;
        $usage = $responseBody['usageMetadata'] ?? [];

        if (! $candidate) {
            return [
                'text' => '',
                'tool_calls' => [],
                'usage' => $usage,
            ];
        }

        $parts = $candidate['content']['parts'] ?? [];

        $text = '';
        $toolCalls = [];

        foreach ($parts as $part) {
            if (isset($part['text'])) {
                $text .= $part['text'];
            }

            if (isset($part['functionCall'])) {
                $name = $part['functionCall']['name'];
                $args = $part['functionCall']['args'] ?? [];

                $toolCalls[] = [
                    'name' => $name,
                    'arguments' => $args,
                ];
            }
        }

        return [
            'text' => trim($text),
            'tool_calls' => $toolCalls,
            'usage' => [
                'prompt_tokens' => $usage['promptTokenCount'] ?? 0,
                'candidates_tokens' => $usage['candidatesTokenCount'] ?? 0,
                'total_tokens' => $usage['totalTokenCount'] ?? 0,
            ],
        ];
    }
}
