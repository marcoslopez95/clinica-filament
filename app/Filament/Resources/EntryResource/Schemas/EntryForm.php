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
use App\Filament\Forms\Schemas\TimestampForm;
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

                \App\Filament\Forms\Components\StatusPlaceholder::make(),

                Placeholder::make('is_expired')
                    ->label('Condición')
                    ->content(fn(?Invoice $record): string =>
                        $record?->is_expired === null
                            ? 'Sin Condición'
                            : ($record->is_expired ? 'Vencida' : 'Sin vencer')
                    ),

                Select::make('invoiceable_id')
                    ->label('Proveedor')
                    ->options(fn() => Supplier::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->createOptionForm(\App\Filament\Resources\SupplierResource\Schemas\SupplierForm::schema())
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

                \App\Filament\Forms\Components\TypeDocumentSelect::make(),

                DatePicker::make('date')
                    ->label('Fecha de factura')
                    ->default(now()->format('Y-m-d'))
                    ->required(),

                DatePicker::make('credit_date')
                    ->label('Fecha de crédito')
                    ->required(),

                ...\App\Filament\Forms\Schemas\CurrencyForm::schema(),

                TextInput::make('total')
                    ->label('Total')
                    ->type('number')
                    ->default(0)
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(2),

                \App\Filament\Forms\Components\ToPay::make(),

                ...\App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
