<x-app-layout title="Fletes">
    <x-slot name="header">
        <x-header title="Fletes" subtitle="Registra y controla los fletes por cliente con detalle de productos." />
    </x-slot>

    <section class="space-y-4">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
            <div class="space-y-3">
                <a href="{{ route('fletes.create') }}" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-4 text-base font-bold text-white shadow-lg shadow-sky-200 sm:text-lg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Crear flete</span>
                </a>

                <form id="fletes-search-form" method="GET" action="{{ route('fletes.index') }}" class="flex w-full items-center gap-2">
                    @if ($desde !== '')
                        <input type="hidden" name="desde" value="{{ $desde }}">
                    @endif
                    @if ($hasta !== '')
                        <input type="hidden" name="hasta" value="{{ $hasta }}">
                    @endif
                    <div class="relative flex-1">
                        <input
                            type="search"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Buscar por fecha, nombre o id"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-3 text-base text-slate-700 placeholder:text-slate-400 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-sky-200"
                        >
                        <div class="absolute left-3 top-3.5 text-slate-400">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35" />
                                <circle cx="11" cy="11" r="6" />
                            </svg>
                        </div>
                    </div>
                    <button
                        type="button"
                        id="open-date-filter"
                        class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border {{ ($desde !== '' || $hasta !== '') ? 'border-sky-300 bg-sky-50 text-sky-700' : 'border-slate-200 bg-white text-slate-600' }} hover:bg-sky-50 hover:text-sky-700"
                        aria-label="Filtrar por fecha"
                        aria-haspopup="dialog"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h18l-7 8.5V19l-4-2v-3.5L3 5Z" />
                        </svg>
                    </button>
                    <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-slate-800 px-4 py-3 text-base font-bold text-white">
                        Buscar
                    </button>
                </form>

                @if ($search !== '' || $desde !== '' || $hasta !== '')
                    <p class="text-sm text-slate-500">
                        @if ($search !== '')
                            Resultados para: <span class="font-semibold text-slate-700">{{ $search }}</span>
                        @endif
                        @if ($desde !== '' || $hasta !== '')
                            @if ($search !== '') · @endif
                            <span class="font-semibold text-slate-700">
                                @if ($desde !== '' && $hasta !== '')
                                    {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
                                @elseif ($desde !== '')
                                    Desde {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
                                @else
                                    Hasta {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
                                @endif
                            </span>
                        @endif
                        · <a href="{{ route('fletes.index') }}" class="font-semibold text-sky-600">Limpiar</a>
                    </p>
                @endif
            </div>
        </div>

        @if ($fletes->isEmpty())
            <div class="rounded-[1.75rem] border border-dashed border-slate-300 bg-white p-8 text-center shadow-lg shadow-sky-100/70">
                <p class="text-lg font-semibold text-slate-800">No hay fletes para mostrar</p>
                <p class="mt-2 text-sm text-slate-500">Crea un nuevo flete o prueba otra búsqueda.</p>
            </div>
        @else
            <div class="space-y-3 md:hidden">
                @foreach ($fletes as $flete)
                    <article class="rounded-[1.5rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Flete #{{ $flete->id }}</p>
                                <h3 class="mt-1 text-base font-semibold text-slate-800">{{ $flete->cliente?->nombre ?? 'Sin cliente' }}</h3>
                                <p class="mt-0.5 text-sm text-slate-500">{{ $flete->fecha?->format('d/m/Y') ?? '—' }} · {{ $flete->items_count }} producto{{ $flete->items_count === 1 ? '' : 's' }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a
                                    href="{{ route('fletes.download', $flete) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-sky-200 bg-sky-50 text-sky-700 hover:bg-sky-100"
                                    aria-label="Ver PDF del flete {{ $flete->id }}"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M7.5 11.25 12 15.75m0 0 4.5-4.5M12 15.75V3" />
                                    </svg>
                                </a>
                                <a
                                    href="{{ route('fletes.pagos', $flete) }}"
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100"
                                    aria-label="Pagos del flete {{ $flete->id }}"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 15h.01M11 15h2m5-10H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z" />
                                    </svg>
                                </a>
                                <a href="{{ route('fletes.edit', $flete) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100" aria-label="Editar flete {{ $flete->id }}">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931ZM18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </a>
                                <form action="{{ route('fletes.destroy', $flete) }}" method="POST" class="delete-form" data-nombre="flete #{{ $flete->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" aria-label="Eliminar flete {{ $flete->id }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.021.166m-1.021-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21H8.084a2.25 2.25 0 0 1-2.245-1.327L4.772 5.79m14.456 0A48.108 48.108 0 0 0 3.75 5.79m5.25 0V4.875C9 3.839 9.84 3 10.875 3h2.25C14.16 3 15 3.84 15 4.875V5.79" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p class="mt-3 text-base font-bold text-slate-800">Total general: S/ {{ number_format((float) $flete->total_general, 2) }}</p>
                        @if ($flete->pagos_count > 0)
                            @php
                                $totalPagado = (float) ($flete->total_pagado ?? 0);
                                $falta = max((float) $flete->total_general - $totalPagado, 0);
                            @endphp
                            <p class="mt-1 text-sm text-slate-600">
                                Pagado: S/ {{ number_format($totalPagado, 2) }}
                                · Falta: S/ {{ number_format($falta, 2) }}
                            </p>
                        @endif
                    </article>
                @endforeach
            </div>

            <div class="hidden overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-lg shadow-sky-100/70 md:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.18em] text-slate-500">
                            <tr>
                                <th class="px-3 py-3">Cliente</th>
                                <th class="px-3 py-3">Fecha</th>
                                <th class="px-3 py-3">Productos</th>
                                <th class="px-3 py-3">Total general</th>
                                <th class="px-3 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($fletes as $flete)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-slate-800">{{ $flete->cliente?->nombre ?? 'Sin cliente' }}</td>
                                    <td class="px-3 py-3">{{ $flete->fecha?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-3 py-3">{{ $flete->items_count }}</td>
                                    <td class="px-3 py-3 font-semibold">S/ {{ number_format((float) $flete->total_general, 2) }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex justify-end gap-2">
                                            <a
                                                href="{{ route('fletes.pagos', $flete) }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100"
                                                aria-label="Pagos del flete {{ $flete->id }}"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 15h.01M11 15h2m5-10H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2Z" />
                                                </svg>
                                            </a>
                                            <a
                                                href="{{ route('fletes.download', $flete) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-sky-200 bg-sky-50 text-sky-700 hover:bg-sky-100"
                                                aria-label="Ver PDF del flete {{ $flete->id }}"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M7.5 11.25 12 15.75m0 0 4.5-4.5M12 15.75V3" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('fletes.edit', $flete) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100" aria-label="Editar flete {{ $flete->id }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931ZM18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('fletes.destroy', $flete) }}" method="POST" class="delete-form" data-nombre="flete #{{ $flete->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" aria-label="Eliminar flete {{ $flete->id }}">
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

            <div class="space-y-3">
                {{ $fletes->links('fletes.pagination') }}

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg shadow-sky-100/70">
                    <p class="border-b border-slate-100 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                        {{ ($search !== '' || $desde !== '' || $hasta !== '') ? 'Totalizados de la búsqueda' : 'Totalizados' }}
                    </p>
                    <dl class="grid grid-cols-2 sm:grid-cols-4">
                        <div class="border-b border-r border-slate-100 px-3 py-2 sm:border-b-0">
                            <dt class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">Fletes</dt>
                            <dd class="text-sm font-bold text-slate-800">{{ number_format($totales['cantidad']) }}</dd>
                        </div>
                        <div class="border-b border-slate-100 px-3 py-2 sm:border-b-0 sm:border-r">
                            <dt class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">Total</dt>
                            <dd class="text-sm font-bold text-slate-800">S/ {{ number_format($totales['total_general'], 2) }}</dd>
                        </div>
                        <div class="border-r border-slate-100 px-3 py-2">
                            <dt class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">Pagado</dt>
                            <dd class="text-sm font-bold text-emerald-700">S/ {{ number_format($totales['pagado'], 2) }}</dd>
                        </div>
                        <div class="px-3 py-2">
                            <dt class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-400">Falta</dt>
                            <dd class="text-sm font-bold text-amber-700">S/ {{ number_format($totales['falta'], 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        @endif
    </section>

    <dialog id="date-filter-modal" class="w-[min(100%-2rem,24rem)] rounded-2xl border border-slate-200 bg-white p-4 shadow-xl" aria-labelledby="date-filter-title">
        <div>
            <div class="flex items-center justify-between gap-3">
                <h2 id="date-filter-title" class="text-base font-bold text-slate-800">Filtrar por fecha</h2>
                <button type="button" data-close-date-filter class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600" aria-label="Cerrar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>

            <form method="GET" action="{{ route('fletes.index') }}" class="mt-4 space-y-3">
                @if ($search !== '')
                    <input type="hidden" name="q" value="{{ $search }}">
                @endif
                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">Desde</span>
                    <input type="date" name="desde" value="{{ $desde }}" class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-base text-slate-700 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-sky-200">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">Hasta</span>
                    <input type="date" name="hasta" value="{{ $hasta }}" class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-base text-slate-700 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-sky-200">
                </label>
                <div class="flex items-center justify-between gap-2 pt-1">
                    <a href="{{ route('fletes.index', $search !== '' ? ['q' => $search] : []) }}" class="text-sm font-semibold text-slate-500">Quitar fechas</a>
                    <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-bold text-white">Aplicar</button>
                </div>
            </form>
        </div>
    </dialog>
    <style>
        #date-filter-modal::backdrop { background: rgba(15, 23, 42, 0.45); }
    </style>

    <script>
        (function () {
            const dateModal = document.getElementById('date-filter-modal');
            const openDateFilter = document.getElementById('open-date-filter');

            openDateFilter.addEventListener('click', function () {
                dateModal.showModal();
                const firstDate = dateModal.querySelector('input[name="desde"]');
                if (firstDate) {
                    firstDate.focus();
                }
            });
            dateModal.addEventListener('click', function (event) {
                if (event.target === dateModal) {
                    dateModal.close();
                }
            });
            dateModal.querySelectorAll('[data-close-date-filter]').forEach(function (button) {
                button.addEventListener('click', function () {
                    dateModal.close();
                });
            });

            document.querySelectorAll('.delete-form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const nombre = form.getAttribute('data-nombre') || 'este flete';
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
