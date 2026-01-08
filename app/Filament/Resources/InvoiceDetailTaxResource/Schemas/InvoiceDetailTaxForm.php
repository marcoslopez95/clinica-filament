<?php

namespace App\Filament\Resources\InvoiceDetailTaxResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class InvoiceDetailTaxForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nombre')
                ->required(),

            TextInput::make('percentage')
                ->label('Porcentaje')
                ->numeric()
                ->step(0.01)
                ->required()
                ->suffix('%')
                ->live(onBlur: true),

            TextInput::make('amount')
                ->label('Monto')
                ->numeric()
                ->required()
                ->prefix('$')
                ->live(onBlur: true),
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
