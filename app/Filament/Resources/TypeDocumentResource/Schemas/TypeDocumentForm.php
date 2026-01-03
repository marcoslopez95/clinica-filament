<?php

namespace App\Filament\Resources\TypeDocumentResource\Schemas;

use App\Models\TypeDocument;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class TypeDocumentForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('code')
                    ->label('Código')
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?TypeDocument $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha de Última Modificación')
                    ->content(fn(?TypeDocument $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
