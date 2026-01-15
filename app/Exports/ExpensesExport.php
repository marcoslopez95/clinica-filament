<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $expenses;

    public function __construct($expenses)
    {
        $this->expenses = $expenses;
    }

    public function collection()
    {
        return $this->expenses;
    }

    public function headings(): array
    {
        return [
            'Descripción',
            'Precio',
            'Moneda',
            'Tasa de Cambio',
            'Proveedor',
            'Categoría',
            'Fecha',
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->description,
            $expense->price,
            $expense->currency?->name ?? 'N/A',
            $expense->exchange,
            $expense->supplier?->name ?? 'N/A',
            $expense->category?->name ?? 'N/A',
            $expense->created_at->format('d/m/Y'),
        ];
    }
}
