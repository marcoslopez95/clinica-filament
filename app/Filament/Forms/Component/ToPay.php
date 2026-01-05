<?php

namespace App\Filament\Forms\Component;

use App\Models\Invoice;
use Filament\Forms\Components\Placeholder;

class ToPay
{
    public static function make(): Placeholder
    {
        return Placeholder::make('to_pay')
            ->label('Por Pagar')
            ->content(function (?Invoice $record): string {
                if (!$record) return '0.00 $';
                return number_format($record->to_pay_with_discounts, 2) . ' $';
            });
    }
}
