<?php

namespace App\Filament\Resources\UnitCategoryResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;

class UnitCategoryForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->unique(ignorable: true),

            Select::make('units')
                ->label('Unidades')
                ->multiple()
                ->relationship('units', 'name')
                ->preload()
                ->searchable(),
        ];
    }

    public static function configure(Form $form): Form
    {
        return $form->schema([
            ...self::schema(),

            \App\Filament\Forms\Schemas\TimestampForm::schema(),
        ]);
    }
}
