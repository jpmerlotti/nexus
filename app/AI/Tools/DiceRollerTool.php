<?php

namespace App\AI\Tools;

use Exception;

class DiceRollerTool extends AbstractTool
{
    public function name(): string
    {
        return 'roll_dice';
    }

    public function description(): string
    {
        return 'Roll one or more dice using standard RPG notation (e.g., "1d20+5", "2d6"). Useful for skill checks, attacks, and damage rolls.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'expression' => [
                    'type' => 'string',
                    'description' => 'The dice expression to roll (e.g., "1d20", "2d6+3", "1d100-10").',
                ],
            ],
            'required' => ['expression'],
        ];
    }

    public function handle(array $arguments): mixed
    {
        $expression = $arguments['expression'] ?? '';

        if (empty($expression)) {
            return [
                'success' => false,
                'message' => 'No dice expression provided.',
            ];
        }

        try {
            // Remove spaces and normalize
            $expression = str_replace(' ', '', strtolower($expression));

            // Match standard format: [count]d[sides][+|-modifier]
            if (! preg_match('/^(\d+)?d(\d+)([\+\-]\d+)?$/', $expression, $matches)) {
                throw new Exception('Invalid dice expression format. Examples: 1d20, 2d6+3');
            }

            $count = ! empty($matches[1]) ? (int) $matches[1] : 1;
            $sides = (int) $matches[2];
            $modifier = ! empty($matches[3]) ? (int) $matches[3] : 0;

            if ($count <= 0 || $sides <= 0 || $count > 100) {
                throw new Exception('Unreasonable dice count or sides.');
            }

            $rolls = [];
            $total = 0;

            for ($i = 0; $i < $count; $i++) {
                $val = random_int(1, $sides);
                $rolls[] = $val;
                $total += $val;
            }

            $total += $modifier;

            // Quest: First Dice Roll
            $user = auth()->user();
            if ($user) {
                \App\AI\Quests\QuestManager::complete($user, 'first_dice_roll');
            }

            return [
                'success' => true,
                'expression' => $expression,
                'result' => $total,
                'rolls' => $rolls,
                'modifier' => $modifier,
                'message' => "Rolled {$expression}: Total {$total}",
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error rolling dice: '.$e->getMessage(),
            ];
        }
    }
}
