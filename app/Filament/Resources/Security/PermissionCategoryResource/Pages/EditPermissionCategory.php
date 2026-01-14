<?php

namespace App\Filament\Resources\Security\PermissionCategoryResource\Pages;

use App\Filament\Resources\Security\PermissionCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermissionCategory extends EditRecord
{
    protected static string $resource = PermissionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
