<div id="flete-capture-{{ $flete->id }}" class="flete-capture-card" aria-hidden="true">
    <div class="capture-header">
        <h2>Registro de Flete #{{ $flete->id }}</h2>
        <p>{{ config('app.name', 'Camionero Carga') }}</p>
    </div>

    <div class="capture-meta">
        <div><strong>Cliente:</strong> {{ $flete->cliente?->nombre ?? 'Sin cliente' }}</div>
        <div><strong>Fecha:</strong> {{ $flete->fecha?->format('d/m/Y') ?? '—' }}</div>
    </div>

    <table class="capture-table">
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

    <div class="capture-total">
        <strong>Total flete: S/ {{ number_format((float) $flete->total_flete, 2) }}</strong>
    </div>
</div>
