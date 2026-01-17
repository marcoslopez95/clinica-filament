<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithHeadings, WithMapping
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection()
    {
        return $this->products;
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Precio de Compra',
            'Precio de Venta',
            'Porcentaje de Ganancia',
            'Unidad',
            'Categoría',
            'Moneda',
        ];
    }

    public function map($product): array
    {
        $profit_margin = '0.00%';
        if ($product->buy_price > 0) {
            $profit = (($product->sell_price - $product->buy_price) / $product->buy_price) * 100;
            $profit_margin = number_format($profit, 2) . '%';
        }

        return [
            $product->name,
            $product->buy_price,
            $product->sell_price,
            $profit_margin,
            $product->unit?->name ?? 'N/A',
            $product->productCategory?->name ?? 'N/A',
            $product->currency?->name ?? 'N/A',
        ];
    }
}
