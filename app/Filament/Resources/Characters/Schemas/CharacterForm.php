<?php

namespace App\Filament\Resources\Characters\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CharacterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Fieldset::make('Informações Básicas')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('race')
                            ->required(),
                        Select::make('background')
                            ->options(
                                collect(\App\Enums\CharacterBackground::cases())
                                    ->mapWithKeys(fn ($bg) => [$bg->value => $bg->getLabel()])
                                    ->toArray()
                            )
                            ->searchable(),
                        Select::make('alignment')
                            ->options([
                                'Lawful Good' => 'Lawful Good',
                                'Neutral Good' => 'Neutral Good',
                                'Chaotic Good' => 'Chaotic Good',
                                'Lawful Neutral' => 'Lawful Neutral',
                                'True Neutral' => 'True Neutral',
                                'Chaotic Neutral' => 'Chaotic Neutral',
                                'Lawful Evil' => 'Lawful Evil',
                                'Neutral Evil' => 'Neutral Evil',
                                'Chaotic Evil' => 'Chaotic Evil',
                            ]),
                        Repeater::make('classes')
                            ->schema([
                                TextInput::make('class')->required(),
                                TextInput::make('level')->numeric()->required(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
                \Filament\Forms\Components\Fieldset::make('Inventário')
                    ->schema([
                        Repeater::make('inventory')
                            ->schema([
                                TextInput::make('name')->required(),
                                TextInput::make('quantity')->numeric()->required(),
                                TextInput::make('weight')->numeric(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
                \Filament\Forms\Components\Fieldset::make('Estatísticas de Jogo')
                    ->schema([
                        TextInput::make('level')
                            ->required()
                            ->numeric()
                            ->default(1),
                        TextInput::make('max_hp')
                            ->required()
                            ->numeric()
                            ->default(10),
                        TextInput::make('current_hp')
                            ->required()
                            ->numeric()
                            ->default(10),
                        TextInput::make('current_xp')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])->columns(4),

                \Filament\Forms\Components\Fieldset::make('Atributos (Rolagem)')
                    ->schema([
                        TextInput::make('strength')
                            ->required()
                            ->numeric()
                            ->default(10),
                        TextInput::make('dexterity')
                            ->required()
                            ->numeric()
                            ->default(10),
                        TextInput::make('constitution')
                            ->required()
                            ->numeric()
                            ->default(10),
                        TextInput::make('intelligence')
                            ->required()
                            ->numeric()
                            ->default(10),
                        TextInput::make('wisdom')
                            ->required()
                            ->numeric()
                            ->default(10),
                        TextInput::make('charisma')
                            ->required()
                            ->numeric()
                            ->default(10),
                    ])->columns(3),
            ]);
    }
}
