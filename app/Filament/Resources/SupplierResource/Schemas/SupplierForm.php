<?php

namespace App\Filament\Resources\SupplierResource\Schemas;

use App\Models\Supplier;
use App\Models\TypeDocument;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class SupplierForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                Select::make('type_document_id')
                    ->label('Tipo de Documento')
                    ->relationship('typeDocument', 'name')
                    ->required(),

                TextInput::make('document')
                    ->label('Documento')
                    ->required(),
            ]);
    }
}
