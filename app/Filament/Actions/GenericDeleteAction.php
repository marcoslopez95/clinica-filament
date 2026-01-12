<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\DeleteAction;

class GenericDeleteAction
{
    public static function make(): DeleteAction
    {
        return DeleteAction::make()
            ->label('Eliminar')
            ->modalHeading(fn ($record) => 'Eliminar Registro')
            ->modalDescription('¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.')
            ->modalSubmitActionLabel('Confirmar')
            ->color('danger')
            ->icon('heroicon-o-trash');
    }
}