<x-app-layout title="Fletes">
    <x-slot name="header">
        <x-header title="Fletes" subtitle="Registra y controla los fletes por cliente con detalle de productos." />
    </x-slot>

    <section class="space-y-4">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
            <div class="flex flex-row flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('fletes.create') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-200">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Crear flete</span>
                    </a>
                    <span class="hidden rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 sm:inline-flex">
                        {{ $fletes->total() }} registros
                    </span>
                </div>

                <form id="fletes-search-form" method="GET" action="{{ route('fletes.index') }}" class="flex w-full items-center gap-2 lg:max-w-md">
                    <div class="relative flex-1">
                        <input
                            type="search"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Buscar por cliente o id"
                            class="w-full rounded-md border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-sky-200"
                        >
                        <div class="absolute left-3 top-2.5 text-slate-400">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35" />
                                <circle cx="11" cy="11" r="6" />
                            </svg>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($fletes->isEmpty())
            <div class="rounded-[1.75rem] border border-dashed border-slate-300 bg-white p-8 text-center shadow-lg shadow-sky-100/70">
                <p class="text-lg font-semibold text-slate-800">No hay fletes para mostrar</p>
                <p class="mt-2 text-sm text-slate-500">Crea un nuevo flete para comenzar.</p>
            </div>
        @else
            <div class="space-y-3 md:hidden">
                @foreach ($fletes as $flete)
                    <article class="rounded-[1.5rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Flete #{{ $flete->id }}</p>
                                <h3 class="mt-1 text-sm font-semibold text-slate-800">{{ $flete->cliente?->nombre ?? 'Sin cliente' }}</h3>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $flete->fecha?->format('d/m/Y') ?? '—' }} · {{ $flete->items_count }} producto{{ $flete->items_count === 1 ? '' : 's' }}</p>
                            </div>
                            <div class="flex items-center gap-2">
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
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-800">Total flete: S/ {{ number_format((float) $flete->total_flete, 2) }}</p>
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
                                    <td class="px-3 py-3">{{ $flete->items_count }}</td>
                                    <td class="px-3 py-3 font-semibold">S/ {{ number_format((float) $flete->total_flete, 2) }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex justify-end gap-2">
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

            <div>
                {{ $fletes->links() }}
            </div>
        @endif
    </section>

    <script>
        (function () {
            const form = document.getElementById('fletes-search-form');
            if (form) {
                const input = form.querySelector('input[name="q"]');
                let timer = null;
                input?.addEventListener('input', function () {
                    if (timer) clearTimeout(timer);
                    timer = setTimeout(function () {
                        form.submit();
                    }, 800);
                });
            }

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
