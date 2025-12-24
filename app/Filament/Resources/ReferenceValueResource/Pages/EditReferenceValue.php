<?php

namespace App\Filament\Resources\ReferenceValueResource\Pages;

use App\Filament\Resources\ReferenceValueResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReferenceValue extends EditRecord
{
    protected static string $resource = ReferenceValueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
