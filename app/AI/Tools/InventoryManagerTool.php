<?php

namespace App\AI\Tools;

use App\AI\Quests\QuestManager;
use App\Models\Character;

class InventoryManagerTool extends AbstractTool
{
    public function name(): string
    {
        return 'manage_inventory';
    }

    public function description(): string
    {
        return 'Add or remove items from a character\'s inventory.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'character_id' => [
                    'type' => 'integer',
                    'description' => 'The ID of the character whose inventory is being managed.',
                ],
                'action' => [
                    'type' => 'string',
                    'enum' => ['add', 'remove'],
                    'description' => 'The action to perform: "add" or "remove".',
                ],
                'item_name' => [
                    'type' => 'string',
                    'description' => 'The name of the item to add or remove.',
                ],
                'quantity' => [
                    'type' => 'integer',
                    'description' => 'The quantity of the item to add or remove.',
                ],
            ],
            'required' => ['character_id', 'action', 'item_name', 'quantity'],
        ];
    }

    public function handle(array $arguments): mixed
    {
        $characterId = $arguments['character_id'] ?? null;
        $action = $arguments['action'] ?? null;
        $itemName = $arguments['item_name'] ?? null;
        $quantity = $arguments['quantity'] ?? 1;

        if (! $characterId || ! $action || ! $itemName || $quantity <= 0) {
            return [
                'success' => false,
                'message' => 'Missing or invalid parameters. character_id, action, item_name, and a positive quantity are required.',
            ];
        }

        $character = Character::find($characterId);

        if (! $character) {
            return [
                'success' => false,
                'message' => "Character with ID {$characterId} not found.",
            ];
        }

        $inventory = $character->inventory ?? [];

        // Normalize item name for array keys
        $itemKey = strtolower(trim($itemName));

        if ($action === 'add') {
            if (isset($inventory[$itemKey])) {
                $inventory[$itemKey]['quantity'] += $quantity;
            } else {
                $inventory[$itemKey] = [
                    'name' => trim($itemName),
                    'quantity' => $quantity,
                ];
            }

            // Quest: First Item Added
            $user = auth()->user();
            if ($user) {
                QuestManager::complete($user, 'first_item_added');
            }
        } elseif ($action === 'remove') {
            if (! isset($inventory[$itemKey])) {
                return [
                    'success' => false,
                    'message' => "Item '{$itemName}' not found in inventory.",
                ];
            }

            if ($inventory[$itemKey]['quantity'] <= $quantity) {
                unset($inventory[$itemKey]); // Remove completely if trying to remove more than or equal to what exists
            } else {
                $inventory[$itemKey]['quantity'] -= $quantity;
            }
        } else {
            return [
                'success' => false,
                'message' => "Invalid action '{$action}'. Must be 'add' or 'remove'.",
            ];
        }

        // Re-index array values to keep it as a list if desired, but associative is easier to manage.
        // Let's keep it associative for fast lookups.

        $character->inventory = $inventory;
        $character->save();

        return [
            'success' => true,
            'message' => "Item '{$itemName}' successfully {$action}ed.",
            'inventory' => array_values($inventory),
        ];
    }
}
