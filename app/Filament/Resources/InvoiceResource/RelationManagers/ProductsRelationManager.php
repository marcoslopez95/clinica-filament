<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Models\Currency;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductCategory;
use App\Models\Warehouse;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Model;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?string $title = 'Productos de la factura';

    protected function getUsedContentIds(Model $owner, ?int $excludeRecordId = null): array
    {
        $query = $owner->details()->where('content_type', Product::class);
        if ($excludeRecordId) {
            $query->where('id', '!=', $excludeRecordId);
        }
        return $query->pluck('content_id')->toArray();
    }

    protected function getAvailableProducts(Model $owner, ?int $excludeRecordId = null)
    {
        $used = $this->getUsedContentIds($owner, $excludeRecordId);
        return Product::whereHas('inventory')
            ->when(count($used) > 0, fn($q) => $q->whereNotIn('id', $used))
            ->pluck('name', 'id');
    }

    protected function fillPriceFromProduct($state, $set): void
    {
        $product = Product::find($state);
        if ($product) {
            $set('price', $product->buy_price);
        }
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('content_id')
                ->label('Producto')
                ->options(fn() => $this->getAvailableProducts($this->getOwnerRecord()))
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    $this->fillPriceFromProduct($state, $set);
                }),

            TextInput::make('price')
                ->label('Precio')
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
                    ->modalHeading('Crear nuevo producto y añadir a la factura')
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
                    ->modalHeading('Añadir producto existente a la factura')
                    ->form([
                        Select::make('content_id')
                            ->label('Producto')
                            ->options(fn() => $this->getAvailableProducts($this->getOwnerRecord()))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                $this->fillPriceFromProduct($state, $set);
                            }),

                        TextInput::make('price')
                            ->label('Precio')
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
                                    return $this->getAvailableProducts($this->getOwnerRecord(), $record->id ?? null);
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

                            TextInput::make('price')
                                ->label('Precio')
                                ->numeric()
                                ->required(),

                            TextInput::make('quantity')
                                ->label('Cantidad')
                                ->numeric()
                                ->required(),
                        ]);
                    })
                    ->mutateRecordDataUsing(function (array $data, Model $record): array {
                        $product = $record->content_type === Product::class ? $record->content : ($record->product ?? null);
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
                        $product = $record->content_type === Product::class ? $record->content : ($record->product ?? null);
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
                DeleteAction::make()
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshTotal');
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
