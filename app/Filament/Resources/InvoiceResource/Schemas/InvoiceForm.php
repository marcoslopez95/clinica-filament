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
