<?php

namespace App\Filament\Forms\Schemas;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class SimpleForm
{
    public static function schema(?string $uniqueTable = null): array
    {
        $nameField = TextInput::make('name')
            ->label('Nombre')
            ->required()
            ->maxLength(255);

        if ($uniqueTable) {
            $nameField->unique($uniqueTable, ignorable: true);
        }

        return [
            $nameField,

            Textarea::make('description')
                ->label('Descripción')
                ->rows(0)
                ->maxLength(500),
        ];
    }
}

