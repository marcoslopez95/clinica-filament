<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\ProductCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class InventoryImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $descripcion = $row['descripcion'] ?? null;
            $unidadNombre = $row['unidad'] ?? null;
            $precioVenta = $row['precio_venta'] ?? 0;
            $precioCompra = $row['precio_compra'] ?? 0;
            $existencia = $row['existencia'] ?? 0;
            $almacenNombre = $row['almacen'] ?? null;
            $categoriaNombre = $row['categoria'] ?? null;

            if (!$descripcion) {
                continue;
            }

            // 1. Unidad
            $unitId = null;
            if ($unidadNombre) {
                $unit = Unit::firstOrCreate(
                    ['name' => $unidadNombre],
                    ['symbol' => substr($unidadNombre, 0, 3)]
                );
                $unitId = $unit->id;
            }

            // 2. Almacén
            $warehouseId = null;
            if ($almacenNombre) {
                $warehouse = Warehouse::firstOrCreate(['name' => $almacenNombre]);
                $warehouseId = $warehouse->id;
            }

            // 3. Categoría
            $categoryId = null;
            if ($categoriaNombre) {
                $category = ProductCategory::firstOrCreate(['name' => $categoriaNombre]);
                $categoryId = $category->id;
            }

            // 4. Producto
            $product = Product::updateOrCreate(
                ['name' => $descripcion],
                [
                    'sell_price' => $precioVenta,
                    'buy_price' => $precioCompra,
                    'unit_id' => $unitId,
                    'product_category_id' => $categoryId,
                    'currency_id' => 1,
                ]
            );

            // 5. Inventario
            if ($warehouseId) {
                $inventory = Inventory::where('product_id', $product->id)
                    ->where('warehouse_id', $warehouseId)
                    ->first();

                if ($inventory) {
                    $inventory->update([
                        'amount' => $existencia
                    ]);
                } else {
                    Inventory::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouseId,
                        'amount' => $existencia,
                        'stock_min' => 0,
                    ]);
                }
            }
        }
    }
}
