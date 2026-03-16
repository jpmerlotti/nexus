<?php

namespace App\AI;

interface LLMDriverInterface
{
    /**
     * Send a sequence of messages to the LLM and receive the response.
     *
     * @param  array  $messages  The chat history (e.g., [['role' => 'user', 'content' => '...']])
     * @param  array  $tools  An array of Tools injected for this interaction
     * @return array The structured response containing the narrative and any executed tool calls
     */
    public function generate(array $messages, array $tools = []): array;
}
