<?php

namespace App\Filament\Resources\RoomResource\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Table;
use \Filament\Tables\Columns\TextColumn;

class RoomsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ...\App\Filament\Forms\Tables\SimpleTable::columns(),

                TextColumn::make('price')
                    ->label('Precio')
                    ->sortable(),

                TextColumn::make('currency.name')
                    ->label('Moneda'),

                ...\App\Filament\Forms\Tables\TimestampTable::columns(),
            ])
            
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
