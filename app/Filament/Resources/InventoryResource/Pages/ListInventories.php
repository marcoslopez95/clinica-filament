<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use App\Filament\Resources\InventoryModeResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use App\Models\Inventory;
use App\Models\Warehouse;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;

class ListInventories extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mover_inventario')
                ->label('Mover Varios')
                ->icon('heroicon-o-arrows-right-left')
                ->modalWidth(MaxWidth::FiveExtraLarge)
                ->form([
                    Select::make('from_warehouse_id')
                        ->label('Almacén Origen')
                        ->options(Warehouse::pluck('name', 'id'))
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($set) => $set('items', [])),
                    Select::make('to_warehouse_id')
                        ->label('Almacén Destino')
                        ->options(Warehouse::pluck('name', 'id'))
                        ->required()
                        ->reactive(),
                    \Filament\Forms\Components\Grid::make(3)
                        ->schema([
                            \Filament\Forms\Components\Placeholder::make('product_header')
                                ->label('Producto')
                                ->content('')
                                ->extraAttributes(['class' => 'font-bold']),
                            \Filament\Forms\Components\Placeholder::make('stock_header')
                                ->label('Stock Actual')
                                ->content('')
                                ->extraAttributes(['class' => 'font-bold']),
                            \Filament\Forms\Components\Placeholder::make('quantity_header')
                                ->label('Cantidad a mover')
                                ->content('')
                                ->extraAttributes(['class' => 'font-bold']),
                        ])
                        ->extraAttributes(['class' => 'mb-[-20px] ml-2']),
                    Repeater::make('items')
                        ->label('')
                        ->schema([
                            Select::make('inventory_id')
                                ->label('Producto')
                                ->hiddenLabel()
                                ->options(function (callable $get) {
                                    $fromWarehouseId = $get('../../from_warehouse_id');
                                    if (!$fromWarehouseId) {
                                        return [];
                                    }
                                    return Inventory::where('warehouse_id', $fromWarehouseId)
                                        ->with('product')
                                        ->get()
                                        ->mapWithKeys(function ($inventory) {
                                            return [$inventory->id => $inventory->product->name . " (Stock: {$inventory->amount})"];
                                        });
                                })
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set) {
                                    if ($state) {
                                        $inventory = Inventory::find($state);
                                        $set('available_stock', $inventory?->amount ?? 0);
                                    } else {
                                        $set('available_stock', 0);
                                    }
                                })
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            TextInput::make('available_stock')
                                ->label('Stock Actual')
                                ->hiddenLabel()
                                ->disabled()
                                ->dehydrated(false)
                                ->numeric(),
                            TextInput::make('quantity')
                                ->label('Cantidad a mover')
                                ->hiddenLabel()
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->rules([
                                    fn (callable $get) => function (string $attribute, $value, $fail) use ($get) {
                                        $inventoryId = $get('inventory_id');
                                        if ($inventoryId) {
                                            $inventory = Inventory::find($inventoryId);
                                            if ($inventory && $value > $inventory->amount) {
                                                $fail("La cantidad a mover ({$value}) no puede ser mayor al stock disponible ({$inventory->amount}).");
                                            }
                                        }
                                    },
                                ]),
                        ])
                        ->columns(3)
                        ->defaultItems(1)
                        ->addActionLabel('Añadir producto')
                        ->itemLabel(fn (array $state): ?string => $state['inventory_id'] ? null : 'Nuevo producto')
                        ->collapsible()
                ])
                ->action(function (array $data): void {
                    if ($data['from_warehouse_id'] === $data['to_warehouse_id']) {
                        Notification::make()
                            ->title('Error')
                            ->body('El almacén de origen y destino no pueden ser el mismo.')
                            ->danger()
                            ->send();
                        return;
                    }

                    \DB::transaction(function () use ($data) {
                        foreach ($data['items'] as $item) {
                            $sourceInventory = Inventory::find($item['inventory_id']);
                            $quantity = $item['quantity'];

                            // Doble validación por si acaso cambió el stock entre que abrió el modal y guardó
                            if ($sourceInventory->amount < $quantity) {
                                throw new \Exception("Stock insuficiente para el producto: {$sourceInventory->product->name}");
                            }

                            // Restar del origen
                            $sourceInventory->decrement('amount', $quantity);

                            // Sumar al destino
                            $targetInventory = Inventory::firstOrCreate(
                                [
                                    'product_id' => $sourceInventory->product_id,
                                    'warehouse_id' => $data['to_warehouse_id'],
                                ],
                                [
                                    'amount' => 0,
                                    'stock_min' => $sourceInventory->stock_min,
                                    'batch' => $sourceInventory->batch,
                                    'end_date' => $sourceInventory->end_date,
                                ]
                            );

                            $targetInventory->increment('amount', $quantity);
                        }
                    });

                    Notification::make()
                        ->title('Movimiento completado')
                        ->body('Los productos se han movido correctamente.')
                        ->success()
                        ->send();
                })
                ->modalSubmitActionLabel('Mover'),

            Action::make('modo_inventario')
                ->label('Mover')
                ->url(InventoryModeResource::getUrl('index')),

            CreateAction::make(),
        ];
    }
}
