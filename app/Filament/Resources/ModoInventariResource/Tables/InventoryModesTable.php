<?php

namespace App\Filament\Resources\ModoInventariResource\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Inventory;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class InventoryModesTable
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
                    ->options(fn() => Warehouse::pluck('name', 'id')->toArray())
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
                Action::make('move_stock')
                    ->label('Mover Stock')
                    ->visible(fn(): bool => auth()->user()->can('inventory_modes.move'))
                    ->icon('heroicon-o-arrows-right-left')
                    ->form([
                        TextInput::make('current_amount')
                            ->label('Cantidad actual')
                            ->disabled()
                            ->default(fn(Inventory $record) => $record->amount),
                        TextInput::make('move_amount')
                            ->label('Cantidad a mover')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(fn(Inventory $record) => $record->amount),
                        Select::make('target_warehouse_id')
                            ->label('Almacén destino')
                            ->options(Warehouse::pluck('name', 'id'))
                            ->required()
                            ->default(fn(Inventory $record) => $record->warehouse_id),
                    ])
                    ->action(function (Inventory $record, array $data): void {
                        $moveAmount = $data['move_amount'];
                        $targetWarehouseId = $data['target_warehouse_id'];

                        if ($record->warehouse_id == $targetWarehouseId) {
                            Notification::make()
                                ->title('Error')
                                ->body('El almacén de destino no puede ser el mismo que el de origen.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Restar del origen
                        $record->decrement('amount', $moveAmount);

                        // Buscar o crear en destino
                        $targetInventory = Inventory::firstOrCreate(
                            [
                                'product_id' => $record->product_id,
                                'warehouse_id' => $targetWarehouseId,
                            ],
                            [
                                'amount' => 0,
                                'stock_min' => $record->stock_min,
                                'batch' => $record->batch,
                                'end_date' => $record->end_date,
                            ]
                        );

                        $targetInventory->increment('amount', $moveAmount);

                        Notification::make()
                            ->title('Movimiento realizado')
                            ->body("Se han movido {$moveAmount} unidades correctamente.")
                            ->success()
                            ->send();
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn(): bool => auth()->user()->can('inventory_modes.bulk_delete')),
                ]),
            ]);
    }
}
