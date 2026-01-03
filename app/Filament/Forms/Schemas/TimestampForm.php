<?php

namespace App\Filament\Forms\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;

class TimestampForm
{
    public static function schema(): array
    {
        return [
            Section::make()
                ->schema([
                    Placeholder::make('created_at')
                        ->label('Fecha de Creación')
                        ->content(fn($record): string => $record?->created_at?->diffForHumans() ?? '-'),

                    Placeholder::make('updated_at')
                        ->label('Fecha de Última Modificación')
                        ->content(fn($record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                ])
                ->columns(2),
        ];
    }
}
