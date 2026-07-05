@php
    $estadoPagoClasses = [
        'PENDIENTE' => 'border-rose-200 bg-rose-50 text-rose-700',
        'PARCIAL' => 'border-orange-200 bg-orange-50 text-orange-700',
        'PAGADO' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
    ];
@endphp

<x-app-layout title="Pagos del servicio">
    <x-slot name="header">
        <x-header title="Pagos del servicio" subtitle="Registra abonos y consulta el historial de cobros del servicio." />
    </x-slot>

    <section class="space-y-4">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('servicios.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver a servicios
            </a>
            <a href="{{ route('servicios.edit', $servicio) }}" class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-100">
                Editar servicio
            </a>
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">Servicio #{{ $servicio->id }}</p>
                    <h2 class="mt-0.5 truncate text-base font-bold text-slate-800 sm:text-lg">{{ $servicio->cliente?->nombre ?? 'Sin cliente' }}</h2>
                    <p class="mt-0.5 truncate text-xs text-slate-500 sm:text-sm">{{ $servicio->agencia?->nombre ?? 'Sin agencia' }} · {{ $servicio->tipo_servicio }}</p>
                </div>
                <span class="shrink-0 rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $estadoPagoClasses[$servicio->estado_pago] ?? 'border-slate-200 bg-slate-50 text-slate-700' }}">
                    {{ $servicio->estado_pago }}
                </span>
            </div>

            <div class="mt-3 grid grid-cols-3 gap-2">
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-2 py-2 text-center sm:px-3 sm:py-2.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Total</p>
                    <p class="mt-0.5 text-sm font-bold text-slate-800 sm:text-base">S/ {{ number_format((float) $servicio->total, 2, '.', ',') }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-2 py-2 text-center sm:px-3 sm:py-2.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Pagado</p>
                    <p class="mt-0.5 text-sm font-bold text-emerald-700 sm:text-base">S/ {{ number_format($totalPagado, 2, '.', ',') }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-2 py-2 text-center sm:px-3 sm:py-2.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Saldo</p>
                    <p class="mt-0.5 text-sm font-bold text-rose-700 sm:text-base">S/ {{ number_format($saldoPendiente, 2, '.', ',') }}</p>
                </div>
            </div>
        </div>

        <div class="grid gap-3 lg:grid-cols-[0.95fr_1.05fr] lg:gap-4">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
                <h3 class="text-sm font-bold text-slate-800 sm:text-base">Registrar pago</h3>

                <form action="{{ route('servicios.pagos.store', $servicio) }}" method="POST" class="mt-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="fecha_pago" class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Fecha</label>
                            <input
                                id="fecha_pago"
                                type="datetime-local"
                                name="fecha_pago"
                                value="{{ old('fecha_pago', now()->format('Y-m-d\TH:i')) }}"
                                class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-sm text-slate-700 @error('fecha_pago') border-rose-300 @enderror"
                            >
                            @error('fecha_pago')
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
                                step="0.01"
                                value="{{ old('monto') }}"
                                placeholder="0.00"
                                class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-sm text-slate-700 @error('monto') border-rose-300 @enderror"
                            >
                            @error('monto')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-2">
                        <span class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Método</span>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach (['EFECTIVO', 'YAPE', 'PLIN', 'TRANSFERENCIA'] as $metodo)
                                <label data-radio-card class="flex cursor-pointer items-center gap-2 rounded-md border px-2 py-2 text-xs font-semibold {{ old('metodo_pago', 'EFECTIVO') === $metodo ? 'border-sky-500 bg-sky-50 text-sky-700' : 'border-slate-200 bg-white text-slate-600' }}">
                                    <input type="radio" name="metodo_pago" value="{{ $metodo }}" class="h-3.5 w-3.5 border-slate-300 text-sky-600 focus:ring-sky-500" @checked(old('metodo_pago', 'EFECTIVO') === $metodo)>
                                    <span>{{ $metodo }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('metodo_pago')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-2">
                        <label for="observacion" class="mb-1 block text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Observación</label>
                        <textarea
                            id="observacion"
                            name="observacion"
                            rows="2"
                            class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-sm text-slate-700 @error('observacion') border-rose-300 @enderror"
                            placeholder="Detalle opcional"
                        >{{ old('observacion') }}</textarea>
                        @error('observacion')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-200 hover:bg-sky-700 sm:w-auto sm:py-2.5">
                            Guardar pago
                        </button>
                    </div>
                </form>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-800 sm:text-base">Pagos registrados</h3>
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[11px] font-semibold text-slate-600">{{ $servicio->pagos->count() }}</span>
                </div>

                <div class="mt-3 space-y-2">
                    @forelse ($servicio->pagos as $pago)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <p class="font-semibold text-slate-800">S/ {{ number_format((float) $pago->monto, 2, '.', ',') }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $pago->metodo_pago }} · {{ $pago->fecha_pago?->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            @if ($pago->observacion)
                                <p class="mt-1 text-xs text-slate-500">{{ $pago->observacion }}</p>
                            @endif
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
            function syncRadioCards(name) {
                document.querySelectorAll(`input[name="${name}"]`).forEach((input) => {
                    const card = input.closest('[data-radio-card]');
                    if (!card) return;

                    card.classList.toggle('border-sky-500', input.checked);
                    card.classList.toggle('bg-sky-50', input.checked);
                    card.classList.toggle('text-sky-700', input.checked);
                    card.classList.toggle('border-slate-200', !input.checked);
                    card.classList.toggle('bg-white', !input.checked);
                    card.classList.toggle('text-slate-600', !input.checked);
                });
            }

            document.querySelectorAll('input[name="metodo_pago"]').forEach((input) => {
                input.addEventListener('change', function () {
                    syncRadioCards('metodo_pago');
                });
            });
        })();
    </script>
</x-app-layout>
