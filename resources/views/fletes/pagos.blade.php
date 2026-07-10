<x-app-layout title="Pagos del flete">
    <x-slot name="header">
        <x-header title="Pagos del flete" subtitle="Registra abonos parciales y consulta lo que falta por pagar." />
    </x-slot>

    <section class="space-y-4">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('fletes.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver a fletes
            </a>
            <a href="{{ route('fletes.edit', $flete) }}" class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-100">
                Editar flete
            </a>
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Flete #{{ $flete->id }}</p>
                <h2 class="mt-0.5 text-base font-bold text-slate-800 sm:text-lg">{{ $flete->cliente?->nombre ?? 'Sin cliente' }}</h2>
                <p class="mt-0.5 text-xs text-slate-500 sm:text-sm">{{ $flete->fecha?->format('d/m/Y') ?? '—' }}</p>
            </div>

            <div class="mt-3 grid grid-cols-3 gap-2">
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-2 py-2 text-center sm:px-3 sm:py-2.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Total</p>
                    <p class="mt-0.5 text-sm font-bold text-slate-800 sm:text-base">S/ {{ number_format((float) $flete->total_general, 2) }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-2 py-2 text-center sm:px-3 sm:py-2.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Pagado</p>
                    <p class="mt-0.5 text-sm font-bold text-emerald-700 sm:text-base">S/ {{ number_format($totalPagado, 2) }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-2 py-2 text-center sm:px-3 sm:py-2.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Falta</p>
                    <p class="mt-0.5 text-sm font-bold text-rose-700 sm:text-base">S/ {{ number_format($faltaPagar, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="grid gap-3 lg:grid-cols-[0.95fr_1.05fr] lg:gap-4">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
                <h3 class="text-sm font-bold text-slate-800 sm:text-base">Registrar pago</h3>

                @if ($faltaPagar <= 0)
                    <p class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-4 text-sm text-emerald-700">
                        Este flete ya está pagado completamente.
                    </p>
                @else
                    <form id="flete-pago-form" action="{{ route('fletes.pagos.store', $flete) }}" method="POST" class="mt-3 space-y-3">
                        @csrf
                        <div>
                            <label for="descripcion" class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Descripción</label>
                            <input
                                id="descripcion"
                                type="text"
                                name="descripcion"
                                value="{{ old('descripcion') }}"
                                placeholder="Ej. Abono, yapeo"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-base text-slate-700 @error('descripcion') border-rose-300 @enderror"
                            >
                            @error('descripcion')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="monto" class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Monto</label>
                            <input
                                id="monto"
                                type="number"
                                name="monto"
                                min="0.01"
                                max="{{ number_format($faltaPagar, 2, '.', '') }}"
                                step="0.01"
                                inputmode="decimal"
                                value="{{ old('monto') }}"
                                placeholder="0.00"
                                class="js-pago-monto w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-base text-slate-700 @error('monto') border-rose-300 @enderror"
                            >
                            <p class="mt-1 text-xs text-slate-500">Máximo: S/ {{ number_format($faltaPagar, 2) }}</p>
                            @error('monto')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-sky-600 px-4 py-3 text-base font-bold text-white shadow-lg shadow-sky-200 hover:bg-sky-700">
                            Guardar pago
                        </button>
                    </form>
                @endif
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-800 sm:text-base">Pagos registrados</h3>
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[11px] font-semibold text-slate-600">{{ $flete->pagos->count() }}</span>
                </div>

                <div class="mt-3 space-y-2">
                    @forelse ($flete->pagos as $pago)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-600">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-slate-800">{{ $pago->descripcion }}</p>
                                    <p class="mt-0.5 font-bold text-emerald-700">S/ {{ number_format((float) $pago->monto, 2) }}</p>
                                </div>
                                <form
                                    action="{{ route('fletes.pagos.destroy', [$flete, $pago]) }}"
                                    method="POST"
                                    class="delete-pago-form shrink-0"
                                    data-descripcion="{{ $pago->descripcion }}"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100"
                                        aria-label="Eliminar pago {{ $pago->descripcion }}"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-slate-200 px-3 py-6 text-center text-sm text-slate-500">
                            Aún no hay pagos registrados.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <script>
        (function () {
            const montoInput = document.getElementById('monto');
            const form = document.getElementById('flete-pago-form');
            const maxMonto = {{ json_encode((float) $faltaPagar) }};

            if (montoInput) {
                montoInput.addEventListener('wheel', function (event) {
                    if (document.activeElement === montoInput) {
                        event.preventDefault();
                    }
                }, { passive: false });
            }

            if (form && montoInput) {
                form.addEventListener('submit', function (event) {
                    const monto = Number.parseFloat(montoInput.value || '0');

                    if (!Number.isFinite(monto) || monto <= 0) {
                        event.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Monto inválido',
                            text: 'Ingresa un monto mayor a 0.',
                        });
                        return;
                    }

                    if (monto > maxMonto) {
                        event.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Monto inválido',
                            text: `El monto no puede exceder lo que falta por pagar (S/ ${maxMonto.toFixed(2)}).`,
                        });
                    }
                });
            }

            document.querySelectorAll('.delete-pago-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    const descripcion = form.getAttribute('data-descripcion') || 'este pago';

                    Swal.fire({
                        title: '¿Eliminar pago?',
                        text: `Se eliminará "${descripcion}". Esta acción no se puede deshacer.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
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
