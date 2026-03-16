<?php

namespace Tests\Feature\AITools;

use App\AI\Tools\ContextQueryTool;
use App\Models\Campaign;
use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->campaign = Campaign::factory()->create([
        'user_id' => $this->user->id,
        'title' => 'Lost Mines',
        'description' => 'A classic adventure',
        'difficulty' => 'Normal',
        'play_style' => 'Balanced',
        'progression_type' => 'Milestone',
    ]);

    $this->character = Character::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Elara',
        'race' => 'Elf',
        'level' => 3,
        'current_hp' => 25,
        'max_hp' => 30,
        'strength' => 10,
        'dexterity' => 16,
    ]);
});

it('has correct name and description', function () {
    $tool = new ContextQueryTool;

    expect($tool->name())->toBe('query_context')
        ->and($tool->description())->toBeString()
        ->and($tool->parameters())->toBeArray();
});

it('requires query_type and entity_id', function () {
    $tool = new ContextQueryTool;

    $result = $tool->handle([]);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('Missing parameters');
});

it('returns an error for unknown query type', function () {
    $tool = new ContextQueryTool;

    $result = $tool->handle([
        'query_type' => 'unknown_type',
        'entity_id' => 1,
    ]);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('Unknown query type');
});

it('fetches character stats correctly', function () {
    $tool = new ContextQueryTool;

    $result = $tool->handle([
        'query_type' => 'character_stats',
        'entity_id' => $this->character->id,
    ]);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeTrue()
        ->and($result['data']['name'])->toBe('Elara')
        ->and($result['data']['race'])->toBe('Elf')
        ->and($result['data']['level'])->toBe(3)
        ->and($result['data']['hp']['current'])->toBe(25)
        ->and($result['data']['hp']['max'])->toBe(30)
        ->and($result['data']['stats']['strength'])->toBe(10)
        ->and($result['data']['stats']['dexterity'])->toBe(16);
});

it('returns an error if character not found for stats query', function () {
    $tool = new ContextQueryTool;

    $result = $tool->handle([
        'query_type' => 'character_stats',
        'entity_id' => 99999,
    ]);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('not found');
});

it('fetches campaign lore correctly', function () {
    $tool = new ContextQueryTool;

    $result = $tool->handle([
        'query_type' => 'campaign_lore',
        'entity_id' => $this->campaign->id,
    ]);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeTrue()
        ->and($result['data']['title'])->toBe('Lost Mines')
        ->and($result['data']['description'])->toBe('A classic adventure')
        ->and($result['data']['difficulty'])->toBe('Normal');
});

it('returns an error if campaign not found for lore query', function () {
    $tool = new ContextQueryTool;

    $result = $tool->handle([
        'query_type' => 'campaign_lore',
        'entity_id' => 99999,
    ]);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('not found');
});
