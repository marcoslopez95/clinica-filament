<?php

namespace App\Filament\Forms\Component;

use Filament\Forms\Components\Select;
use App\Models\TypeDocument;

class TypeDocumentSelect
{
    public static function make(): Select
    {
        return Select::make('type_document_id')
            ->label('Tipo de Documento')
            ->relationship('typeDocument', 'name')
            ->required();
    }
}
