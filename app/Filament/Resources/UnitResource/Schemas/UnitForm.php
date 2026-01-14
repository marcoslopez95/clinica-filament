<?php

namespace App\Filament\Resources\UnitResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class UnitForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('symbol')
                    ->label('Símbolo')
                    ->required(),

                \App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
