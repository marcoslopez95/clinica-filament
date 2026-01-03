<?php

namespace App\Filament\Resources\InvoiceResource\Schemas;

use App\Enums\InvoiceStatus;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Product;
use App\Models\TypeDocument;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;

class InvoiceForm
{
    public static function configure(Form $form): Form
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
                        $patient = Patient::find($state);

                        $set('full_name', $patient->first_name . ' ' . ($patient->last_name ?? ''));
                        $set('dni', $patient->full_document);
                        $set('type_document_id', $patient->typeDocument->id);
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
                                        modifyQueryUsing: fn (Get $get, Builder $query) => $query
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
                ...\App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
