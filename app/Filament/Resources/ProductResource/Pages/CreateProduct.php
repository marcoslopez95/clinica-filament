<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Request;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        $returnTo = Request::query('return_to');
        if ($returnTo) {
            $actions[] = Action::make('volver')
                ->label('Regresar')
                ->url(urldecode($returnTo));
        }

        return $actions;
    }
}
