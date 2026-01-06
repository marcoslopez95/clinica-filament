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
use App\Models\Supplier;

class InvoiceForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                \App\Filament\Forms\Components\StatusPlaceholder::make(),

                Select::make('invoiceable_id')
                    ->label('Proveedor')
                    ->options(fn() => Supplier::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->createOptionForm(\App\Filament\Resources\SupplierResource\Schemas\SupplierForm::schema())
                    ->createOptionUsing(fn (array $data) => Supplier::create($data)->id)
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $supplier = $state ? Supplier::find($state) : null;

                        $set('full_name', $supplier?->name ?? null);
                        $set('dni', $supplier?->document ?? null);
                        $set('type_document_id', $supplier?->type_document_id ?? null);
                    }),

                TextInput::make('full_name')
                    ->label('Nombre'),

                TextInput::make('dni')
                    ->label('Documento'),

                \App\Filament\Forms\Components\TypeDocumentSelect::make(),

                DatePicker::make('date')
                    ->label('Fecha')
                    ->default(now()->format('Y-m-d'))
                    ->required(),

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
