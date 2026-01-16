<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Hidden;
use App\Models\Currency;

class CurrencySelect
{
    public static function make(): Hidden
    {
        return Hidden::make('currency_id')
            ->default(1);
    }
}
