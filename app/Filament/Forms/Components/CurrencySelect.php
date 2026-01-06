<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Select;
use App\Models\Currency;

class CurrencySelect
{
    public static function make(): Select
    {
        return Select::make('currency_id')
            ->label('Moneda')
            ->options(fn() => Currency::pluck('name', 'id')->toArray())
            ->required()
            ->preload();
    }
}
