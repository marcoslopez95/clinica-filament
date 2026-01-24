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
            vertical-align: middle;
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
                        <div><strong>Cédula:</strong> {{ $record->invoiceable->full_document ?? 'N/A' }}</div>
                        <div><strong>Edad:</strong> {{ $record->invoiceable->age ?? 'N/A' }}</div>
                    @else
                        {{ $record->invoiceable->name ?? 'N/A' }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    @php
        $groupedDetails = $record->details->groupBy(function($detail) {
            if ($detail->content_type === 'App\Models\Exam' && $detail->content && $detail->content->examCategory) {
                return $detail->content->examCategory->name;
            }
            return 'Sin Categoría';
        });
    @endphp

    @foreach($groupedDetails as $categoryName => $details)
        <div class="exam-header" align="center">
            {{ $categoryName }}
        </div>

        <table class="results-table">
            <thead>
                <tr>
                    <th width="35%">Parámetro</th>
                    <th width="20%">Resultado</th>
                    <th width="20%">Unidad</th>
                    <th width="25%">Rango/Valor Ref.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $detail)
                    @php
                        $showExamTitle = true;
                        if ($detail->content_type === 'App\Models\Exam' && $detail->content && $detail->content->examCategory) {
                            $showExamTitle = $detail->content->examCategory->show_exam_title;
                        }
                    @endphp

                    @if($showExamTitle)
                        <tr>
                            <td colspan="4" style="background-color: #f9f9f9; font-weight: bold;">
                                Examen: {{ $detail->content->name ?? 'N/A' }}
                            </td>
                        </tr>
                    @endif

                    @forelse($detail->referenceResults as $result)
                        <tr>
                            <td align="left">{{ $result->referenceValue->name ?? 'N/A' }}</td>
                            <td align="center">{{ $result->result }}</td>
                            <td align="center">{!! $result->referenceValue->unit->name ?? 'N/A' !!}</td>
                            <td align="center">
                                @if($result->referenceValue->min_value && $result->referenceValue->max_value)
                                    {{ $result->referenceValue->min_value }} - {{ $result->referenceValue->max_value }}
                                @else
                                    {{ $result->referenceValue->min_value ?: $result->referenceValue->max_value ?: 'N/A' }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No hay resultados cargados</td>
                        </tr>
                    @endforelse
                @endforeach
            </tbody>
        </table>
    @endforeach

</body>
</html>
