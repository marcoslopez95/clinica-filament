<?php

namespace App\Filament\Forms\Tables;

use Filament\Tables\Columns\TextColumn;

class TimestampTable
{
    public static function columns(): array
    {
        return [
            TextColumn::make('created_at')
                ->label('Fecha de Creación')
                ->dateTime('d/m/Y H:i')
                ->sortable(),

            TextColumn::make('updated_at')
                ->label('Última Modificación')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
