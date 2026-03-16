<?php

namespace App\Enums;

enum CharacterClass: string
{
    case Barbarian = 'barbarian';
    case Bard = 'bard';
    case Cleric = 'cleric';
    case Druid = 'druid';
    case Fighter = 'fighter';
    case Monk = 'monk';
    case Paladin = 'paladin';
    case Ranger = 'ranger';
    case Rogue = 'rogue';
    case Sorcerer = 'sorcerer';
    case Warlock = 'warlock';
    case Wizard = 'wizard';
    case Artificer = 'artificer';

    public function getLabel(): string
    {
        return match ($this) {
            self::Barbarian => 'Barbarian',
            self::Bard => 'Bard',
            self::Cleric => 'Cleric',
            self::Druid => 'Druid',
            self::Fighter => 'Fighter',
            self::Monk => 'Monk',
            self::Paladin => 'Paladin',
            self::Ranger => 'Ranger',
            self::Rogue => 'Rogue',
            self::Sorcerer => 'Sorcerer',
            self::Warlock => 'Warlock',
            self::Wizard => 'Wizard',
            self::Artificer => 'Artificer',
        };
    }
}
