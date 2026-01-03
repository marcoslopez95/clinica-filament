<?php

namespace App\Filament\Resources\EntryResource\Schemas;

use App\Enums\InvoiceStatus;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Supplier;
use App\Models\TypeDocument;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;

class EntryForm
{
    public static function configure(Form $form): Form
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

                Placeholder::make('to_pay')
                    ->label('Por Pagar')
                    ->content(function (?Invoice $record): string {
                        if (!$record) return '0.00 $';
                        return number_format($record->to_pay_with_discounts, 2) . ' $';
                    }),

                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?Invoice $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha de Última Modificación')
                    ->content(fn(?Invoice $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
