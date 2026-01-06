<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder as FormPlaceholder;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductCategory;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'productDetails';

    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';

    public function form(Form $form): Form
    {
        return $form->schema([
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
                ->searchable()
                ->required(),

            Select::make('product_category_id')
                ->label('Categoría')
                ->required()
                ->options(fn() => ProductCategory::pluck('name', 'id'))
                ->searchable()
                ->required(),

            Select::make('currency_id')
                ->label('Moneda')
                ->required()
                ->options(fn() => Currency::pluck('name', 'id'))
                ->searchable(),

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
                TextColumn::make('product.name')
                    ->label('Producto asociado')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label('Cantidad asignada'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Añadir producto')
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
                            ->searchable()
                            ->required(),

                        Select::make('product_category_id')
                            ->label('Categoría')
                            ->required()
                            ->options(fn() => ProductCategory::pluck('name', 'id'))
                            ->searchable()
                            ->required(),

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
                        $owner = $livewire->getOwnerRecord();
                        $owner->productDetails()->create([
                            'product_id' => $product->id,
                            'quantity' => $data['quantity'],
                        ]);
                    }),

                CreateAction::make('add_existing')
                    ->label('Añadir producto existente')
                    ->form([
                        Select::make('product_id')
                            ->label('Producto')
                            ->options(function () {
                                $owner = $this->getOwnerRecord();
                                $used = $owner->productDetails()->pluck('product_id')->toArray();
                                return Product::whereNotIn('id', $used)->pluck('name', 'id')->toArray();
                            })
                            ->searchable()
                            ->required(),

                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        $owner = $livewire->getOwnerRecord();
                        $owner->productDetails()->create([
                            'product_id' => $data['product_id'],
                            'quantity' => $data['quantity'],
                        ]);
                    }),
            ])
            ->actions([
                EditAction::make()
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
                            ->searchable()
                            ->required(),

                        Select::make('product_category_id')
                            ->label('Categoría')
                            ->required()
                            ->options(fn() => ProductCategory::pluck('name', 'id'))
                            ->searchable()
                            ->required(),

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
                    ->mutateRecordDataUsing(function ($data, $record) {
                        if ($record->product) {
                            $data['name'] = $record->product->name;
                            $data['buy_price'] = $record->product->buy_price;
                            $data['sell_price'] = $record->product->sell_price;
                            $data['unit_id'] = $record->product->unit_id;
                            $data['product_category_id'] = $record->product->product_category_id;
                            $data['currency_id'] = $record->product->currency_id;
                        }
                        $data['quantity'] = $record->quantity;
                        return $data;
                    })
                    ->action(function ($data, $record) {
                        if ($record->product) {
                            $record->product->update([
                                'name' => $data['name'],
                                'buy_price' => $data['buy_price'],
                                'sell_price' => $data['sell_price'],
                                'unit_id' => $data['unit_id'],
                                'product_category_id' => $data['product_category_id'],
                                'currency_id' => $data['currency_id'],
                            ]);
                        }
                        $record->update([
                            'quantity' => $data['quantity'],
                        ]);
                    }),
                DeleteAction::make(),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Productos del servicio';
    }
}
