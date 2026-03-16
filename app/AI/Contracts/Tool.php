<?php

namespace App\AI\Contracts;

interface Tool
{
    /**
     * The name of the tool.
     */
    public function name(): string;

    /**
     * A description of what the tool does.
     */
    public function description(): string;

    /**
     * Definition of the tool's parameters (JSON Schema format).
     *
     * @return array<string, mixed>
     */
    public function parameters(): array;

    /**
     * Execute the tool with the given arguments.
     *
     * @param  array<string, mixed>  $arguments
     */
    public function handle(array $arguments): mixed;
}
