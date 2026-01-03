<?php

namespace App\Filament\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class SimpleTable
{
    public static function make(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->label('Nombre')
                ->sortable()
                ->searchable(),

            TextColumn::make('description')
                ->label('Descripción')
                ->limit(50),
        ]);
    }
}
