<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Flete #{{ $flete->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1e293b;
            margin: 0;
            padding: 24px;
        }

        h1 {
            margin: 0 0 16px;
            font-size: 20px;
        }

        .meta {
            margin-bottom: 16px;
        }

        .meta p {
            margin: 0 0 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th, td {
            border: 1px solid #94a3b8;
            padding: 8px 6px;
            text-align: left;
        }

        th {
            background: #e2e8f0;
            font-size: 10px;
            text-transform: uppercase;
        }

        .total {
            margin-top: 8px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }
        .total-cancelado {
            color: red;
        }

        .total:first-of-type {
            margin-top: 16px;
        }
    </style>
</head>
<body>
    <h1>Registro de Flete #{{ $flete->id }}</h1>

    <div class="meta">
        <p><strong>Cliente:</strong> {{ $flete->cliente?->nombre ?? 'Sin cliente' }}</p>
        <p><strong>Fecha:</strong> {{ $flete->fecha?->format('d/m/Y') ?? '—' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Servicio</th>
                <th>Flete</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($flete->items as $item)
                <tr>
                    <td>{{ $item->fecha?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $item->descripcion ?: '—' }}</td>
                    <td>S/ {{ number_format((float) $item->servicio, 2) }}</td>
                    <td>S/ {{ number_format((float) $item->flete, 2) }}</td>
                    <td>S/ {{ number_format((float) $item->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Sin productos</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="total">total general: S/ {{ number_format((float) $flete->total_general, 2) }}</p>

    @foreach ($flete->pagos as $pago)
        <p class="total-cancelado total">{{ strtolower($pago->descripcion) }}: S/ -{{ number_format((float) $pago->monto, 2) }}</p>
    @endforeach

    <p class="total">total a pagar: S/ {{ number_format($flete->faltaPagar(), 2) }}</p>
</body>
</html>
