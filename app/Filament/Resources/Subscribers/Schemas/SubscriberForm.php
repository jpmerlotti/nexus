<?php

namespace App\Filament\Resources\Subscribers\Schemas;

use Filament\Schemas\Schema;

class SubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }
}
