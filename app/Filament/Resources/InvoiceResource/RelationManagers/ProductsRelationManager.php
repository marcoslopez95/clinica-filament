<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Models\Product;
use App\Models\Service;
use Filament\Tables\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ServiceResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Hidden;
use App\Filament\Actions\RefreshTotalDeleteAction;
use App\Models\Inventory;

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
                ->required()
                ->disabled(),

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
                    ->label('Subtotal'),

                TextColumn::make('content_type')
                    ->label('Tipo')
                    ->formatStateUsing(
                        fn (string $state) => match ($state) {
                            Product::class => 'Producto',
                            Service::class => 'Servicio',
                            default => 'Desconocido'
                        }
                    ),
            ])
            ->headerActions([

                Action::make('choose_resource')
                    ->label('Crear recurso')
                    ->modalHeading('Crear recurso')
                    ->modalWidth('sm')
                    ->visible(
                        fn () => auth()->user()->can('products.view') || auth()->user()->can('services.view')
                    )
                    ->form([
                        Radio::make('resource')
                            ->label(false)
                            ->options(function () {
                                $options = [];

                                if (auth()->user()->can('products.view')) {
                                    $options['product'] = 'Producto';
                                }

                                if (auth()->user()->can('services.view')) {
                                    $options['service'] = 'Servicio';
                                }

                                return $options;
                            })
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

                        return redirect($url);
                    }),

                CreateAction::make('add_existing')
                    ->label('Añadir existente')
                    ->visible(fn (): bool => auth()->user()->can('invoices.details.attach'))
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

                            $price = $data['price'] ?? null;
                            if (!$price) {
                                $model = match ($data['content_type']) {
                                    Product::class => Product::find($selectedId),
                                    Service::class => Service::find($selectedId),
                                    default => null,
                                };

                                if ($model) {
                                    $price = match ($data['content_type']) {
                                        Product::class => $model->sell_price ?? null,
                                        Service::class => $model->sell_price ?? null,
                                        default => null,
                                    };
                                }
                            }

                            $owner
                                ->details()
                                ->create([
                                    'content_id' => $selectedId,
                                    'content_type' => $data['content_type'],
                                    'price' => $price,
                                    'quantity' => $data['quantity'] ?? 1,
                                ]);

                            $livewire->dispatch('refreshTotal');
                        }),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn (): bool => auth()->user()->can('invoices.details.edit.view'))
                    ->form(function (Form $form) {
                        return $form->schema([
                            Hidden::make('content_type')
                                ->default(fn (?Model $record) => $record->content_type ?? Product::class),

                            Select::make('content_id')
                                ->label('Elemento')
                                ->options(function (Model $record) {
                                    $owner = $this->getOwnerRecord();
                                    $exclude = $record->id ?? null;
                                    $contentType = $record->content_type ?? Product::class;

                                    $query = $owner->details()->where('content_type', $contentType);
                                    if ($exclude) {
                                        $query->where('id', '!=', $exclude);
                                    }
                                    $used = $query->pluck('content_id')->toArray();

                                    $model = match ($contentType) {
                                        Service::class => Service::class,
                                        Product::class => Product::class,
                                        default       => null,
                                    };

                                    if (! $model) {
                                        return [];
                                    }

                                    $builder = $model::query();

                                    if ($model === Product::class) {
                                        $builder->whereHas('inventory');
                                    }

                                    return $builder
                                        ->when(count($used) > 0, fn($q) => $q->whereNotIn('id', $used))
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $contentId = $state;
                                    $contentType = $get('content_type');

                                    $model = match ($contentType) {
                                        Service::class => Service::find($contentId),
                                        Product::class => Product::find($contentId),
                                        default        => null,
                                    };

                                    if (! $model) {
                                        return;
                                    }

                                    $price = match ($contentType) {
                                        Service::class, Product::class => $model->sell_price ?? null,
                                        default                        => null,
                                    };

                                    $set('price', $price);
                                    $set('name', $model->name);
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
                    ->action(function (Model $record, array $data): void {

                        if (!auth()->user()->can('invoices.details.edit')) {
                            Notification::make()
                                ->title('Acceso denegado')
                                ->body('No tienes permiso para editar este elemento')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update([
                            'content_id' => $data['content_id'],
                            'price' => $data['price'],
                            'quantity' => $data['quantity'],
                        ]);
                    })
                    ->after(function ($livewire) {
                        $livewire->dispatch('refreshTotal');
                    }),

                RefreshTotalDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('invoices.details.view');
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->schema());
    }
}
