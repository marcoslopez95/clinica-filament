<?php

namespace App\Filament\Resources\HozpitaliacionesResource\RelationManagers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Exam;
use App\Models\Service;
use App\Models\Room;
use App\Enums\ResourceType;
use Filament\Tables\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ExamResource;
use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\RoomResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use App\Filament\Actions\RefreshTotalDeleteAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Actions\LoadResultsAction;
use \Filament\Forms\Components\Hidden;
class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?string $title = 'Detalles de Productos';

    protected function schema(): array
    {
        return [
            Select::make('content_type')
                ->label('Tipo de contenido')
                ->options(function () {
                    return collect(ResourceType::cases())
                        ->mapWithKeys(fn($case) => [$case->value => $case->getName()])
                        ->toArray();
                })
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    $set('product_id', null);
                    $set('exam_id', null);
                    $set('service_id', null);
                    $set('room_id', null);
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

            Select::make('exam_id')
                ->label('Examen')
                ->options(function () {
                    $owner = $this->getOwnerRecord();
                    $used = $owner->details()
                        ->where('content_type', Exam::class)->pluck('content_id')->toArray();

                    return Exam::when(count($used) > 0, fn($q) => $q->whereNotIn('id', $used))
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->required(fn($get) => $get('content_type') === Exam::class)
                ->visible(fn($get) => $get('content_type') === Exam::class)
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    $exam = Exam::find($state);
                    if ($exam) {
                        $set('name', $exam->name);
                        $set('price', $exam->price);
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
                        $set('price', $service->buy_price);
                    }
                }),
            Select::make('room_id')
                ->label('Habitación')
                ->options(function () {
                    $owner = $this->getOwnerRecord();
                    $used = $owner->details()
                        ->where('content_type', Room::class)->pluck('content_id')->toArray();

                    return Room::when(count($used) > 0, fn($q) => $q->whereNotIn('id', $used))
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->required(fn($get) => $get('content_type') === Room::class)
                ->visible(fn($get) => $get('content_type') === Room::class)
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    $room = Room::find($state);
                    if ($room) {
                        $set('name', $room->name);
                        $set('price', $room->price ?? null);
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
                ->required(fn($get) => in_array($get('content_type'), [Product::class, Service::class]))
                ->visible(fn($get) => in_array($get('content_type'), [Product::class, Service::class])),
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

                Action::make('choose_resource')
                    ->label('Crear recurso')
                    ->modalHeading('Crear recurso')
                    ->form([
                        Radio::make('resource')
                            ->label(false)
                            ->options([
                                'product' => 'Producto',
                                'exam' => 'Examen',
                                'room' => 'Habitación',
                                'service' => 'Servicio',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        $map = [
                            'product' => ProductResource::class,
                            'exam' => ExamResource::class,
                            'room' => RoomResource::class,
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
                    ->modalHeading('Añadir producto existente a la entrada')
                    ->form($this->schema())
                        ->action(function (array $data, $livewire) {
                            $owner = $livewire->getOwnerRecord();

                            $selectedId = null;
                            switch ($data['content_type'] ?? null) {
                                case Product::class:
                                    $selectedId = $data['product_id'] ?? null;
                                    break;
                                case Exam::class:
                                    $selectedId = $data['exam_id'] ?? null;
                                    break;
                                case Service::class:
                                    $selectedId = $data['service_id'] ?? null;
                                    break;
                                case Room::class:
                                    $selectedId = $data['room_id'] ?? null;
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
                                    'quantity' => in_array($data['content_type'] ?? '', [Product::class, Service::class]) ? ($data['quantity'] ?? 1) : null,
                                ]);

                            $livewire->dispatch('refreshTotal');
                        }),
            ])
            ->actions([
                EditAction::make()
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

                                    if ($contentType === Exam::class) {
                                        $query = $owner->details()->where('content_type', Exam::class);
                                        if ($exclude) {
                                            $query->where('id', '!=', $exclude);
                                        }
                                        $used = $query->pluck('content_id')->toArray();

                                        return Exam::when(count($used) > 0, fn($q) => $q->whereNotIn('id', $used))
                                            ->pluck('name', 'id');
                                    }

                                    if ($contentType === Service::class) {
                                        $query = $owner->details()->where('content_type', Service::class);
                                        if ($exclude) {
                                            $query->where('id', '!=', $exclude);
                                        }
                                        $used = $query->pluck('content_id')->toArray();

                                        return Service::when(count($used) > 0, fn($q) => $q->whereNotIn('id', $used))
                                            ->pluck('name', 'id');
                                    }

                                    if ($contentType === Room::class) {
                                        $query = $owner->details()->where('content_type', Room::class);
                                        if ($exclude) {
                                            $query->where('id', '!=', $exclude);
                                        }
                                        $used = $query->pluck('content_id')->toArray();

                                        return Room::when(count($used) > 0, fn($q) => $q->whereNotIn('id', $used))
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
                                            $set('price', $service->buy_price ?? $service->sell_price ?? null);
                                            $set('name', $service->name);
                                        }
                                    } elseif ($contentType === Exam::class) {
                                        $exam = Exam::find($contentId);
                                        if ($exam) {
                                            $set('price', $exam->price ?? null);
                                            $set('name', $exam->name);
                                        }
                                    } elseif ($contentType === Room::class) {
                                        $room = Room::find($contentId);
                                        if ($room) {
                                            $set('price', $room->price ?? null);
                                            $set('name', $room->name);
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
                                ->readOnly(fn ($get) => ($get('content_type') === Product::class) ? ! Inventory::where('product_id', $get('content_id'))->exists() : false),
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

                LoadResultsAction::make(),
                    
                RefreshTotalDeleteAction::make(),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->schema());
    }

}
