<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Placeholder;
use App\Models\Invoice;
use App\Enums\InvoiceStatus;

class StatusPlaceholder
{
    public static function make(): Placeholder
    {
        return Placeholder::make('status')
                ->label('Estado')
                    ->content(fn(?Invoice $record): string => $record?->status?->getName() ?? 'Sin estado');
    }
}