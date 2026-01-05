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

                \App\Filament\Forms\Components\TypeDocumentSelect::make(),

                TextInput::make('document')
                    ->label('Documento')
                    ->required(),
            ]);
    }
}
