@php
    $estadoServicioClasses = [
        'PENDIENTE' => 'border-amber-200 bg-amber-50 text-amber-700',
        'ENTREGADO' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
    ];
    $estadoPagoClasses = [
        'PENDIENTE' => 'border-rose-200 bg-rose-50 text-rose-700',
        'PARCIAL' => 'border-orange-200 bg-orange-50 text-orange-700',
        'PAGADO' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
    ];
@endphp

<x-app-layout title="Servicios">
    <x-slot name="header">
        <x-header title="Servicios" subtitle="Controla cliente, agencia, costos, estados y entrega desde una sola lista compacta." />
    </x-slot>

    <section class="space-y-4">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
            <form id="servicios-filters-form" method="GET" action="{{ route('servicios.index') }}" class="mt-2 w-full">
                <div class="flex items-center gap-3 w-full">
                    <a href="{{ route('servicios.create') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-200">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Agregar</span>
                    </a>

                    <div class="relative flex-1">
                        <input
                            type="search"
                            name="q"
                            value="{{ $search ?? '' }}"
                            placeholder="Buscar por cliente, agencia, descripción o id"
                            class="w-full rounded-md border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-sky-200"
                        >
                        <div class="absolute left-3 top-2.5 text-slate-400">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35" />
                                <circle cx="11" cy="11" r="6" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="hidden rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 sm:inline-flex">{{ $servicios->total() }} registros</span>
                        <button id="clear-filters" type="button" title="Limpiar filtros" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-600">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 6v12a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 11v6m4-6v6" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="mt-3 grid gap-2 sm:grid-cols-3">
                    <div>
                        <label for="tipo_servicio" class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Tipo</label>
                        <select id="tipo_servicio" name="tipo_servicio" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                            <option value="">Todos</option>
                            <option value="ENVIO" @selected(($tipo_servicio ?? '') === 'ENVIO')>ENVIO</option>
                            <option value="RECOJO" @selected(($tipo_servicio ?? '') === 'RECOJO')>RECOJO</option>
                        </select>
                    </div>

                    <div>
                        <label for="estado_pago" class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Estado pago</label>
                        <select id="estado_pago" name="estado_pago" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                            <option value="">Todos</option>
                            <option value="PENDIENTE" @selected(($estado_pago ?? '') === 'PENDIENTE')>PENDIENTE</option>
                            <option value="PARCIAL" @selected(($estado_pago ?? '') === 'PARCIAL')>PARCIAL</option>
                            <option value="PAGADO" @selected(($estado_pago ?? '') === 'PAGADO')>PAGADO</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Rango fecha</label>
                        <div class="flex items-center gap-2">
                            <input type="date" name="start_date" value="{{ $start_date ?? \Illuminate\Support\Carbon::now()->startOfMonth()->format('Y-m-d') }}" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                            <input type="date" name="end_date" value="{{ $end_date ?? \Illuminate\Support\Carbon::now()->endOfMonth()->format('Y-m-d') }}" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        @if ($servicios->isEmpty())
            <div class="rounded-[1.75rem] border border-dashed border-slate-300 bg-white p-8 text-center shadow-lg shadow-sky-100/70">
                <p class="text-lg font-semibold text-slate-800">No hay servicios para mostrar</p>
                <p class="mt-2 text-sm text-slate-500">Ajusta los filtros o registra un nuevo servicio.</p>
            </div>
        @else
            <div class="space-y-3 md:hidden">
                @foreach ($servicios as $servicio)
                    <article class="rounded-[1.5rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ $servicio->tipo_servicio }}</p>
                                <h3 class="mt-1 text-sm font-semibold text-slate-800">{{ $servicio->cliente?->nombre ?? 'Sin cliente' }}</h3>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $servicio->agencia?->nombre ?? 'Sin agencia' }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('servicios.edit', $servicio) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 sm:h-9 sm:w-9" aria-label="Editar servicio {{ $servicio->id }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931ZM18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </a>
                                <form action="{{ route('servicios.destroy', $servicio) }}" method="POST" class="delete-form" data-nombre="servicio #{{ $servicio->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" aria-label="Eliminar servicio {{ $servicio->id }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.021.166m-1.021-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21H8.084a2.25 2.25 0 0 1-2.245-1.327L4.772 5.79m14.456 0A48.108 48.108 0 0 0 3.75 5.79m5.25 0V4.875C9 3.839 9.84 3 10.875 3h2.25C14.16 3 15 3.84 15 4.875V5.79" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-slate-600">
                            <div class="rounded-md bg-slate-50 px-2 py-2">
                                <span class="block text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Bultos</span>
                                <p class="mt-1 font-semibold text-slate-800">{{ $servicio->cantidad_bultos }}</p>
                            </div>
                            <div class="rounded-md bg-slate-50 px-2 py-2">
                                <span class="block text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Total</span>
                                <p class="mt-1 font-semibold text-slate-800">S/ {{ number_format((float) $servicio->total, 2, '.', ',') }}</p>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2 text-[11px] font-semibold">
                            <span class="inline-flex rounded-full border px-2 py-1 {{ $estadoServicioClasses[$servicio->estado_servicio] ?? 'border-slate-200 bg-slate-50 text-slate-700' }}">{{ $servicio->estado_servicio }}</span>
                            <span data-estado-pago-badge="{{ $servicio->id }}" class="inline-flex rounded-full border px-2 py-1 {{ $estadoPagoClasses[$servicio->estado_pago] ?? 'border-slate-200 bg-slate-50 text-slate-700' }}">{{ $servicio->estado_pago }}</span>
                        </div>

                        <div class="mt-3 grid gap-2 text-sm text-slate-600">
                            <div>
                                <span class="block text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Fecha</span>
                                <p class="mt-1 text-xs">{{ $servicio->fecha_servicio?->format('d/m/Y H:i') ?? 'Sin fecha' }}</p>
                            </div>
                            <div>
                                <span class="block text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Descripción</span>
                                <p class="mt-1 text-xs whitespace-pre-line">{{ $servicio->descripcion ? \Illuminate\Support\Str::limit($servicio->descripcion, 100) : 'Sin descripción' }}</p>
                            </div>
                            <button
                                type="button"
                                data-open-pagos-modal="pagos-modal-{{ $servicio->id }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2.5 text-xs font-semibold text-sky-700 hover:bg-sky-100"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 15h.01M11 15h2m5-10H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z" />
                                </svg>
                                <span>Pagos del servicio</span>
                                <span class="rounded-full bg-white px-2 py-0.5 text-[11px] text-sky-700">{{ $servicio->pagos->count() }}</span>
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="hidden overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-lg shadow-sky-100/70 md:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.18em] text-slate-500">
                            <tr>
                                <th class="px-3 py-3">Cliente / Agencia</th>
                                <th class="px-3 py-3">Servicio</th>
                                <th class="px-3 py-3">Estados</th>
                                <th class="px-3 py-3">Costos</th>
                                <th class="px-3 py-3">Fecha</th>
                                <th class="px-3 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($servicios as $servicio)
                                <tr>
                                    <td class="px-3 py-3 align-top">
                                        <p class="font-medium text-slate-800">{{ $servicio->cliente?->nombre ?? 'Sin cliente' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $servicio->agencia?->nombre ?? 'Sin agencia' }}</p>
                                    </td>
                                    <td class="px-3 py-3 align-top">
                                        <p class="font-medium text-slate-800">{{ $servicio->tipo_servicio }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $servicio->cantidad_bultos }} bultos</p>
                                        <p class="mt-1 max-w-xs text-xs text-slate-500">{{ $servicio->descripcion ? \Illuminate\Support\Str::limit($servicio->descripcion, 120) : 'Sin descripción' }}</p>
                                        <button
                                            type="button"
                                            data-open-pagos-modal="pagos-modal-{{ $servicio->id }}"
                                            class="mt-2 inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-100"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 15h.01M11 15h2m5-10H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z" />
                                            </svg>
                                            <span>Pagos</span>
                                            <span class="rounded-full bg-white px-2 py-0.5 text-[11px] text-sky-700">{{ $servicio->pagos->count() }}</span>
                                        </button>
                                    </td>
                                    <td class="px-3 py-3 align-top">
                                        <div class="flex flex-col gap-2 text-[11px] font-semibold">
                                            <span class="inline-flex w-fit rounded-full border px-2 py-1 {{ $estadoServicioClasses[$servicio->estado_servicio] ?? 'border-slate-200 bg-slate-50 text-slate-700' }}">{{ $servicio->estado_servicio }}</span>
                                            <span data-estado-pago-badge="{{ $servicio->id }}" class="inline-flex w-fit rounded-full border px-2 py-1 {{ $estadoPagoClasses[$servicio->estado_pago] ?? 'border-slate-200 bg-slate-50 text-slate-700' }}">{{ $servicio->estado_pago }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 align-top text-xs text-slate-600">
                                        <p>Transporte: S/ {{ number_format((float) $servicio->costo_transporte, 2, '.', ',') }}</p>
                                        <p class="mt-1">Flete: S/ {{ number_format((float) $servicio->costo_flete, 2, '.', ',') }}</p>
                                        <p class="mt-1 font-semibold text-slate-800">Total: S/ {{ number_format((float) $servicio->total, 2, '.', ',') }}</p>
                                    </td>
                                    <td class="px-3 py-3 align-top text-xs text-slate-600">
                                        <p>{{ $servicio->fecha_servicio?->format('d/m/Y H:i') ?? 'Sin fecha' }}</p>
                                        <p class="mt-1">Entrega: {{ $servicio->fecha_entrega?->format('d/m/Y H:i') ?? 'Pendiente' }}</p>
                                    </td>
                                    <td class="px-3 py-3 align-top">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('servicios.edit', $servicio) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100" aria-label="Editar servicio {{ $servicio->id }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931ZM18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('servicios.destroy', $servicio) }}" method="POST" class="delete-form" data-nombre="servicio #{{ $servicio->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" aria-label="Eliminar servicio {{ $servicio->id }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.021.166m-1.021-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21H8.084a2.25 2.25 0 0 1-2.245-1.327L4.772 5.79m14.456 0A48.108 48.108 0 0 0 3.75 5.79m5.25 0V4.875C9 3.839 9.84 3 10.875 3h2.25C14.16 3 15 3.84 15 4.875V5.79" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $servicios->links() }}
            </div>

            @foreach ($servicios as $servicio)
                @php
                    $totalPagado = (float) $servicio->pagos->sum('monto');
                    $saldoPendiente = max((float) $servicio->total - $totalPagado, 0);
                @endphp
                <div id="pagos-modal-{{ $servicio->id }}" class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-900/40 p-3 sm:items-center">
                    <div class="w-full max-w-2xl rounded-[1.5rem] bg-white p-4 shadow-2xl sm:p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Servicio #{{ $servicio->id }}</p>
                                <h3 class="mt-1 text-base font-bold text-slate-800">{{ $servicio->cliente?->nombre ?? 'Sin cliente' }}</h3>
                                <p class="mt-1 text-xs text-slate-500">{{ $servicio->agencia?->nombre ?? 'Sin agencia' }} · Total S/ {{ number_format((float) $servicio->total, 2, '.', ',') }}</p>
                            </div>
                            <button type="button" class="rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700" data-close-pagos-modal="pagos-modal-{{ $servicio->id }}">✕</button>
                        </div>

                        <div class="mt-4 grid gap-3 lg:grid-cols-[0.95fr_1.05fr]">
                            <form action="{{ route('servicios.pagos-inline', $servicio) }}" method="POST" class="rounded-2xl border border-slate-200 bg-slate-50 p-3" data-pago-form data-servicio-id="{{ $servicio->id }}">
                                @csrf
                                <h4 class="text-sm font-semibold text-slate-800">Registrar pago</h4>
                                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Fecha</label>
                                        <input type="datetime-local" name="fecha_pago" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Monto</label>
                                        <input type="number" name="monto" min="0.01" step="0.01" placeholder="0.00" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <label class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Método</label>
                                    <select name="metodo_pago" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                                        <option value="EFECTIVO">EFECTIVO</option>
                                        <option value="YAPE">YAPE</option>
                                        <option value="PLIN">PLIN</option>
                                        <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                    </select>
                                </div>
                                <div class="mt-2">
                                    <label class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Observación</label>
                                    <textarea name="observacion" rows="2" class="w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700" placeholder="Detalle opcional"></textarea>
                                </div>
                                <p class="mt-2 hidden text-xs text-rose-500" data-pago-error></p>
                                <div class="mt-3 flex items-center justify-between gap-2 text-xs text-slate-500">
                                    <div>
                                        <p>Pagado: <span class="font-semibold text-slate-800" data-total-pagado>S/ {{ number_format($totalPagado, 2, '.', ',') }}</span></p>
                                        <p>Saldo: <span class="font-semibold text-slate-800" data-saldo-pendiente>S/ {{ number_format($saldoPendiente, 2, '.', ',') }}</span></p>
                                    </div>
                                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white">Guardar pago</button>
                                </div>
                            </form>

                            <div class="rounded-2xl border border-slate-200 bg-white p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-sm font-semibold text-slate-800">Pagos registrados</h4>
                                    <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-semibold text-slate-600" data-estado-pago-badge-modal>{{ $servicio->estado_pago }}</span>
                                </div>
                                <div class="mt-3 space-y-2" data-pagos-list>
                                    @forelse ($servicio->pagos as $pago)
                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                                            <div class="flex items-center justify-between gap-2">
                                                <div>
                                                    <p class="font-semibold text-slate-800">S/ {{ number_format((float) $pago->monto, 2, '.', ',') }}</p>
                                                    <p class="mt-0.5 text-[11px] text-slate-500">{{ $pago->metodo_pago }} · {{ $pago->fecha_pago?->format('d/m/Y H:i') }}</p>
                                                </div>
                                            </div>
                                            @if ($pago->observacion)
                                                <p class="mt-1 text-[11px] text-slate-500">{{ $pago->observacion }}</p>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="rounded-xl border border-dashed border-slate-200 px-3 py-4 text-center text-xs text-slate-500" data-pagos-empty>Aún no hay pagos registrados.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function(){
            // Búsqueda automática y filtros
            (function(){
                const form = document.getElementById('servicios-filters-form');
                if (!form) return;
                const input = form.querySelector('input[name="q"]');
                if (input) {
                    let timer = null;
                    input.addEventListener('input', function(){
                        if (timer) clearTimeout(timer);
                        timer = setTimeout(function(){
                            form.submit();
                        }, 350);
                    });
                }

                // submit on change for selects and date inputs
                form.querySelectorAll('select, input[type="date"]').forEach(function(el){
                    el.addEventListener('change', function(){
                        form.submit();
                    });
                });

                // clear filters button
                const clearBtn = document.getElementById('clear-filters');
                if (clearBtn) {
                    clearBtn.addEventListener('click', function(){
                        window.location = "{{ route('servicios.index') }}";
                    });
                }
            })();

            const estadoPagoClasses = {
                PENDIENTE: 'border-rose-200 bg-rose-50 text-rose-700',
                PARCIAL: 'border-orange-200 bg-orange-50 text-orange-700',
                PAGADO: 'border-emerald-200 bg-emerald-50 text-emerald-700',
            };

            function openPagosModal(id) {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closePagosModal(id) {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                const error = modal.querySelector('[data-pago-error]');
                if (error) {
                    error.textContent = '';
                    error.classList.add('hidden');
                }
            }

            document.querySelectorAll('[data-open-pagos-modal]').forEach(function(button){
                button.addEventListener('click', function(){
                    openPagosModal(this.getAttribute('data-open-pagos-modal'));
                });
            });

            document.querySelectorAll('[data-close-pagos-modal]').forEach(function(button){
                button.addEventListener('click', function(){
                    closePagosModal(this.getAttribute('data-close-pagos-modal'));
                });
            });

            document.querySelectorAll('[id^="pagos-modal-"]').forEach(function(modal){
                modal.addEventListener('click', function(event){
                    if (event.target === modal) {
                        closePagosModal(modal.id);
                    }
                });
            });

            document.querySelectorAll('[data-pago-form]').forEach(function(form){
                form.addEventListener('submit', async function(event){
                    event.preventDefault();

                    const error = form.querySelector('[data-pago-error]');
                    if (error) {
                        error.textContent = '';
                        error.classList.add('hidden');
                    }

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            body: new FormData(form),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            const message = data.message || Object.values(data.errors || {}).flat().join(' ') || 'No se pudo registrar el pago.';
                            throw new Error(message);
                        }

                        const list = form.closest('[id^="pagos-modal-"]')?.querySelector('[data-pagos-list]');
                        const empty = form.closest('[id^="pagos-modal-"]')?.querySelector('[data-pagos-empty]');
                        if (empty) {
                            empty.remove();
                        }

                        if (list) {
                            const item = document.createElement('div');
                            item.className = 'rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600';
                            item.innerHTML = `
                                <div class="flex items-center justify-between gap-2">
                                    <div>
                                        <p class="font-semibold text-slate-800">S/ ${data.pago.monto}</p>
                                        <p class="mt-0.5 text-[11px] text-slate-500">${data.pago.metodo_pago} · ${data.pago.fecha_pago}</p>
                                    </div>
                                </div>
                                ${data.pago.observacion ? `<p class="mt-1 text-[11px] text-slate-500">${data.pago.observacion}</p>` : ''}
                            `;
                            list.prepend(item);
                        }

                        const totalPagado = form.querySelector('[data-total-pagado]');
                        const saldoPendiente = form.querySelector('[data-saldo-pendiente]');
                        if (totalPagado) totalPagado.textContent = `S/ ${data.total_pagado}`;
                        if (saldoPendiente) saldoPendiente.textContent = `S/ ${data.saldo}`;

                        form.closest('[id^="pagos-modal-"]')?.querySelectorAll('[data-estado-pago-badge-modal], [data-estado-pago-badge]').forEach(function(badge){
                            badge.textContent = data.estado_pago;
                            badge.className = badge.className.replace(/border-\S+|bg-\S+|text-\S+/g, '').trim();
                        });

                        document.querySelectorAll(`[data-estado-pago-badge="${form.getAttribute('data-servicio-id')}"]`).forEach(function(badge){
                            badge.textContent = data.estado_pago;
                            badge.className = `inline-flex rounded-full border px-2 py-1 ${estadoPagoClasses[data.estado_pago] || 'border-slate-200 bg-slate-50 text-slate-700'}`;
                        });

                        const modalBadge = form.closest('[id^="pagos-modal-"]')?.querySelector('[data-estado-pago-badge-modal]');
                        if (modalBadge) {
                            modalBadge.textContent = data.estado_pago;
                        }

                        form.reset();
                        const fechaInput = form.querySelector('input[name="fecha_pago"]');
                        if (fechaInput) {
                            fechaInput.value = "{{ now()->format('Y-m-d\TH:i') }}";
                        }
                    } catch (err) {
                        if (error) {
                            error.textContent = err.message;
                            error.classList.remove('hidden');
                        }
                    }
                });
            });

            document.querySelectorAll('.delete-form').forEach(function(form){
                form.addEventListener('submit', function(e){
                    e.preventDefault();
                    const nombre = form.getAttribute('data-nombre') || 'este servicio';
                    Swal.fire({
                        title: '¿Eliminar?',
                        text: `Se eliminará ${nombre}. Esta acción no se puede deshacer.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        })();
    </script>
</x-app-layout>