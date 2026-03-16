<?php

namespace Tests\Unit\AITools;

use App\AI\Tools\DiceRollerTool;

it('has correct name and description', function () {
    $tool = new DiceRollerTool;

    expect($tool->name())->toBe('roll_dice')
        ->and($tool->description())->toBeString()
        ->and($tool->parameters())->toBeArray();
});

it('rolls a simple d20 correctly', function () {
    $tool = new DiceRollerTool;

    $result = $tool->handle(['expression' => '1d20']);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeTrue()
        ->and($result['expression'])->toBe('1d20')
        ->and($result['result'])->toBeBetween(1, 20)
        ->and($result['rolls'])->toHaveCount(1)
        ->and($result['rolls'][0])->toBeBetween(1, 20)
        ->and($result['modifier'])->toBe(0);
});

it('rolls multiple dice with modifiers correctly', function () {
    $tool = new DiceRollerTool;

    $result = $tool->handle(['expression' => '2d6+5']);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeTrue()
        ->and($result['expression'])->toBe('2d6+5')
        ->and($result['rolls'])->toHaveCount(2)
        ->and($result['modifier'])->toBe(5);

    $sumOfRolls = array_sum($result['rolls']);
    expect($result['result'])->toBe($sumOfRolls + 5);
});

it('handles negative modifiers', function () {
    $tool = new DiceRollerTool;

    $result = $tool->handle(['expression' => '1d10-2']);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeTrue()
        ->and($result['expression'])->toBe('1d10-2')
        ->and($result['modifier'])->toBe(-2)
        ->and($result['result'])->toBe($result['rolls'][0] - 2);
});

it('ignores spaces and handles uppercase', function () {
    $tool = new DiceRollerTool;

    $result = $tool->handle(['expression' => ' 3 D 8 + 1 ']);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeTrue()
        ->and($result['expression'])->toBe('3d8+1');
});

it('returns error on invalid format', function () {
    $tool = new DiceRollerTool;

    $result = $tool->handle(['expression' => 'invalid']);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('Invalid dice expression format');
});

it('returns error on missing expression', function () {
    $tool = new DiceRollerTool;

    $result = $tool->handle([]);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('No dice expression provided');
});

it('returns error on unreasonable dice count', function () {
    $tool = new DiceRollerTool;

    $result = $tool->handle(['expression' => '1000d6']);

    expect($result)
        ->toBeArray()
        ->and($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('Error rolling dice');
});
