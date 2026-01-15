<?php

namespace App\Filament\Resources\UnitResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;

class UnitForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->unique('units', ignoreRecord: true),

                TextInput::make('symbol')
                    ->label('Símbolo')
                    ->required()
                    ->unique('units', ignoreRecord: true),

                Select::make('categories')
                    ->label('Categorías')
                    ->multiple()
                    ->relationship('categories', 'name')
                    ->preload()
                    ->searchable(),

                \App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
