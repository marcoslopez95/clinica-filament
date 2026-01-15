<?php

namespace App\Filament\Resources\CurrencyResource\Schemas;

use App\Models\Currency;
use Filament\Forms\Components\Placeholder;
use App\Filament\Forms\Schemas\TimestampForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class CurrencyForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->unique('currencies', ignoreRecord: true),

                TextInput::make('symbol')
                    ->label('Símbolo')
                    ->required()
                    ->unique('currencies', ignoreRecord: true),

                TextInput::make('exchange')
                    ->label('Tasa de Cambio')
                    ->required()
                    ->numeric(),

                Select::make('paymentMethods')
                    ->label('Métodos de Pago')
                    ->relationship('paymentMethods', 'name')
                    ->multiple()
                    ->preload(),

                TimestampForm::schema(),
            ]);
    }
}
