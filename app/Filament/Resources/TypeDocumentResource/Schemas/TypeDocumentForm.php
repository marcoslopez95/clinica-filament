<?php

namespace App\Filament\Resources\TypeDocumentResource\Schemas;

use App\Models\TypeDocument;
use Filament\Forms\Components\Placeholder;
use App\Filament\Forms\Schemas\TimestampForm;
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

                ...TimestampForm::schema(),
            ]);
    }
}
