<?php

namespace App\Filament\Resources\Security\PermissionCategoryResource\Pages;

use App\Filament\Resources\Security\PermissionCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPermissionCategory extends EditRecord
{
    protected static string $resource = PermissionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSaveFormAction(): Actions\Action
    {
        return parent::getSaveFormAction()
            ->visible(fn(): bool => auth()->user()->can('categories.update'))
            ->action(function () {
                if (!auth()->user()->can('categories.update')) {
                    Notification::make()
                        ->title('Acceso denegado')
                        ->body('No tienes permiso para guardar cambios')
                        ->danger()
                        ->send();
                    return;
                }

                $this->save();
            });
    }
}
