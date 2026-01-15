<?php

namespace App\Filament\Resources\EntryResource\RelationManagers;

use App\Models\Product;
use App\Models\Inventory;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;
use Filament\Notifications\Notification;
use App\Filament\Actions\RefreshTotalDeleteAction;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?string $title = 'Detalles de Productos';

    protected function productFormSchema(): array
    {
        return [
            Select::make('content_id')
                ->label('Producto')
                ->options(function () {
                    $owner = $this->getOwnerRecord();
                    $used = $owner->details()
                        ->where('content_type', Product::class)->pluck('content_id')->toArray();

                    return Product::whereHas('inventory')
                        ->when(count($used) > 0, fn($q) => $q->whereNotIn('id', $used))
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
        ];
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
                    ->visible(fn (): bool => auth()->user()->can('entries.details.create'))
                    ->modalHeading('Crear nuevo producto y añadir a la entrada')
                    ->form([

                        ...\App\Filament\Resources\ProductResource\Schemas\ProductForm::schema(),

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

                        $warehouse = Warehouse::getFarmacia();

                        $inventory = Inventory::where('warehouse_id', $warehouse->id)
                            ->where('product_id', $product->id)
                            ->first();

                        if ($inventory) {
                            $inventory->increment('amount', $data['quantity']);
                        } else {
                            Inventory::create([
                                'product_id' => $product->id,
                                'warehouse_id' => $warehouse->id,
                                'stock_min' => 0,
                                'amount' => $data['quantity'],
                            ]);
                        }

                        $owner = $livewire->getOwnerRecord();
                        $owner
                            ->details()
                                ->create([
                                    'content_id' => $product->id,
                                    'content_type' => Product::class,
                                    'quantity' => $data['quantity'],
                                    'price' => $data['buy_price'],
                                ]);

                        $livewire->dispatch('refreshTotal');
                    }),

                CreateAction::make('add_existing')
                    ->label('Añadir producto existente')
                    ->visible(fn (): bool => auth()->user()->can('entries.details.attach'))
                    ->modalHeading('Añadir producto existente a la entrada')
                    ->form($this->productFormSchema())
                    ->action(function (array $data, $livewire) {
                        $owner = $livewire->getOwnerRecord();
                        $owner
                            ->details()
                            ->create([
                                'content_id' => $data['content_id'],
                                'content_type' => Product::class,
                                'price' => $data['price'],
                                'quantity' => $data['quantity'],
                            ]);

                        $warehouse = Warehouse::getFarmacia();

                        $inventory = Inventory::where('warehouse_id', $warehouse->id)
                            ->where('product_id', $data['content_id'])
                            ->first();

                        if ($inventory) {
                            $inventory->increment('amount', $data['quantity']);
                        } else {
                            Inventory::create([
                                'product_id' => $data['content_id'],
                                'warehouse_id' => $warehouse->id,
                                'stock_min' => 0,
                                'amount' => $data['quantity'],
                            ]);
                        }

                        $livewire->dispatch('refreshTotal');
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn (): bool => auth()->user()->can('entries.details.edit.view'))
                    ->form([
                        ...\App\Filament\Resources\ProductResource\Schemas\ProductForm::schema(),

                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->required()
                            ->readOnly(
                                fn ($get) => ! Inventory::where('product_id', $get('content_id'))->exists()
                            )
                            ->helperText(
                                fn ($get) => Inventory::where('product_id', $get('content_id'))->exists()
                                ? '  ' : 'Este producto no tiene inventario por ende no puede modificarse su cantidad'
                            ),
                    ])
                    ->mutateRecordDataUsing(function (array $data, Model $record): array {
                        $product = $record->content_type === Product::class ? $record->content : ($record->product ?? null);
                        if ($product) {
                            $data['name'] = $product->name;
                            $data['buy_price'] = $product->buy_price;
                            $data['sell_price'] = $product->sell_price;
                            $data['unit_id'] = $product->unit_id;
                            $data['product_category_id'] = $product->product_category_id;
                            $data['currency_id'] = $product->currency_id;
                            $data['content_id'] = $product->id;
                        }

                        return $data;
                    })
                    ->action(function (Model $record, array $data, $livewire): void {
                        if (!auth()->user()->can('entries.details.edit')) {
                            Notification::make()
                                ->title('Acceso denegado')
                                ->body('No tienes permiso para editar este elemento')
                                ->danger()
                                ->send();

                            return;
                        }

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
                            'content_id' => $data['content_id'] ?? $record->content_id,
                            'content_type' => Product::class,
                            'price' => $data['price'] ?? $record->price,
                            'quantity' => $data['quantity'] ?? $record->quantity,
                        ]);
                    })
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshTotal');
                    }),
                Action::make('taxes')
                    ->label('Impuestos')
                    ->color('warning')
                    ->icon('heroicon-m-receipt-percent')
                    ->modalHeading(false)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(fn (Model $record) => view('filament.actions.manage-taxes', ['record' => $record])),

                Action::make('batches')
                    ->label('Lotes')
                    ->color('success')
                    ->icon('heroicon-m-archive-box')
                    ->visible(function (Model $record): bool {
                        $p = $record->content_type === Product::class ? $record->content : ($record->product ?? null);
                        if (! $p || ! ($p->productCategory ?? null)) {
                            return false;
                        }
                        return strtolower(($p->productCategory->name) ?? '') === 'medicina';
                    })
                    ->modalHeading(false)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(fn (Model $record) => view('filament.actions.manage-batches', ['record' => $record])),

                RefreshTotalDeleteAction::make()
                    ->visible(fn (): bool => auth()->user()->can('entries.details.delete')),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('entries.details.view');
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->productFormSchema());
    }
}
