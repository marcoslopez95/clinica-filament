<?php

namespace App\Filament\Resources\UnitResource\Schemas;

use App\Models\Unit;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class UnitForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('symbol')
                    ->label('Símbolo')
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Fecha de creación')
                    ->content(fn(?Unit $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha Última Modificación')
                    ->content(fn(?Unit $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
