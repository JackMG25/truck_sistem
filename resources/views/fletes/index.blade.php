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
                    <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-slate-800 px-4 py-3 text-base font-bold text-white">
                        Buscar
                    </button>
                </form>

                @if ($search !== '')
                    <p class="text-sm text-slate-500">
                        Resultados para: <span class="font-semibold text-slate-700">{{ $search }}</span>
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
                                <p class="mt-0.5 text-sm text-slate-500">{{ $flete->fecha?->format('d/m/Y') ?? '—' }} · {{ $flete->items->count() }} producto{{ $flete->items->count() === 1 ? '' : 's' }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="js-download-flete inline-flex h-10 w-10 items-center justify-center rounded-md border border-sky-200 bg-sky-50 text-sky-700 hover:bg-sky-100"
                                    data-flete-id="{{ $flete->id }}"
                                    aria-label="Descargar flete {{ $flete->id }}"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M7.5 11.25 12 15.75m0 0 4.5-4.5M12 15.75V3" />
                                    </svg>
                                </button>
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
                        <p class="mt-3 text-base font-bold text-slate-800">Total flete: S/ {{ number_format((float) $flete->total_flete, 2) }}</p>
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
                                <th class="px-3 py-3">Total flete</th>
                                <th class="px-3 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($fletes as $flete)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-slate-800">{{ $flete->cliente?->nombre ?? 'Sin cliente' }}</td>
                                    <td class="px-3 py-3">{{ $flete->fecha?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-3 py-3">{{ $flete->items->count() }}</td>
                                    <td class="px-3 py-3 font-semibold">S/ {{ number_format((float) $flete->total_flete, 2) }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                class="js-download-flete inline-flex h-8 w-8 items-center justify-center rounded-md border border-sky-200 bg-sky-50 text-sky-700 hover:bg-sky-100"
                                                data-flete-id="{{ $flete->id }}"
                                                aria-label="Descargar flete {{ $flete->id }}"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M7.5 11.25 12 15.75m0 0 4.5-4.5M12 15.75V3" />
                                                </svg>
                                            </button>
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

            <div class="sr-only" aria-hidden="true">
                @foreach ($fletes as $flete)
                    @include('fletes._capture', ['flete' => $flete])
                @endforeach
            </div>

            <div>
                {{ $fletes->links() }}
            </div>
        @endif
    </section>

    <style>
        .flete-capture-card {
            position: fixed;
            left: -9999px;
            top: 0;
            width: 720px;
            padding: 24px;
            background: #fff;
            color: #1e293b;
            font-family: Figtree, ui-sans-serif, system-ui, sans-serif;
        }

        .flete-capture-card .capture-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
        }

        .flete-capture-card .capture-header p {
            margin: 4px 0 0;
            font-size: 14px;
            color: #64748b;
        }

        .flete-capture-card .capture-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin: 20px 0 16px;
            font-size: 16px;
        }

        .flete-capture-card .capture-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .flete-capture-card .capture-table th,
        .flete-capture-card .capture-table td {
            border: 1px solid #94a3b8;
            padding: 8px 6px;
            text-align: left;
        }

        .flete-capture-card .capture-table th {
            background: #e2e8f0;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
        }

        .flete-capture-card .capture-total {
            margin-top: 16px;
            text-align: right;
            font-size: 18px;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+MMgHw4zVIl/X+pfj4X1jlm4JgGUYoK5FynX0/zgU+f3D2MvmtjY7byaWEQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        (function () {
            document.querySelectorAll('.js-download-flete').forEach(function (button) {
                button.addEventListener('click', async function () {
                    const fleteId = button.getAttribute('data-flete-id');
                    const target = document.getElementById('flete-capture-' + fleteId);

                    if (!target || typeof html2canvas === 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'No se pudo descargar',
                            text: 'Intenta de nuevo en unos segundos.',
                        });
                        return;
                    }

                    button.disabled = true;

                    try {
                        const canvas = await html2canvas(target, {
                            scale: 2,
                            backgroundColor: '#ffffff',
                            useCORS: true,
                        });

                        const link = document.createElement('a');
                        link.download = 'flete-' + fleteId + '.png';
                        link.href = canvas.toDataURL('image/png');
                        link.click();
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al generar imagen',
                            text: 'No se pudo crear la imagen del registro.',
                        });
                    } finally {
                        button.disabled = false;
                    }
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
