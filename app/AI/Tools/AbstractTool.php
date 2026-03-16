<?php

namespace App\AI\Tools;

use App\AI\Contracts\Tool;

abstract class AbstractTool implements Tool
{
    /**
     * Convert the tool definition into an array suitable for APIs like OpenAI.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name(),
                'description' => $this->description(),
                'parameters' => $this->parameters(),
            ],
        ];
    }
}
