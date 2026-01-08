<?php

namespace App\Filament\Forms\columns;

use Filament\Tables\Columns\TextColumn;
use App\Enums\InvoiceStatus;

class StatusColumn
{
    public static function make(): TextColumn
    {
        return TextColumn::make('status')
            ->label('Estado')
            ->formatStateUsing(fn (InvoiceStatus $state): string => $state->getName())
            ->searchable()
            ->sortable();
    }
}
