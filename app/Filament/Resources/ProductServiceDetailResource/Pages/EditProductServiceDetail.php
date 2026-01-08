<?php

namespace App\Filament\Resources\ProductServiceDetailResource\Pages;

use App\Filament\Resources\ProductServiceDetailResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProductServiceDetail extends EditRecord
{
    protected static string $resource = ProductServiceDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
