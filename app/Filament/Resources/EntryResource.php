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
use Filament\Forms\Components\Actions\Action;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Resources\EntryResource\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\EntryResource\RelationManagers\InventoryRelationManager;

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
                Placeholder::make('status')
                    ->label('Estado')
                    ->content(fn(?Invoice $record): string => $record?->status instanceof InvoiceStatus ? $record->status->getName() : ($record?->status ? (InvoiceStatus::tryFrom($record->status)?->getName() ?? $record->status) : InvoiceStatus::OPEN->getName())),

                Placeholder::make('is_expired')
                    ->label('Condición')
                    ->content(fn(?Invoice $record): string => $record?->is_expired ? 'Vencida' : 'Sin vencer'),

                Select::make('invoiceable_id')
                    ->label('Proveedor')
                    ->options(fn() => Supplier::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        Select::make('type_document_id')
                            ->label('Tipo de Documento')
                            ->options(fn() => TypeDocument::all()->pluck('name','id'))
                            ->required(),
                        TextInput::make('document')
                            ->label('Documento')
                            ->required(),
                    ])
                    ->createOptionUsing(fn (array $data): int => Supplier::create($data)->id)
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $supplier = $state ? Supplier::find($state) : null;
                        $set('full_name', $supplier?->name);
                        $set('dni', $supplier?->document);
                        $set('type_document_id', $supplier?->type_document_id);
                    }),

                TextInput::make('invoice_number')
                    ->label('Número de factura')
                    ->columnSpan(2),

                TextInput::make('full_name')
                    ->label('Nombre'),

                TextInput::make('dni')
                    ->label('Documento'),

                Select::make('type_document_id')
                    ->label('Tipo de Documento')
                    ->options(fn() => TypeDocument::all()->pluck('name','id'))
                    ->required(),

                DatePicker::make('date')
                    ->label('Fecha de factura')
                    ->default(now()->format('Y-m-d'))
                    ->required(),

                DatePicker::make('credit_date')
                    ->label('Fecha de crédito')
                    ->required(),

                Select::make('currency_id')
                    ->label('Moneda')
                    ->relationship('currency', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?int $state) {
                        if (!$state) {
                            $set('exchange', null);
                            return;
                        }
                        $currency = Currency::find($state);
                        $set('exchange', $currency->exchange ?? null);
                    }),

                TextInput::make('exchange')
                    ->label('Tasa de cambio')
                    ->numeric()
                    ->required(),

                Section::make('Pagos')
                    ->description('Pagos Asignados a la Entrada')
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
                                    ->numeric()
                                    ->required()
                                    ->disabled(fn(Get $get) => !$get('currency_id'))
                                    ->live(debounce: 500),

                                TextInput::make('exchange')
                                    ->label('Tasa de Cambio')
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->columns(3)->columnSpan(2),

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

                                $totalDiscounts = collect($get('discounts'))->sum(function($item) {
                                    return (float) ($item['amount'] ?? 0);
                                });

                                $pay = (float) $get('total');
                                return number_format($pay - $totalPayments - $totalDiscounts, 2) . ' $';
                            }),
                    ]),

                Section::make('Descuentos')
                    ->collapsible()
                    ->visible(fn (?Invoice $record) => $record !== null)
                    ->schema([
                        Repeater::make('discounts')
                            ->relationship()
                            ->defaultItems(0)
                            ->schema([
                                TextInput::make('percentage')
                                    ->label('Porcentaje (%)')
                                    ->numeric()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $total = (float) $get('../../total');
                                        $percentage = (float) $state;
                                        $set('amount', $total * ($percentage / 100));
                                    })
                                    ->suffixAction(
                                        Action::make('calculateAmount')
                                            ->icon('heroicon-m-calculator')
                                            ->action(function (Get $get, Set $set, $state) {
                                                $total = (float) $get('../../total');
                                                $percentage = (float) $state;
                                                $set('amount', $total * ($percentage / 100));
                                            })
                                    ),
                                TextInput::make('amount')
                                    ->label('Monto')
                                    ->numeric()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $total = (float) $get('../../total');
                                        $amount = (float) $state;
                                        if ($total > 0) {
                                            $set('percentage', ($amount / $total) * 100);
                                        }
                                    })
                                    ->suffixAction(
                                        Action::make('calculatePercentage')
                                            ->icon('heroicon-m-calculator')
                                            ->action(function (Get $get, Set $set, $state) {
                                                $total = (float) $get('../../total');
                                                $amount = (float) $state;
                                                if ($total > 0) {
                                                    $set('percentage', ($amount / $total) * 100);
                                                }
                                            })
                                    ),
                                TextInput::make('description')
                                    ->label('Descripción')
                                    ->columnSpan(2),
                            ])
                            ->columns(2)
                            ->live()
                            ->columnSpanFull(),
                    ]),

                TextInput::make('total')
                    ->label('Monto')
                    ->numeric()
                    ->default(0)
                    ->readOnly()
                    ->dehydrated()
                    ->suffixAction(
                        Action::make('calculateTotal')
                            ->icon('heroicon-m-calculator')
                            ->label('Calcular')
                            ->action(function (Set $set, ?Invoice $record) {
                                if ($record) {
                                    $total = $record->details()->sum('subtotal');
                                    $record->update(['total' => $total]);
                                    $set('total', $total);
                                }
                            })
                    ),

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
                TextColumn::make('total')->label('Monto'),
                TextColumn::make('currency.name')->label('Moneda'),
                TextColumn::make('exchange')->label('Tasa de cambio'),
                TextColumn::make('to_pay_with_discounts')->label('Por Pagar'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn(InvoiceStatus $state): string => $state->getName())
                    ->searchable()
                    ->sortable(),
                TextColumn::make('is_expired')
                    ->label('Condición')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Vencida' : 'Sin vencer')
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

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
            InventoryRelationManager::class,
        ];
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
