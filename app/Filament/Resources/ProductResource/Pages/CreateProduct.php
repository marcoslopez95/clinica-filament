<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Request;

/**
 * @property Product $record
 */
class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function afterCreate()
    {
        $warehouse = Warehouse::getFarmacia();
        $inventory = new Inventory([
            'stock_min' => 0,
            'amount' => 0,
            'warehouse_id' => $warehouse->id,
            'product_id' => $this->record->id
        ]);
        $inventory->save();
    }
}
