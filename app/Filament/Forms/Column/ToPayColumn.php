<?php

namespace App\Filament\Forms\column;

use Filament\Tables\Columns\TextColumn;

class ToPayColumn
{
    public static function make(string $field = 'to_pay_with_discounts'): TextColumn
    {
        return TextColumn::make($field)
            ->label('Por Pagar');
    }
}
