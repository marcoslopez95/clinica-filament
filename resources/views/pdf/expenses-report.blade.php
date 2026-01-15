<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Gastos</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Gastos</h1>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Moneda</th>
                <th>Tasa</th>
                <th>Proveedor</th>
                <th>Categoría</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
                <tr>
                    <td>{{ $expense->created_at->format('d/m/Y') }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ number_format((float)$expense->price, 2) }}</td>
                    <td>{{ $expense->currency?->name ?? 'N/A' }}</td>
                    <td>{{ $expense->exchange }}</td>
                    <td>{{ $expense->supplier?->name ?? 'N/A' }}</td>
                    <td>{{ $expense->category?->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
