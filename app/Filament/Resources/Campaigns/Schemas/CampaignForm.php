<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                \Filament\Forms\Components\Select::make('narration_detail_level')
                    ->options([
                        'succinct' => 'Sucinto / Direto',
                        'normal' => 'Normal',
                        'detailed' => 'Detalhado / Livro',
                    ])
                    ->required()
                    ->default('normal'),
                \Filament\Forms\Components\Select::make('difficulty')
                    ->options([
                        'easy' => 'Fácil / Modo História',
                        'normal' => 'Normal',
                        'hard' => 'Difícil / Sobrevivência',
                    ])
                    ->required()
                    ->default('normal'),
                TextInput::make('starting_level')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(20),
                \Filament\Forms\Components\Select::make('play_style')
                    ->options([
                        'combat_focused' => 'Foco em Combate',
                        'roleplay' => 'Foco em Roleplay (Interpretação e Enigmas)',
                        'balanced' => 'Equilibrado',
                    ])
                    ->required()
                    ->default('balanced'),
                \Filament\Forms\Components\Select::make('progression_type')
                    ->options([
                        'xp' => 'Por Experiência (XP)',
                        'milestone' => 'Por Marcos (Milestone)',
                    ])
                    ->required()
                    ->default('xp'),
            ]);
    }
}
