<?php

namespace App\Filament\Forms\Schemas;

use Filament\Tables\Columns\TextColumn;

class SimpleTable
{
    public static function columns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Nombre')
                ->searchable()
                ->sortable(),

            TextColumn::make('description')
                ->label('Descripción')
                ->limit(50),
        ];
    }
}
