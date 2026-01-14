<?php

namespace App\Filament\Forms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use App\Models\Currency;

class CurrencyForm
{
    public static function schema(bool $disabledExchange = false): array
    {
        return [
            Select::make('currency_id')
                ->label('Moneda')
                ->options(fn() => Currency::pluck('name', 'id'))
                ->required()
                ->live()
                ->afterStateUpdated(function (Set $set, ?int $state) {
                    if (!$state) {
                        $set('exchange', null);
                        return;
                    }

                    $currency = Currency::find($state);
                    $set('exchange', $currency?->exchange ?? null);
                }),

            TextInput::make('exchange')
                ->label('Tasa de cambio')
                ->numeric()
                ->required()
                ->disabled($disabledExchange)
                ->dehydrated(),
        ];
    }
}
