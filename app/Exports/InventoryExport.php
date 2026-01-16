<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $inventories;

    public function __construct($inventories)
    {
        $this->inventories = $inventories;
    }

    public function collection()
    {
        return $this->inventories;
    }

    public function headings(): array
    {
        return [
            'Producto',
            'Almacén',
            'Stock Mínimo',
            'Cantidad',
            'Lote',
            'Fecha Expiración',
            'Observaciones',
        ];
    }

    public function map($inventory): array
    {
        return [
            $inventory->product?->name ?? 'N/A',
            $inventory->warehouse?->name ?? 'N/A',
            $inventory->stock_min,
            $inventory->amount,
            $inventory->batch,
            $inventory->end_date ? $inventory->end_date->format('d/m/Y') : 'N/A',
            $inventory->observation,
        ];
    }
}
