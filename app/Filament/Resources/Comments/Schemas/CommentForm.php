<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\Select::make('post_id')
                    ->relationship('post', 'title')
                    ->required()
                    ->searchable(),
                \Filament\Forms\Components\TextInput::make('author')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\Textarea::make('content')
                    ->required()
                    ->rows(4),
                \Filament\Forms\Components\TextInput::make('score')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
