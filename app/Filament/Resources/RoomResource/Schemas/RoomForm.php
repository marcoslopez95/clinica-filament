<?php

namespace App\Filament\Resources\RoomResource\Schemas;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class RoomForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                ...\App\Filament\Forms\Schemas\SimpleForm::schema('rooms'),

                TextInput::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->required()
                    ->step(0.01),

                \Filament\Forms\Components\Hidden::make('currency_id')
                    ->default(1),

                \App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
