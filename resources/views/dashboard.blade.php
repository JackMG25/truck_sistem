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

<x-app-layout title="Dashboard">
    <x-slot name="header">
        <x-header title="Dashboard" subtitle="Resumen de operaciones, cobros y pendientes de tu negocio." />
    </x-slot>

    <section class="space-y-5">
        {{-- Hero financiero --}}
        <div class="relative overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-sky-600 via-indigo-600 to-violet-700 p-5 text-white shadow-xl shadow-indigo-300/40 sm:p-6">
            <div class="pointer-events-none absolute -right-8 -top-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-12 left-1/3 h-32 w-32 rounded-full bg-amber-300/20 blur-2xl"></div>

            <div class="relative">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-sky-100/90">Resumen del mes</p>
                <p class="mt-1 text-sm text-sky-100/80">{{ now()->translatedFormat('F Y') }}</p>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                        <p class="text-xs font-medium text-sky-100/90">Ingresos del mes</p>
                        <p class="mt-1 text-3xl font-extrabold tracking-tight">S/ {{ number_format($ingresosMes, 2) }}</p>
                        <p class="mt-2 inline-flex items-center gap-1 rounded-full bg-emerald-400/20 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-100">
                            Hoy: S/ {{ number_format($ingresosDia, 2) }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                        <p class="text-xs font-medium text-rose-100/90">Deuda por cobrar</p>
                        <p class="mt-1 text-3xl font-extrabold tracking-tight text-amber-100">S/ {{ number_format($totalDeuda, 2) }}</p>
                        <p class="mt-2 inline-flex items-center gap-1 rounded-full bg-rose-400/20 px-2.5 py-0.5 text-[11px] font-semibold text-rose-100">
                            {{ $serviciosConDeudaCount }} servicio{{ $serviciosConDeudaCount === 1 ? '' : 's' }} con saldo
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjetas principales --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            <a href="{{ route('servicios.index') }}" class="group rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md hover:shadow-amber-100">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500 text-white shadow-sm shadow-amber-200">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-amber-700/80">Por entregar</p>
                <p class="mt-0.5 text-2xl font-extrabold text-amber-900">{{ $serviciosPendientes }}</p>
            </a>

            <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-teal-50 p-4 shadow-sm">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-sm shadow-emerald-200">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-emerald-700/80">Entregados</p>
                <p class="mt-0.5 text-2xl font-extrabold text-emerald-900">{{ $serviciosEntregados }}</p>
            </div>

            <a href="{{ route('servicios.index', ['estado_pago' => 'PENDIENTE']) }}" class="group rounded-2xl border border-rose-200 bg-gradient-to-br from-rose-50 to-pink-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md hover:shadow-rose-100">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-500 text-white shadow-sm shadow-rose-200">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-rose-700/80">Sin pagar</p>
                <p class="mt-0.5 text-2xl font-extrabold text-rose-900">{{ $pagosPendientes }}</p>
            </a>

            <a href="{{ route('servicios.index', ['estado_pago' => 'PARCIAL']) }}" class="group rounded-2xl border border-orange-200 bg-gradient-to-br from-orange-50 to-amber-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md hover:shadow-orange-100">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-500 text-white shadow-sm shadow-orange-200">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                </div>
                <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-orange-700/80">Pagos parciales</p>
                <p class="mt-0.5 text-2xl font-extrabold text-orange-900">{{ $pagosParciales }}</p>
            </a>

            <a href="{{ route('clientes.index') }}" class="group rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-cyan-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md hover:shadow-sky-100">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-500 text-white shadow-sm shadow-sky-200">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
                <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-sky-700/80">Clientes</p>
                <p class="mt-0.5 text-2xl font-extrabold text-sky-900">{{ $totalClientes }}</p>
            </a>

            <a href="{{ route('agencias.index') }}" class="group rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 to-purple-50 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md hover:shadow-violet-100">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-500 text-white shadow-sm shadow-violet-200">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                    </svg>
                </div>
                <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-violet-700/80">Agencias</p>
                <p class="mt-0.5 text-2xl font-extrabold text-violet-900">{{ $totalAgencias }}</p>
            </a>
        </div>

        {{-- Totalizados --}}
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
            <div class="flex items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 sm:text-base">Totalizados generales</h3>
                    <p class="text-xs text-slate-500">{{ $totalServicios }} servicios registrados en total</p>
                </div>
                <a href="{{ route('servicios.create') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white shadow-md shadow-sky-200 hover:bg-sky-700">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nuevo
                </a>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3 text-center">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Facturado</p>
                    <p class="mt-1 text-lg font-extrabold text-slate-800 sm:text-xl">S/ {{ number_format($totalFacturado, 2) }}</p>
                </div>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-3 py-3 text-center">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-emerald-600">Cobrado</p>
                    <p class="mt-1 text-lg font-extrabold text-emerald-800 sm:text-xl">S/ {{ number_format($totalCobrado, 2) }}</p>
                </div>
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-3 py-3 text-center">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-rose-600">Por cobrar</p>
                    <p class="mt-1 text-lg font-extrabold text-rose-800 sm:text-xl">S/ {{ number_format($totalDeuda, 2) }}</p>
                </div>
                <div class="rounded-2xl border border-indigo-200 bg-indigo-50 px-3 py-3 text-center">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-indigo-600">Este mes</p>
                    <p class="mt-1 text-lg font-extrabold text-indigo-800 sm:text-xl">S/ {{ number_format($ingresosMes, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Pendientes y deudas --}}
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-[1.75rem] border border-amber-200 bg-gradient-to-b from-amber-50/80 to-white p-4 shadow-lg shadow-amber-100/60 sm:p-5">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-500 text-white">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9.75-4.5h9.75m-9.75 0a3 3 0 1 0 6 0m-9.75 0V10.5m0 4.25V15m12 3.75H15.75M15.75 15v4.5m0-4.5a3 3 0 0 0 3-3h2.25a3 3 0 0 1 3 3v4.5H15.75m-6-4.5h6m-6 0V10.5m6 4.5V10.5m0 0V9.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V10.5" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-amber-900 sm:text-base">Servicios pendientes</h3>
                            <p class="text-xs text-amber-700/70">Próximos por entregar</p>
                        </div>
                    </div>
                    <a href="{{ route('servicios.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-900">Ver todos</a>
                </div>

                <div class="mt-4 space-y-2">
                    @forelse($serviciosPendientesRecientes as $servicio)
                        <a href="{{ route('servicios.edit', $servicio) }}" class="flex items-center justify-between gap-3 rounded-xl border border-amber-100 bg-white/80 px-3 py-2.5 transition hover:border-amber-200 hover:bg-white">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $servicio->cliente?->nombre ?? 'Sin cliente' }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $servicio->tipo_servicio }} · {{ $servicio->agencia?->nombre ?? '—' }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold text-slate-800">S/ {{ number_format($servicio->total, 2) }}</p>
                                <p class="text-[11px] text-amber-600">{{ optional($servicio->fecha_servicio)->format('d/m H:i') ?? '—' }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="rounded-xl border border-dashed border-amber-200 bg-white/60 px-4 py-6 text-center text-sm text-amber-700/70">No hay servicios pendientes de entrega.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-rose-200 bg-gradient-to-b from-rose-50/80 to-white p-4 shadow-lg shadow-rose-100/60 sm:p-5">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-rose-500 text-white">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-rose-900 sm:text-base">Deudas por cobrar</h3>
                            <p class="text-xs text-rose-700/70">Mayores saldos pendientes</p>
                        </div>
                    </div>
                    <a href="{{ route('servicios.index', ['estado_pago' => 'PENDIENTE']) }}" class="text-xs font-semibold text-rose-700 hover:text-rose-900">Ver deudas</a>
                </div>

                <div class="mt-4 space-y-2">
                    @forelse($serviciosConDeuda as $servicio)
                        <a href="{{ route('servicios.pagos', $servicio) }}" class="flex items-center justify-between gap-3 rounded-xl border border-rose-100 bg-white/80 px-3 py-2.5 transition hover:border-rose-200 hover:bg-white">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800">#{{ $servicio->id }} · {{ $servicio->cliente?->nombre ?? 'Sin cliente' }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $servicio->agencia?->nombre ?? '—' }} · Total S/ {{ number_format($servicio->total, 2) }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold text-rose-700">S/ {{ number_format($servicio->saldo_pendiente, 2) }}</p>
                                <span class="inline-flex rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $estadoPagoClasses[$servicio->estado_pago] ?? 'border-slate-200 bg-slate-50 text-slate-700' }}">
                                    {{ $servicio->estado_pago }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <p class="rounded-xl border border-dashed border-emerald-200 bg-emerald-50/60 px-4 py-6 text-center text-sm text-emerald-700">¡Todo cobrado! No hay deudas pendientes.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Actividad reciente --}}
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
                <h3 class="text-sm font-bold text-slate-800 sm:text-base">Últimos servicios</h3>
                <div class="mt-3 divide-y divide-slate-100">
                    @foreach($ultimosServicios as $servicio)
                        <div class="flex items-start justify-between gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $servicio->tipo_servicio }} · {{ $servicio->cliente?->nombre ?? '—' }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $servicio->agencia?->nombre ?? '—' }} · {{ optional($servicio->fecha_servicio)->format('d/m H:i') ?? '—' }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold text-slate-800">S/ {{ number_format($servicio->total, 2) }}</p>
                                <span class="inline-flex rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $estadoServicioClasses[$servicio->estado_servicio] ?? 'border-slate-200 bg-slate-50 text-slate-700' }}">
                                    {{ $servicio->estado_servicio }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-lg shadow-sky-100/70 sm:p-5">
                <h3 class="text-sm font-bold text-slate-800 sm:text-base">Últimos pagos</h3>
                <div class="mt-3 divide-y divide-slate-100">
                    @foreach($ultimosPagos as $pago)
                        <div class="flex items-start justify-between gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $pago->servicio?->cliente?->nombre ?? 'Servicio #'.$pago->servicio_id }}</p>
                                <p class="truncate text-xs text-slate-500">{{ optional($pago->fecha_pago)->format('d/m H:i') ?? '—' }} · {{ $pago->metodo_pago }}</p>
                            </div>
                            <p class="shrink-0 text-sm font-bold text-emerald-700">+ S/ {{ number_format($pago->monto, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
