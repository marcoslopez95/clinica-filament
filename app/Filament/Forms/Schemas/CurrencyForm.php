<?php

namespace App\Filament\Forms\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use App\Models\Currency;

class CurrencyForm
{
    public static function schema(bool $disabledExchange = false): array
    {
        return [
            Hidden::make('currency_id')
                ->default(1),

            TextInput::make('exchange')
                ->label('Tasa de cambio')
                ->numeric()
                ->required()
                ->default(fn() => Currency::find(1)?->exchange ?? 1)
                ->disabled($disabledExchange)
                ->dehydrated(),
        ];
    }
}
