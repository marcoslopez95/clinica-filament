<?php

namespace App\Filament\Resources\RoomResource\Schemas;

use App\Models\Room;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class RoomForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),

                TextInput::make('description')
                    ->label('Descripción'),

                Placeholder::make('created_at')
                    ->label('Fecha de Creación')
                    ->content(fn(?Room $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Fecha de Última Modificación')
                    ->content(fn(?Room $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
