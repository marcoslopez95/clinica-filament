<?php

namespace App\Filament\Forms\columns;

use Filament\Tables\Columns\TextColumn;
use App\Enums\InvoiceStatus;

class CancellationColumn
{
    public static function make(): TextColumn
    {
        return TextColumn::make('cancellation_reason')
            ->label('Motivo de anulación')
            ->default('-')
            ->toggleable(isToggledHiddenByDefault: true);
    }
}

