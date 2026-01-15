<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laboratorio {{ $record->id }}</title>
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
        .exam-header {
            background-color: #f2f2f2;
            padding: 8px;
            font-weight: bold;
            border: 1px solid #ccc;
            margin-top: 10px;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .results-table td, .results-table th {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .payments-table th, .payments-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .payments-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td width="50%">
                <div class="title">INFORME DE LABORATORIO</div>
                <div><strong>Nro:</strong> {{ $record->id }}</div>
                <div><strong>Fecha:</strong> {{ $record->date->format('d/m/Y') }}</div>
            </td>
            <td width="50%" class="text-right">
                <div><strong>Paciente:</strong>
                    @if($record->invoiceable_type === 'App\Models\Patient')
                        {{ $record->invoiceable->last_name }}, {{ $record->invoiceable->first_name }}
                    @else
                        {{ $record->invoiceable->name ?? 'N/A' }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    @foreach($record->details as $detail)
        <div class="exam-header">
            <table width="100%">
                <tr>
                    <td>{{ $detail->content->name ?? 'Examen' }}</td>
                    <td class="text-right">Precio: {{ number_format($detail->price, 2) }}</td>
                </tr>
            </table>
        </div>
        <table class="results-table">
            <thead>
                <tr>
                    <th>Valor Referencial</th>
                    <th>Unidad</th>
                    <th>Rango/Valor Ref.</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($detail->referenceResults as $result)
                    <tr>
                        <td>{{ $result->referenceValue->name ?? 'N/A' }}</td>
                        <td>{{ $result->referenceValue->unit->name ?? 'N/A' }}</td>
                        <td>
                            @if($result->referenceValue->min_value && $result->referenceValue->max_value)
                                {{ $result->referenceValue->min_value }} - {{ $result->referenceValue->max_value }}
                            @else
                                {{ $result->referenceValue->min_value ?: $result->referenceValue->max_value ?: 'N/A' }}
                            @endif
                        </td>
                        <td>{{ $result->result }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No hay resultados cargados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

    <div class="text-right" style="margin-top: 10px;">
        <strong>TOTAL: {{ number_format($record->details->sum('subtotal'), 2) }}</strong>
    </div>

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
