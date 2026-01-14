<?php

namespace App\Filament\Resources\Security\PermissionCategoryResource\Pages;

use App\Filament\Resources\Security\PermissionCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermissionCategories extends ListRecords
{
    protected static string $resource = PermissionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
