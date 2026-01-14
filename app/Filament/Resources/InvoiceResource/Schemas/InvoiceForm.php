<?php

namespace App\Filament\Resources\InvoiceResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Forms\Set;
use App\Enums\InvoiceStatus;
use App\Models\Patient;

class InvoiceForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                \App\Filament\Forms\Components\StatusPlaceholder::make(),

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

                TextInput::make('total')
                    ->label('Total')
                    ->type('number')
                    ->default(0)
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(2),

                ...\App\Filament\Forms\Schemas\CurrencyForm::schema(true),

                \App\Filament\Forms\Components\ToPay::make(),

                \App\Filament\Forms\Components\CancellationPlaceholder::make(),

                \App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
