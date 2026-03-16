<?php

namespace App\AI\Tools;

use App\Models\Campaign;
use App\Models\Character;

class ContextQueryTool extends AbstractTool
{
    public function name(): string
    {
        return 'query_context';
    }

    public function description(): string
    {
        return 'Query specific context information about the campaign or characters. Use this when you need detailed current stats, lore, or attributes to make a decision.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query_type' => [
                    'type' => 'string',
                    'enum' => ['character_stats', 'campaign_lore'],
                    'description' => 'The type of context to query. "character_stats" returns character details (HP, stats, level). "campaign_lore" returns campaign details and history.',
                ],
                'entity_id' => [
                    'type' => 'integer',
                    'description' => 'The ID of the character or campaign to query based on query_type.',
                ],
            ],
            'required' => ['query_type', 'entity_id'],
        ];
    }

    public function handle(array $arguments): mixed
    {
        $queryType = $arguments['query_type'] ?? null;
        $entityId = $arguments['entity_id'] ?? null;

        if (! $queryType || ! $entityId) {
            return [
                'success' => false,
                'message' => 'Missing parameters. Both query_type and entity_id are required.',
            ];
        }

        return match ($queryType) {
            'character_stats' => $this->getCharacterStats($entityId),
            'campaign_lore' => $this->getCampaignLore($entityId),
            default => [
                'success' => false,
                'message' => "Unknown query type: {$queryType}",
            ],
        };
    }

    protected function getCharacterStats(int $characterId): array
    {
        $character = Character::find($characterId);

        if (! $character) {
            return [
                'success' => false,
                'message' => "Character with ID {$characterId} not found.",
            ];
        }

        return [
            'success' => true,
            'data' => [
                'id' => $character->id,
                'name' => $character->name,
                'race' => $character->race,
                'classes' => $character->classes,
                'level' => $character->level,
                'alignment' => $character->alignment,
                'background' => $character->background,
                'hp' => [
                    'current' => $character->current_hp,
                    'max' => $character->max_hp,
                ],
                'stats' => [
                    'strength' => $character->strength,
                    'dexterity' => $character->dexterity,
                    'constitution' => $character->constitution,
                    'intelligence' => $character->intelligence,
                    'wisdom' => $character->wisdom,
                    'charisma' => $character->charisma,
                ],
                'inventory' => $character->inventory ?? [],
            ],
        ];
    }

    protected function getCampaignLore(int $campaignId): array
    {
        $campaign = Campaign::find($campaignId);

        if (! $campaign) {
            return [
                'success' => false,
                'message' => "Campaign with ID {$campaignId} not found.",
            ];
        }

        return [
            'success' => true,
            'data' => [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'description' => $campaign->description,
                'difficulty' => $campaign->difficulty,
                'play_style' => $campaign->play_style,
                'progression_type' => $campaign->progression_type,
            ],
        ];
    }
}
