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
        .text-center {
            text-align: center;
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
            <td width="20%">
                <img src="{{ public_path('images/logo.png') }}" style="width: 80px; height: auto;">
            </td>
            <td width="40%">
                <div class="title"></div>
                <div><strong>Nro:</strong> {{ $record->id }}</div>
                <div><strong>Fecha:</strong> {{ $record->date->format('d/m/Y') }}</div>
            </td>
            <td width="40%" class="text-right">
                <div><strong>Paciente:</strong>
                    @if($record->invoiceable_type === 'App\Models\Patient')
                        {{ $record->invoiceable->last_name }}, {{ $record->invoiceable->first_name }}
                        <div><strong>Teléfono:</strong> {{ $record->invoiceable->phone ?? 'N/A' }}</div>
                    @else
                        {{ $record->invoiceable->name ?? 'N/A' }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    @foreach($record->details as $detail)
        <div class="exam-header text-center">
            {{ $detail->content->name ?? 'Examen' }}
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
                        <td>{!! $result->referenceValue->unit->name ?? 'N/A' !!}</td>
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

</body>
</html>
