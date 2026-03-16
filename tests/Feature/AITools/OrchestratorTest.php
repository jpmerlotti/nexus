<?php

namespace Tests\Feature\AITools;

use App\AI\Contracts\Tool;
use App\AI\LLMDriverInterface;
use App\AI\Orchestrator;
use App\Models\Campaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

uses(RefreshDatabase::class);

it('orchestrates a simple message without tools', function () {
    $campaign = Campaign::factory()->create(['title' => 'Test Campaign']);

    $driverMock = Mockery::mock(LLMDriverInterface::class);
    $driverMock->shouldReceive('generate')
        ->once()
        ->andReturn([
            'text' => 'Hello, I am your DM.',
            'tool_calls' => [],
        ]);

    $orchestrator = new Orchestrator($driverMock, $campaign);

    $response = $orchestrator->interact('Hello!');

    expect($response)->toBe('Hello, I am your DM.');

    /** @var \Tests\TestCase $this */
    $this->assertDatabaseHas('story_logs', [
        'campaign_id' => $campaign->id,
        'role' => 'user',
        'content' => 'Hello!',
    ]);

    $this->assertDatabaseHas('story_logs', [
        'campaign_id' => $campaign->id,
        'role' => 'assistant',
        'content' => 'Hello, I am your DM.',
    ]);
});

it('orchestrates a message that triggers a tool call', function () {
    $campaign = Campaign::factory()->create();

    $toolMock = Mockery::mock(Tool::class);
    $toolMock->shouldReceive('name')->andReturn('dummy_tool');
    $toolMock->shouldReceive('handle')->with(['action' => 'test'])->once()->andReturn('Tool executed');

    $driverMock = Mockery::mock(LLMDriverInterface::class);

    // First iteration returns a tool call
    $driverMock->shouldReceive('generate')
        ->once()
        ->andReturn([
            'text' => '',
            'tool_calls' => [
                ['name' => 'dummy_tool', 'arguments' => ['action' => 'test']],
            ],
        ]);

    // Second iteration returns final text after getting tool results
    $driverMock->shouldReceive('generate')
        ->once()
        ->andReturn([
            'text' => 'The tool answered: Tool executed',
            'tool_calls' => [],
        ]);

    $orchestrator = new Orchestrator($driverMock, $campaign);
    $orchestrator->registerTool($toolMock);

    $response = $orchestrator->interact('Use the tool');

    expect($response)->toBe('The tool answered: Tool executed');

    /** @var \Tests\TestCase $this */
    $this->assertDatabaseHas('story_logs', [
        'campaign_id' => $campaign->id,
        'role' => 'assistant',
        'content' => 'The tool answered: Tool executed',
    ]);
});
