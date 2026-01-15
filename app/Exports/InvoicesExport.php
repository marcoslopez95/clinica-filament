<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection()
    {
        return $this->invoices;
    }

    public function headings(): array
    {
        return [
            'Nro',
            'Cliente/Paciente',
            'DNI/RIF',
            'Fecha',
            'Tipo',
            'Estado',
            'Moneda',
            'Total',
            'Saldo',
            'Vencida',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->full_name,
            $invoice->dni,
            $invoice->date->format('d/m/Y'),
            $invoice->invoice_type?->getName(),
            $invoice->status?->getName(),
            $invoice->currency?->code ?? 'USD',
            $invoice->total,
            $invoice->balance,
            $invoice->is_expired ? 'Sí' : 'No',
        ];
    }
}
