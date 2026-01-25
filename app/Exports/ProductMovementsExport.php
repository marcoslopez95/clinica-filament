<?php

namespace App\Exports;

use App\Enums\InvoiceType;
use App\Models\InvoiceDetail;
use App\Models\Patient;
use App\Models\Supplier;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductMovementsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'Producto',
            'Fecha del movimiento',
            'Cantidad',
            'Almacén/Tipo',
            'Paciente/Proveedor',
            'Precio del momento',
        ];
    }

    /**
     * @param InvoiceDetail $record
     */
    public function map($record): array
    {
        $invoice = $record->invoice()->withoutGlobalScope('exclude_user_movements')->first();
        if (!$invoice) {
            return [
                $record->content->name ?? 'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
            ];
        }
        $prefix = str_contains($record->description, 'Entrada') ? '+' : '-';
        $actorName = 'N/A';
        if ($invoice->invoiceable) {
            if ($invoice->invoiceable instanceof Patient) {
                $actorName = $invoice->invoiceable->fullName;
            } elseif ($invoice->invoiceable instanceof Supplier) {
                $actorName = $invoice->invoiceable->name;
            } elseif ($invoice->invoiceable instanceof User) {
                $actorName = $invoice->invoiceable->name;
            } else {
                $actorName = $invoice->invoiceable->name ?? $invoice->invoiceable->fullName ?? 'N/A';
            }
        }

        return [
            $record->content->name ?? 'N/A',
            $invoice->date ? $invoice->date->format('d/m/Y H:i') : 'N/A',
            $prefix . $record->quantity,
            $invoice->invoice_type?->getName() ?? 'N/A',
            $actorName,
            $record->price . ' ' . ($invoice->currency->code ?? 'USD'),
        ];
    }
}
