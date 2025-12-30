<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Patient;
use App\Enums\InvoiceType;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder as FormPlaceholder;
use App\Models\TypeDocument;
use App\Models\Product;
use Filament\Forms\Components\Actions\Action;
use Faker\Provider\Text;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'invoices';

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $modelLabel = 'Factura';
    protected static ?string $pluralModelLabel = 'Facturas';
    protected static ?string $navigationLabel = 'Facturas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('status')
                    ->label('Estado')
                    ->content(fn(?Invoice $record): string => $record?->status instanceof InvoiceStatus ? $record->status->getName() : ($record?->status ? (InvoiceStatus::tryFrom($record->status)?->getName() ?? $record->status) : InvoiceStatus::OPEN->getName())),

                Select::make('invoiceable_id')
                    ->label('Paciente')
                    ->options(fn() => Patient::all()->pluck('full_name', 'id'))
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('first_name')
                            ->label('Nombre')
                            ->required(),
                        TextInput::make('last_name')
                            ->label('Apellido'),
                        Select::make('type_document_id')
                            ->label('Tipo de Documento')
                            ->options(fn() => TypeDocument::all()->pluck('name','id'))
                            ->required(),
                        TextInput::make('dni')
                            ->label('Documento')
                            ->required(),
                        DatePicker::make('born_date')
                            ->label('Fecha de Nacimiento'),
                        TextInput::make('address')
                            ->label('Dirección')
                            ->required(),
                    ])
                    ->createOptionUsing(fn (array $data): int => Patient::create($data)->id)
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $patient = $state ? Patient::find($state) : null;
                        $set('full_name', $patient ? $patient->first_name.' '.$patient->last_name : null);
                        $set('dni', $patient?->full_document);
                        $set('type_document_id', $patient?->typeDocument?->id);
                    }),

                TextInput::make('full_name')
                    ->label('Nombre'),

                TextInput::make('dni')
                    ->label('Documento'),

                Select::make('type_document_id')
                    ->label('Tipo de Documento')
                    ->options(fn() => TypeDocument::all()->pluck('name','id'))
                    ->required()
                    ->disabled()
                    ->dehydrated(),

                DatePicker::make('date')
                    ->label('Fecha')
                    ->default(now()->format('Y-m-d'))
                    ->required(),

                Section::make('')
                    ->label('Detalles')
                    ->description('Productos asociados a la factura')
                    ->schema([
                        Repeater::make('details')->label('Detalles')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship(
                                        name: 'product',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query) => $query->whereHas('inventory')
                                    )
                                    ->label('Producto')
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nombre')
                                            ->required(),
                                        TextInput::make('buy_price')
                                            ->label('Precio de Compra')
                                            ->required()
                                            ->numeric(),
                                        TextInput::make('sell_price')
                                            ->label('Precio de Venta')
                                            ->required()
                                            ->numeric(),
                                        Select::make('unit_id')
                                            ->label('Unidad')
                                            ->required()
                                            ->relationship('unit', 'name')
                                            ->preload(),
                                        Select::make('product_category_id')
                                            ->label('Categoría')
                                            ->required()
                                            ->relationship('productCategory', 'name')
                                            ->preload(),
                                        Select::make('currency_id')
                                            ->label('Moneda')
                                            ->required()
                                            ->relationship('currency', 'name')
                                            ->preload(),
                                    ])
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, ?int $state, Get $get) {
                                        if (!$state) {
                                            $set('price', 0);
                                            $set('quantity', null);
                                            return;
                                        }
                                        $product = Product::with('inventory')->find($state);

                                        if ($product?->inventory) {
                                            $set('price', $product->sell_price ?? 0);
                                            $set('quantity', null);
                                        }
                                    }),

                                TextInput::make('price')
                                    ->label('Precio')
                                    ->type('number')
                                    ->disabled()
                                    ->required()
                                    ->dehydrated(),

                                TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->type('number')
                                    ->required()
                                    ->disabled(fn(Get $get) => !$get('product_id'))
                                    ->live(),
                            ])
                            ->columns(3)->columnSpan(2)
                            ->afterStateUpdated(function (Set $set, mixed $state){
                                $total = collect($state)->sum(fn($item) => $item['quantity'] * $item['price']);
                                $set('total', $total);
                            })
                            ,
                    ]),

                TextInput::make('total')
                    ->label('Total')
                    ->type('number')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(2),

                Section::make('')
                    ->label('Pagos')
                    ->description('Pagos Asignados a la factura')
                    ->collapsible()
                    ->visible(fn (?Invoice $record) => $record !== null)
                    ->schema([
                        Repeater::make('payments')->label('Pagos')
                            ->relationship()
                            ->defaultItems(0)
                            ->schema([
                                Select::make('payment_method_id')
                                    ->relationship('paymentMethod', 'name')
                                    ->label('Método de Pago')
                                    ->required()
                                    ->live(),

                                Select::make('currency_id')
                                    ->relationship('currency', 'name',
                                        modifyQueryUsing: fn (Get $get,Builder $query) => $query
                                            ->whereRelation(
                                                'paymentMethods',
                                                'payment_methods.id',$get('payment_method_id')
                                            )
                                    )
                                    ->label('Moneda')
                                    ->disabled(fn(Get $get) => !$get('payment_method_id'))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, mixed $state){
                                        $currency = Currency::find($state);
                                        $set('exchange', $currency->exchange ?? 0);
                                    }),

                                TextInput::make('amount')
                                    ->label('Monto')
                                    ->type('number')
                                    ->required()
                                    ->disabled(fn(Get $get) => !$get('currency_id'))
                                    ->live(debounce: 500),

                                TextInput::make('exchange')
                                    ->label('Tasa de Cambio')
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->columns(3)->columnSpan(2)
                        ,

                        Placeholder::make('to_pay')
                            ->label('Por Pagar')
                            ->content(function (Get $get): string {
                                $totalPayments = collect($get('payments'))->sum(function($item) {
                                    $exchange = (float) ($item['exchange'] ?? 1);
                                    $amount = (float) ($item['amount'] ?? 0);
                                    $currencyId = (int) ($item['currency_id'] ?? 0);

                                    if ($exchange <= 0) {
                                        $exchange = 1;
                                    }

                                    return $currencyId === 1 ? $amount : $amount / $exchange;
                                });

                                $pay = (float) $get('total');
                                return number_format($pay - $totalPayments, 2) . ' $';
                            }),
                    ]),
                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?Invoice $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha de Última Modificación')
                    ->content(fn(?Invoice $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->sortable()->searchable(),
                TextColumn::make('dni')->sortable()->searchable(),
                TextColumn::make('date')->label('Fecha')->date()->sortable()->searchable(),
                TextColumn::make('total')->label('Total'),
                TextColumn::make('balance')->label('Por Pagar'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn(InvoiceStatus $state): string => $state->getName())
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('Status')
                ->options(InvoiceStatus::class)
                ->attribute('status')
            ])
            ->actions([
                TableAction::make('Cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Invoice $record) => $record->update(['status' => InvoiceStatus::CANCELLED]))
                    ->hidden(fn (Invoice $record) => $record->status === InvoiceStatus::CANCELLED),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InventoryRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])->where('invoice_type', InvoiceType::DEFAULT->value);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
