<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceStatus;
use App\Filament\Resources\EntryResource\Pages;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Supplier;
use App\Models\TypeDocument;
use App\Enums\InvoiceType;
use Filament\Forms\Components\Hidden;
use App\Models\Product;
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
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntryResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $slug = 'entries';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Entrada';
    protected static ?string $pluralModelLabel = 'Entradas';
    protected static ?string $navigationLabel = 'Entradas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('invoiceable_id')
                    ->label('Proveedor')
                    ->options(fn() => Supplier::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $supplier = Supplier::findOrFail($state);
                        $set('full_name', $supplier->name);
                        $set('dni', $supplier->rif);
                    }),

                TextInput::make('full_name')
                    ->label('Nombre'),

                TextInput::make('dni')
                    ->label('Documento'),
                    
                Select::make('type_document_id')
                    ->label('Tipo de Documento')
                    ->options(fn() => TypeDocument::all()->pluck('name','id'))
                    ->default(fn() => TypeDocument::where('code', 'RIF')->first()?->id)
                    ->required(),

                DatePicker::make('date')
                    ->label('Fecha')->default(now()->format('Y-m-d')),

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
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, ?int $state, Get $get) {
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
                    ->schema([
                        Repeater::make('payments')->label('Pagos')
                            ->relationship()
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
                                $total = collect($get('payments'))->sum(function($item) {
                                    $exchange = $item['exchange'] ?? 1;
                                    $amount = $item['amount'] ?? 0;
                                    return $item['currency_id'] === 1 ? $amount : $amount/$exchange;
                                });
                                $pay = +$get('total');
                                return $pay - $total;
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
                TextColumn::make('to_pay')->label('Por Pagar'),
                TextColumn::make('status')->label('Estado')->searchable()->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('Status')
                ->options(collect(InvoiceStatus::cases())
                    ->map(fn(InvoiceStatus $status) => $status->value)
                    ->toArray()
                )->attribute('status')
            ])
            ->actions([
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
            'index' => Pages\ListEntries::route('/'),
            'create' => Pages\CreateEntry::route('/create'),
            'edit' => Pages\EditEntry::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->where('invoice_type', InvoiceType::INVENTORY->value);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
