<?php

namespace App\Filament\Resources\DiscountResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class DiscountForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('percentage')
                ->label('Porcentaje (%)')
                ->numeric()
                ->live(),

            TextInput::make('amount')
                ->label('Monto')
                ->numeric()
                ->required()
                ->live(),

            TextInput::make('description')
                ->label('Descripción')
                ->columnSpanFull(),
        ];
    }

    public static function configure(Form $form): Form
    {
        return $form->schema([
            ...self::schema(),
            ...\App\Filament\Forms\Schemas\TimestampForm::schema(),
        ]);
    }
}
