<?php

namespace App\Filament\Resources\ModoInventariResource\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class ModoInventarisTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('warehouse.name')
                    ->label('Almacén')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Cantidad'),

                ...\App\Filament\Forms\Tables\TimestampTable::columns(),
            ])
            ->filters([
                SelectFilter::make('exclude_warehouse')
                    ->label('Almacén (excluir)')
                    ->options(fn () => Warehouse::pluck('name', 'id')->toArray())
                    ->default(
                        Warehouse::where('name', 'Bodega')->value('id') // por defecto "Bodega"
                    )
                    ->query(function (Builder $query, $state) {
                        if ($state) {
                            $query->where('warehouse_id', '!=', $state);
                        }
                    }),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
