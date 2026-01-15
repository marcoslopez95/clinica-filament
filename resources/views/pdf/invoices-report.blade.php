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
        .total-row {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Facturas</h1>
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
                <th>Total</th>
                <th>Saldo</th>
                <th>Venc.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->full_name }}</td>
                    <td>{{ $invoice->dni }}</td>
                    <td>{{ $invoice->date->format('d/m/Y') }}</td>
                    <td>{{ $invoice->invoice_type?->getName() }}</td>
                    <td>{{ $invoice->status?->getName() }}</td>
                    <td>{{ $invoice->currency?->code ?? 'USD' }}</td>
                    <td>{{ number_format((float)$invoice->total, 2) }}</td>
                    <td>{{ number_format((float)$invoice->balance, 2) }}</td>
                    <td>{{ $invoice->is_expired ? 'Sí' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
