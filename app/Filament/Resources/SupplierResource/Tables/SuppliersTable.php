<?php

namespace App\Filament\Resources\SupplierResource\Tables;

use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Table;

class SuppliersTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable(),

                TextColumn::make('typeDocument.name')
                    ->label('Tipo de Documento')
                    ->sortable(),

                TextColumn::make('document')
                    ->label('Documento')
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),

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
                    DeleteBulkAction::make()
                        ->visible(fn(): bool => auth()->user()->can('suppliers.bulk_delete')),
                ]),
            ]);
    }
}
