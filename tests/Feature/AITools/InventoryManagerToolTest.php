<?php

namespace Tests\Feature\AITools;

use App\AI\Tools\InventoryManagerTool;
use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->character = Character::factory()->create([
        'user_id' => $this->user->id,
        'inventory' => [
            'sword' => ['name' => 'Sword', 'quantity' => 1],
            'potion' => ['name' => 'Potion', 'quantity' => 5],
        ],
    ]);
});

it('has correct name and description', function () {
    $tool = new InventoryManagerTool;

    expect($tool->name())->toBe('manage_inventory')
        ->and($tool->description())->toBeString()
        ->and($tool->parameters())->toBeArray();
});

it('requires character_id, action, item_name, and quantity', function () {
    $tool = new InventoryManagerTool;

    $result = $tool->handle([]);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('Missing or invalid parameters');
});

it('adds a new item to inventory', function () {
    $tool = new InventoryManagerTool;

    $result = $tool->handle([
        'character_id' => $this->character->id,
        'action' => 'add',
        'item_name' => 'Shield',
        'quantity' => 1,
    ]);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeTrue()
        ->and($result['message'])->toContain('Shield');

    $this->character->refresh();
    expect($this->character->inventory)->toHaveKey('shield')
        ->and($this->character->inventory['shield']['quantity'])->toBe(1);
});

it('adds quantity to an existing item', function () {
    $tool = new InventoryManagerTool;

    $result = $tool->handle([
        'character_id' => $this->character->id,
        'action' => 'add',
        'item_name' => 'Potion',
        'quantity' => 3,
    ]);

    expect($result['success'])->toBeTrue();

    $this->character->refresh();
    expect($this->character->inventory['potion']['quantity'])->toBe(8);
});

it('removes quantity from an existing item', function () {
    $tool = new InventoryManagerTool;

    $result = $tool->handle([
        'character_id' => $this->character->id,
        'action' => 'remove',
        'item_name' => 'Potion',
        'quantity' => 2,
    ]);

    expect($result['success'])->toBeTrue();

    $this->character->refresh();
    expect($this->character->inventory['potion']['quantity'])->toBe(3);
});

it('removes the item completely if removed quantity is greater than or equal to current', function () {
    $tool = new InventoryManagerTool;

    $result = $tool->handle([
        'character_id' => $this->character->id,
        'action' => 'remove',
        'item_name' => 'Potion',
        'quantity' => 10,
    ]);

    expect($result['success'])->toBeTrue();

    $this->character->refresh();
    expect($this->character->inventory)->not->toHaveKey('potion');
});

it('returns an error if trying to remove an item that does not exist', function () {
    $tool = new InventoryManagerTool;

    $result = $tool->handle([
        'character_id' => $this->character->id,
        'action' => 'remove',
        'item_name' => 'Dragon Glass',
        'quantity' => 1,
    ]);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('not found in inventory');
});

it('returns an error if character does not exist', function () {
    $tool = new InventoryManagerTool;

    $result = $tool->handle([
        'character_id' => 99999,
        'action' => 'add',
        'item_name' => 'Sword',
        'quantity' => 1,
    ]);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('not found');
});

it('returns an error for an invalid action', function () {
    $tool = new InventoryManagerTool;

    $result = $tool->handle([
        'character_id' => $this->character->id,
        'action' => 'destroy',
        'item_name' => 'Sword',
        'quantity' => 1,
    ]);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('Invalid action');
});
