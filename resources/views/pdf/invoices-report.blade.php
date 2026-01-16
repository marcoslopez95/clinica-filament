<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Facturas</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .text-right {
            text-align: right;
        }
        .detailed-section {
            background-color: #f9f9f9;
            padding: 10px;
        }
        .inner-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 9px;
        }
        .inner-table th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Facturas {{ ($is_detailed ?? false) ? '(Detallado)' : '' }}</h1>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nro</th>
                <th>Cliente/Paciente</th>
                <th>DNI/RIF</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Moneda</th>
                <th class="text-right">Total</th>
                <th class="text-right">Saldo</th>
                <th>Venc.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <tr style="{{ ($is_detailed ?? false) ? 'background-color: #eee; font-weight: bold;' : '' }}">
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->full_name }}</td>
                    <td>{{ $invoice->dni }}</td>
                    <td>{{ $invoice->date->format('d/m/Y') }}</td>
                    <td>{{ $invoice->invoice_type?->getName() }}</td>
                    <td>{{ $invoice->status?->getName() }}</td>
                    <td>{{ $invoice->currency?->code ?? 'USD' }}</td>
                    <td class="text-right">{{ number_format((float)$invoice->total, 2) }}</td>
                    <td class="text-right">{{ number_format((float)$invoice->balance, 2) }}</td>
                    <td>{{ $invoice->is_expired ? 'Sí' : 'No' }}</td>
                </tr>
                @if($is_detailed ?? false)
                    <tr>
                        <td colspan="10" class="detailed-section">
                            <div style="margin-bottom: 5px;"><strong>Detalles de Productos/Servicios:</strong></div>
                            <table class="inner-table">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th class="text-right">Cantidad</th>
                                        <th class="text-right">Precio</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->details as $detail)
                                        <tr>
                                            <td>
                                                @php
                                                    $contentType = match($detail->content_type) {
                                                        'App\Models\Product' => 'Producto',
                                                        'App\Models\Service' => 'Servicio',
                                                        'App\Models\Exam' => 'Examen',
                                                        'App\Models\Room' => 'Habitación',
                                                        default => 'Otro'
                                                    };
                                                @endphp
                                                {{ $contentType }}
                                            </td>
                                            <td>{{ $detail->content->name ?? 'N/A' }}</td>
                                            <td class="text-right">{{ $detail->quantity }}</td>
                                            <td class="text-right">{{ number_format($detail->price, 2) }}</td>
                                            <td class="text-right">{{ number_format($detail->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div style="margin-bottom: 5px;"><strong>Pagos:</strong></div>
                            <table class="inner-table">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Método</th>
                                        <th>Moneda</th>
                                        <th class="text-right">Monto</th>
                                        <th class="text-right">Tasa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoice->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $payment->paymentMethod->name ?? 'N/A' }}</td>
                                            <td>{{ $payment->currency->code ?? 'N/A' }}</td>
                                            <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
                                            <td class="text-right">{{ number_format($payment->exchange, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" style="text-align: center;">Sin pagos registrados</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
