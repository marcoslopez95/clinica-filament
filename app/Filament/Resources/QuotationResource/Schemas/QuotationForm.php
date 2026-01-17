<?php

namespace App\Filament\Resources\QuotationResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use App\Models\Patient;
use App\Enums\InvoiceType;
use Filament\Forms\Components\Hidden;

class QuotationForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                \App\Filament\Forms\Components\StatusPlaceholder::make(),

                Hidden::make('is_quotation')->default(true),

                Select::make('invoice_type')
                    ->label('Tipo de cotización')
                    ->options([
                        InvoiceType::DEFAULT->value => InvoiceType::DEFAULT->getName(),
                        InvoiceType::LABORATORY->value => InvoiceType::LABORATORY->getName(),
                        InvoiceType::HOSPITALIZATION->value => InvoiceType::HOSPITALIZATION->getName(),
                        InvoiceType::CONSULT->value => InvoiceType::CONSULT->getName(),
                    ])
                    ->default(InvoiceType::DEFAULT->value)
                    ->required()
                    ->live(),

                Select::make('invoiceable_id')
                    ->label('Paciente')
                    ->options(fn() => Patient::all()->pluck('full_name', 'id'))
                    ->searchable()
                    ->required()
                    ->createOptionForm(\App\Filament\Resources\PatientResource\Schemas\PatientForm::schema())
                    ->createOptionUsing(fn (array $data) => Patient::create($data)->id)
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $patient = $state ? Patient::find($state) : null;

                        $set('full_name', $patient ? $patient->first_name . ($patient->last_name ? ' ' . $patient->last_name : '') : null );
                        $set('dni', $patient?->full_document ?? null);
                        $set('type_document_id', $patient?->typeDocument?->id ?? null);
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

                DatePicker::make('credit_date')
                    ->label('Vigencia')
                    ->nullable(),

                TextInput::make('total')
                    ->label('Total')
                    ->type('number')
                    ->default(0)
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(2),

                \App\Filament\Forms\Components\ToPay::make(),

                \App\Filament\Forms\Components\CancellationPlaceholder::make(),
//
                \App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
