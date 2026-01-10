<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Models\Product;
use App\Models\Service;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ServiceResource;
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


    protected function schema(): array
    {
        return [
            Select::make('content_type')
                ->label('Tipo de contenido')
                ->options([
                    Product::class => 'Producto',
                    Service::class => 'Servicio',
                ])
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    $set('product_id', null);
                    $set('service_id', null);
                    $set('name', null);
                    $set('price', null);
                    $set('quantity', null);
                }),

            Select::make('product_id')
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
                ->required(fn($get) => $get('content_type') === Product::class)
                ->visible(fn($get) => $get('content_type') === Product::class)
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    $product = Product::find($state);
                    if ($product) {
                        $set('name', $product->name);
                        $set('price', $product->sell_price);
                    }
                }),

            Select::make('service_id')
                ->label('Servicio')
                ->options(function () {
                    $owner = $this->getOwnerRecord();
                    $used = $owner->details()
                        ->where('content_type', Service::class)->pluck('content_id')->toArray();

                    return Service::when(count($used) > 0, fn($q) => $q->whereNotIn('id', $used))
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->required(fn($get) => $get('content_type') === Service::class)
                ->visible(fn($get) => $get('content_type') === Service::class)
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    $service = Service::find($state);
                    if ($service) {
                        $set('name', $service->name);
                        $set('price', $service->sell_price ?? null);
                    }
                }),

            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->disabled(),

            TextInput::make('price')
                ->label('Precio')
                ->numeric()
                ->required(),

            TextInput::make('quantity')
                ->label('Cantidad')
                ->numeric()
                ->required(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->schema());
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
                Action::make('choose_resource')
                    ->label('Crear recurso')
                    ->modalHeading('Crear recurso')
                    ->form([
                        Radio::make('resource')
                            ->label(false)
                            ->options([
                                'product' => 'Producto',
                                'service' => 'Servicio',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        $map = [
                            'product' => ProductResource::class,
                            'service' => ServiceResource::class,
                        ];

                        $key = $data['resource'] ?? null;
                        if (! $key || ! isset($map[$key])) {
                            Notification::make()->danger()->body('Seleccione un recurso válido')->send();
                            return;
                        }

                        $resourceClass = $map[$key];
                        $url = $resourceClass::getUrl('create');

                        $returnTo = url()->previous() ?? request()->header('referer') ?? url()->current();
                        $separator = str_contains($url, '?') ? '&' : '?';
                        $url = $url . $separator . 'return_to=' . urlencode($returnTo) . '&return_from=relation';

                        return redirect($url);
                    }),

                CreateAction::make('add_existing')
                    ->label('Añadir existente')
                    ->modalHeading('Añadir recurso existente a la factura')
                    ->form($this->schema())
                        ->action(function (array $data, $livewire) {
                            $owner = $livewire->getOwnerRecord();

                            $selectedId = null;
                            switch ($data['content_type'] ?? null) {
                                case Product::class:
                                    $selectedId = $data['product_id'] ?? null;
                                    break;
                                case Service::class:
                                    $selectedId = $data['service_id'] ?? null;
                                    break;
                            }

                            if (! $selectedId) {
                                Notification::make()
                                    ->body('Seleccione un elemento válido para el tipo elegido')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            if ($owner->details()
                                ->where('content_type', $data['content_type'])
                                ->where('content_id', $selectedId)
                                ->exists()) {
                                Notification::make()
                                    ->body('El elemento ya fue agregado a los detalles')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $owner
                                ->details()
                                ->create([
                                    'content_id' => $selectedId,
                                    'content_type' => $data['content_type'],
                                    'price' => $data['price'] ?? null,
                                    'quantity' => $data['quantity'] ?? 1,
                                ]);

                            $livewire->dispatch('refreshTotal');
                        }),
            ])
            ->actions([
                EditAction::make()
                    ->form(function (Form $form) {
                        return $form->schema([
                            \Filament\Forms\Components\Hidden::make('content_type')
                                ->default(fn (?Model $record) => $record->content_type ?? Product::class),

                            Select::make('content_id')
                                ->label('Elemento')
                                ->options(function (Model $record) {
                                    $owner = $this->getOwnerRecord();
                                    $exclude = $record->id ?? null;
                                    $contentType = $record->content_type ?? Product::class;

                                    if ($contentType === Service::class) {
                                        $query = $owner->details()->where('content_type', Service::class);
                                        if ($exclude) {
                                            $query->where('id', '!=', $exclude);
                                        }
                                        $used = $query->pluck('content_id')->toArray();

                                        return Service::when(count($used) > 0, fn($q) => $q->whereNotIn('id', $used))
                                            ->pluck('name', 'id');
                                    }

                                    $query = $owner->details()->where('content_type', Product::class);
                                    if ($exclude) {
                                        $query->where('id', '!=', $exclude);
                                    }
                                    $used = $query->pluck('content_id')->toArray();

                                    return Product::whereHas('inventory')
                                        ->when(count($used) > 0, fn($q) => $q->whereNotIn('id', $used))
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $contentId = $state;
                                    $contentType = $get('content_type');

                                    if ($contentType === Service::class) {
                                        $service = Service::find($contentId);
                                        if ($service) {
                                            $set('price', $service->sell_price ?? null);
                                            $set('name', $service->name);
                                        }
                                    } else {
                                        $product = Product::find($contentId);
                                        if ($product) {
                                            $set('price', $product->sell_price);
                                            $set('name', $product->name);
                                        }
                                    }
                                }),

                            TextInput::make('price')
                                ->label('Precio')
                                ->numeric()
                                ->required(),

                            TextInput::make('quantity')
                                ->label('Cantidad')
                                ->numeric()
                                ->required()
                                ->readOnly(fn ($get) => ($get('content_type') === Product::class) ? ! \App\Models\Inventory::where('product_id', $get('content_id'))->exists() : false),
                        ]);
                    })
                    ->action(function (Model $record, array $data, $livewire): void {
                        $record->update([
                            'content_id' => $data['content_id'],
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
