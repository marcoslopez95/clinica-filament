<?php

namespace App\Filament\Resources\EntryResource\RelationManagers;

use App\Models\Currency;
use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductCategory;
use App\Models\Inventory;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Actions\Action as FormAction;
use App\Models\Warehouse;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?string $title = 'Detalles de Productos';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('content_id')
                ->label('Producto')
                ->options(function () {
                    $owner = $this->getOwnerRecord();
                    $used = $owner->details()->where('content_type', Product::class)->pluck('content_id')->toArray();
                    return Product::whereHas('inventory')
                        ->whereNotIn('id', $used)
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    $product = Product::find($state);
                    if ($product) {
                        $set('price', $product->buy_price);
                    }
                }),

            TextInput::make('price')
                ->label('Precio de compra')
                ->numeric()
                ->required(),

            TextInput::make('quantity')
                ->label('Cantidad')
                ->numeric()
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('content.name')
                    ->label('Producto')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Cantidad'),
                TextColumn::make('price')
                    ->label('Precio'),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('USD')
                    ->state(fn (Model $record): float => $record->quantity * $record->price),
            ])
            ->headerActions([
                CreateAction::make('create_new')
                    ->label('Crear producto')
                    ->modalHeading('Crear nuevo producto y añadir a la entrada')
                    ->form([
                        TextInput::make('name')
                            ->label('Nombre del producto')
                            ->required(),

                        TextInput::make('buy_price')
                            ->label('Precio de compra')
                            ->numeric()
                            ->required(),

                        TextInput::make('sell_price')
                            ->label('Precio de venta')
                            ->numeric()
                            ->required(),

                        Select::make('unit_id')
                            ->label('Unidad')
                            ->required()
                            ->options(fn() => Unit::pluck('name', 'id'))
                            ->searchable(),

                        Select::make('product_category_id')
                            ->label('Categoría')
                            ->required()
                            ->options(fn() => ProductCategory::pluck('name', 'id'))
                            ->searchable(),

                        Select::make('currency_id')
                            ->label('Moneda')
                            ->required()
                            ->options(fn() => Currency::pluck('name', 'id'))
                            ->searchable(),

                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        $product = Product::create([
                            'name' => $data['name'],
                            'buy_price' => $data['buy_price'],
                            'sell_price' => $data['sell_price'],
                            'unit_id' => $data['unit_id'],
                            'product_category_id' => $data['product_category_id'],
                            'currency_id' => $data['currency_id'],
                        ]);

                        $warehouseId = Warehouse::where('name', 'Bodega')->first()?->id;

                        Inventory::create([
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouseId,
                            'stock_min' => 0,
                            'amount' => 0,
                        ]);

                        $owner = $livewire->getOwnerRecord();
                        $owner->details()->create([
                            'content_id' => $product->id,
                            'content_type' => Product::class,
                            'quantity' => $data['quantity'],
                            'price' => $data['buy_price'],
                        ]);

                        $livewire->dispatch('refreshTotal');
                    }),

                CreateAction::make('add_existing')
                    ->label('Añadir producto existente')
                    ->modalHeading('Añadir producto existente a la entrada')
                    ->form([
                        Select::make('content_id')
                            ->label('Producto')
                            ->options(function () {
                                $owner = $this->getOwnerRecord();
                                $used = $owner->details()->where('content_type', Product::class)->pluck('content_id')->toArray();
                                return Product::whereHas('inventory')
                                    ->whereNotIn('id', $used)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                $product = Product::find($state);
                                if ($product) {
                                    $set('price', $product->buy_price);
                                }
                            }),

                        TextInput::make('price')
                            ->label('Precio de compra')
                            ->numeric()
                            ->required(),

                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        $owner = $livewire->getOwnerRecord();
                        $owner->details()->create([
                            'content_id' => $data['content_id'],
                            'content_type' => Product::class,
                            'price' => $data['price'],
                            'quantity' => $data['quantity'],
                        ]);

                        $livewire->dispatch('refreshTotal');
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->form(function (Form $form) {
                        return $form->schema([
                            Select::make('content_id')
                                ->label('Producto')
                                ->options(function (Model $record) {
                                    $owner = $this->getOwnerRecord();
                                    $used = $owner->details()
                                        ->where('id', '!=', $record->id)
                                        ->where('content_type', Product::class)
                                        ->pluck('content_id')
                                        ->toArray();
                                    return Product::whereHas('inventory')
                                        ->whereNotIn('id', $used)
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('price', $product->buy_price);
                                        $set('name', $product->name);
                                        $set('buy_price', $product->buy_price);
                                        $set('sell_price', $product->sell_price);
                                        $set('unit_id', $product->unit_id);
                                        $set('product_category_id', $product->product_category_id);
                                        $set('currency_id', $product->currency_id);
                                    }
                                }),

                            TextInput::make('name')
                                ->label('Nombre del producto')
                                ->required(),

                            TextInput::make('buy_price')
                                ->label('Precio de compra (Producto)')
                                ->numeric()
                                ->required(),

                            TextInput::make('sell_price')
                                ->label('Precio de venta')
                                ->numeric()
                                ->required(),

                            Select::make('unit_id')
                                ->label('Unidad')
                                ->required()
                                ->options(fn() => Unit::pluck('name', 'id'))
                                ->searchable(),

                            Select::make('product_category_id')
                                ->label('Categoría')
                                ->required()
                                ->options(fn() => ProductCategory::pluck('name', 'id'))
                                ->searchable(),

                            Select::make('currency_id')
                                ->label('Moneda')
                                ->required()
                                ->options(fn() => Currency::pluck('name', 'id'))
                                ->searchable(),

                            TextInput::make('price')
                                ->label('Precio de compra (Entrada)')
                                ->numeric()
                                ->required(),

                            TextInput::make('quantity')
                                ->label('Cantidad')
                                ->numeric()
                                ->required(),
                        ]);
                    })
                    ->mutateRecordDataUsing(function (array $data, Model $record): array {
                        $product = $record->content_type === Product::class ? $record->content : $record->product;
                        if ($product) {
                            $data['name'] = $product->name;
                            $data['buy_price'] = $product->buy_price;
                            $data['sell_price'] = $product->sell_price;
                            $data['unit_id'] = $product->unit_id;
                            $data['product_category_id'] = $product->product_category_id;
                            $data['currency_id'] = $product->currency_id;
                        }
                        return $data;
                    })
                    ->action(function (Model $record, array $data, $livewire): void {
                        $product = $record->content_type === Product::class ? $record->content : $record->product;
                        if ($product) {
                            $product->update([
                                'name' => $data['name'],
                                'buy_price' => $data['buy_price'],
                                'sell_price' => $data['sell_price'],
                                'unit_id' => $data['unit_id'],
                                'product_category_id' => $data['product_category_id'],
                                'currency_id' => $data['currency_id'],
                            ]);
                        }

                        $record->update([
                            'content_id' => $data['content_id'],
                            'content_type' => Product::class,
                            'price' => $data['price'],
                            'quantity' => $data['quantity'],
                        ]);

                        $livewire->dispatch('refreshTotal');
                    })
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshTotal');
                    }),
                Action::make('taxes')
                    ->label('Taxes')
                    ->color('warning')
                    ->icon('heroicon-m-receipt-percent')
                    ->modalHeading('Gestionar Impuestos')
                    ->mountUsing(fn (Form $form, Model $record) => $form->fill([
                        'taxes' => $record->taxes->toArray(),
                    ]))
                    ->action(function (Model $record, array $data): void {
                        $record->taxes()->delete();
                        $taxes = collect($data['taxes'])->map(function ($tax) {
                            unset($tax['id']);
                            return $tax;
                        })->toArray();
                        $record->taxes()->createMany($taxes);
                    })
                    ->form([
                        Repeater::make('taxes')
                            ->label('Impuestos')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required(),
                                TextInput::make('percentage')
                                    ->label('Porcentaje')
                                    ->numeric()
                                    ->step(0.01)
                                    ->required()
                                    ->suffix('%')
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, $set, Model $record) {
                                        $price = $record->price ?? 0;
                                        $quantity = $record->quantity ?? 0;
                                        $percentage = (float)$state;
                                        $amount = ($price * $quantity) * ($percentage / 100);
                                        $set('amount', $amount);
                                    })
                                    ->suffixAction(
                                        FormAction::make('calculateAmount')
                                            ->label('Calcular')
                                            ->icon('heroicon-m-calculator')
                                            ->action(function ($state, $set, Model $record) {
                                                $price = $record->price ?? 0;
                                                $quantity = $record->quantity ?? 0;
                                                $percentage = (float)$state;
                                                $amount = ($price * $quantity) * ($percentage / 100);
                                                $set('amount', $amount);
                                            })
                                    ),
                                TextInput::make('amount')
                                    ->label('Monto')
                                    ->numeric()
                                    ->required()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, $set, Model $record) {
                                        $price = $record->price ?? 0;
                                        $quantity = $record->quantity ?? 0;
                                        $total = $price * $quantity;
                                        $amount = (float)$state;
                                        if ($total > 0) {
                                            $set('percentage', ($amount / $total) * 100);
                                        }
                                    }),
                            ])
                            ->columns(3)
                    ]),
                Action::make('batches')
                    ->label('Lotes')
                    ->color('success')
                    ->icon('heroicon-m-archive-box')
                    ->visible(fn (Model $record): bool =>
                        ($record->content_type === Product::class ? $record->content : $record->product) &&
                        (($record->content_type === Product::class ? $record->content : $record->product)->productCategory ?? null) &&
                        strtolower((($record->content_type === Product::class ? $record->content : $record->product)->productCategory->name) ?? '') === 'medicina'
                    )
                    ->modalHeading('Gestionar Lotes')
                    ->mountUsing(fn (Form $form, Model $record) => $form->fill([
                        'batches' => $record->batchDetails->toArray(),
                    ]))
                    ->action(function (Model $record, array $data): void {
                        $record->batchDetails()->delete();
                        $batches = collect($data['batches'])->map(function ($batch) {
                            unset($batch['id']);
                            return $batch;
                        })->toArray();
                        $record->batchDetails()->createMany($batches);
                    })
                    ->form([
                        Repeater::make('batches')
                            ->label('Lotes')
                            ->schema([
                                TextInput::make('batch_number')
                                    ->label('Número de Lote')
                                    ->required(),
                                DatePicker::make('expiration_date')
                                    ->label('Fecha de Vencimiento')
                                    ->required(),
                                TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->step(1)
                                    ->required(),
                            ])
                            ->columns(3)
                    ]),
                DeleteAction::make()
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshTotal');
                    }),
            ]);
    }
}
