<?php

namespace App\Filament\Forms\Schemas;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class SimpleForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->label('Descripción')
                ->rows(0)
                ->maxLength(500),
        ];
    }
}

