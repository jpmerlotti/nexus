<?php

namespace App\Models;

use App\Enums\CharacterRace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    /** @use HasFactory<\Database\Factories\CharacterFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'race',
        'classes',
        'background',
        'alignment',
        'level',
        'max_hp',
        'current_hp',
        'current_xp',
        'strength',
        'dexterity',
        'constitution',
        'intelligence',
        'wisdom',
        'charisma',
        'inventory',
        'backstory',
        'appearance',
        'status',
        'notes',
        'relationships',
    ];

    protected function casts(): array
    {
        return [
            'race' => CharacterRace::class,
            'classes' => 'array',
            'inventory' => 'array',
            'appearance' => 'array',
            'relationships' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_character');
    }

    /**
     * Get the modifier for a given attribute score.
     */
    public function modifier(string $attribute): int
    {
        $score = $this->{$attribute} ?? 10;

        return floor(($score - 10) / 2);
    }

    /**
     * Calculate the character's Armor Class (AC).
     */
    public function armorClass(): int
    {
        $baseAC = 10 + $this->modifier('dexterity');

        $inventory = $this->inventory ?? [];
        $equippedArmor = collect($inventory)->filter(fn ($item) => ($item['type'] ?? '') === 'Armor' && ($item['equipped'] ?? false)
        )->sortByDesc(function ($item) {
            // Simple logic: extract a number from description or assume a base for common types
            // For now, let's look for a pattern like "AC: 15" or similar, or just a default
            return preg_match('/AC:\s*(\d+)/i', $item['description'] ?? '', $matches) ? (int) $matches[1] : 0;
        })->first();

        if ($equippedArmor) {
            preg_match('/AC:\s*(\d+)/i', $equippedArmor['description'] ?? '', $matches);
            $armorBase = isset($matches[1]) ? (int) $matches[1] : 10;

            // Handle Dex limits (Heavy armor usually 0 dex, Medium usually max 2)
            $dexMod = $this->modifier('dexterity');
            $desc = strtolower($equippedArmor['description'] ?? '');

            if (str_contains($desc, 'heavy')) {
                return $armorBase;
            }

            if (str_contains($desc, 'medium')) {
                return $armorBase + min($dexMod, 2);
            }

            return $armorBase + $dexMod;
        }

        return $baseAC;
    }
}
