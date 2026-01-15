<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante {{ $record->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
        }
        .header td {
            vertical-align: top;
        }
        .title {
            font-size: 14pt;
            font-bold: bold;
            margin-bottom: 5px;
        }
        .details-table, .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th, .details-table td, .payments-table th, .payments-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .details-table th, .payments-table th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            width: 100%;
            margin-top: 10px;
        }
        .total-section td {
            padding: 5px;
        }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td width="50%">
                <div class="title">
                    @php
                        $typeName = match($record->invoice_type) {
                            \App\Enums\InvoiceType::INVENTORY => 'ENTRADA DE INVENTARIO',
                            \App\Enums\InvoiceType::COTIZACION => 'COTIZACIÓN',
                            \App\Enums\InvoiceType::HOSPITALIZATION => 'HOSPITALIZACIÓN',
                            default => 'FACTURA'
                        };
                    @endphp
                    {{ $typeName }}
                </div>
                <div><strong>Nro:</strong> {{ $record->id }}</div>
                <div><strong>Fecha:</strong> {{ $record->date->format('d/m/Y') }}</div>
            </td>
            <td width="50%" class="text-right">
                <div><strong>Cliente/Proveedor:</strong>
                    @if($record->invoiceable_type === 'App\Models\Patient')
                        {{ $record->invoiceable->last_name }}, {{ $record->invoiceable->first_name }}
                    @else
                        {{ $record->invoiceable->name ?? 'N/A' }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Producto/Servicio</th>
                <th class="text-right">Precio</th>
                <th class="text-right">Cant.</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->details as $detail)
                <tr>
                    <td>
                        @php
                            $contentType = match($detail->content_type) {
                                'App\Models\Product' => 'Producto',
                                'App\Models\Service' => 'Servicio',
                                'App\Models\Exam' => 'Examen',
                                default => 'Otro'
                            };
                        @endphp
                        {{ $contentType }}
                    </td>
                    <td>{{ $detail->content->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($detail->price, 2) }}</td>
                    <td class="text-right">{{ $detail->quantity }}</td>
                    <td class="text-right">{{ number_format($detail->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                <td class="text-right"><strong>{{ number_format($record->details->sum('subtotal'), 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <h3>Desglose de Pagos</h3>
    <table class="payments-table">
        <thead>
            <tr>
                <th>Método de Pago</th>
                <th>Moneda</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($record->payments as $payment)
                <tr>
                    <td>{{ $payment->paymentMethod->name ?? 'N/A' }}</td>
                    <td>{{ $payment->currency->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No hay pagos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
