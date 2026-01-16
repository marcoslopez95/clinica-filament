<?php

namespace App\Filament\Resources\InventoryResource\Tables;

use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use App\Exports\InventoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Collection;

class InventoriesTable
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

                TextColumn::make('stock_min')
                    ->label('Stock Minimo'),

                TextColumn::make('amount')
                    ->label('Cantidad'),

                TextColumn::make('batch')
                    ->label('Lote'),

                TextColumn::make('end_date')
                    ->label('Fecha Expiración')
                    ->date(),

                TextColumn::make('observation')
                    ->label('Observaciones'),

                ...\App\Filament\Forms\Tables\TimestampTable::columns(),
            ])

            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn(Table $table) => Excel::download(
                        new InventoryExport($table->getFilteredTableQuery()->get()),
                        'reporte-inventario-' . now()->format('Y-m-d') . '.xlsx'
                    )),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn(): bool => auth()->user()->can('inventories.bulk_delete')),
                    BulkAction::make('exportSelectedExcel')
                        ->label('Exportar Excel (Seleccionados)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(fn(Collection $records) => Excel::download(
                            new InventoryExport($records),
                            'inventario-seleccionado-' . now()->format('Y-m-d') . '.xlsx'
                        )),
                ]),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->relationship('product', 'name')
                    ->label('Producto')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->label('Almacén')
                    ->searchable()
                    ->preload(),
            ]);
    }
}
