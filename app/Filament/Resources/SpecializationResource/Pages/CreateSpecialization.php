<?php

namespace App\Filament\Resources\SpecializationResource\Pages;

use App\Filament\Resources\SpecializationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecialization extends CreateRecord
{
    protected static string $resource = SpecializationResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
