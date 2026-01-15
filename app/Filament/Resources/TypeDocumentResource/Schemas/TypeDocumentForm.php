<?php

namespace App\Filament\Resources\TypeDocumentResource\Schemas;

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
                    ->required()
                    ->unique(ignorable: true),

                TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->unique(ignorable: true),

                \App\Filament\Forms\Schemas\TimestampForm::schema(),
            ]);
    }
}
