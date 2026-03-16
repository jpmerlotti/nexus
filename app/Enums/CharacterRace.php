<?php

namespace App\Enums;

enum CharacterRace: string
{
    case Human = 'human';
    case Elf = 'elf';
    case Dwarf = 'dwarf';
    case Halfling = 'halfling';
    case Dragonborn = 'dragonborn';
    case Gnome = 'gnome';
    case HalfElf = 'half_elf';
    case HalfOrc = 'half_orc';
    case Tiefling = 'tiefling';

    public function getLabel(): string
    {
        return match ($this) {
            self::Human => 'Human',
            self::Elf => 'Elf',
            self::Dwarf => 'Dwarf',
            self::Halfling => 'Halfling',
            self::Dragonborn => 'Dragonborn',
            self::Gnome => 'Gnome',
            self::HalfElf => 'Half-Elf',
            self::HalfOrc => 'Half-Orc',
            self::Tiefling => 'Tiefling',
        };
    }
}
