<?php

namespace App\Filament\Resources\PaymentMethodResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;

class PaymentMethodForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                ...\App\Filament\Forms\Schemas\SimpleForm::schema(),

                Select::make('currencies')
                    ->label('Moneda')
                    ->relationship('currencies', 'name')
                    ->multiple()
                    ->preload(),

                ...\App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
