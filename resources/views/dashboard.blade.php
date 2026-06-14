<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 px-4">
        <div class="max-w-3xl mx-auto">
            <!-- Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                    <span class="text-xs text-gray-500">Total clientes</span>
                    <span class="text-2xl font-semibold">{{ $totalClientes }}</span>
                </div>

                <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                    <span class="text-xs text-gray-500">Total agencias</span>
                    <span class="text-2xl font-semibold">{{ $totalAgencias }}</span>
                </div>

                <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                    <span class="text-xs text-gray-500">Servicios pendientes</span>
                    <span class="text-2xl font-semibold">{{ $serviciosPendientes }}</span>
                </div>

                <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                    <span class="text-xs text-gray-500">Servicios entregados</span>
                    <span class="text-2xl font-semibold">{{ $serviciosEntregados }}</span>
                </div>

                <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                    <span class="text-xs text-gray-500">Pagos pendientes</span>
                    <span class="text-2xl font-semibold">{{ $pagosPendientes }}</span>
                </div>

                <div class="bg-white rounded-lg shadow p-4 flex flex-col">
                    <span class="text-xs text-gray-500">Ingresos del día</span>
                    <span class="text-2xl font-semibold">S/ {{ number_format($ingresosDia, 2) }}</span>
                </div>

                <div class="col-span-2 sm:col-span-3 bg-white rounded-lg shadow p-4 mt-2">
                    <span class="text-xs text-gray-500">Ingresos del mes</span>
                    <div class="text-3xl font-bold">S/ {{ number_format($ingresosMes, 2) }}</div>
                </div>
            </div>

            <!-- Latest lists -->
            <div class="mt-4 grid grid-cols-1 gap-4">
                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="font-semibold mb-2">Últimos servicios</h3>
                    <div class="divide-y">
                        @foreach($ultimosServicios as $servicio)
                            <div class="py-2 flex justify-between items-start">
                                <div>
                                    <div class="text-sm font-medium">{{ $servicio->tipo_servicio }} • {{ $servicio->cliente?->nombre ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $servicio->agencia?->nombre ?? '—' }} • {{ optional($servicio->fecha_servicio)->format('d/m H:i') ?? '—' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold">S/ {{ number_format($servicio->total, 2) }}</div>
                                    <div class="text-xs text-gray-500">{{ $servicio->estado_servicio }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="font-semibold mb-2">Últimos pagos</h3>
                    <div class="divide-y">
                        @foreach($ultimosPagos as $pago)
                            <div class="py-2 flex justify-between items-start">
                                <div>
                                    <div class="text-sm">Servicio #{{ $pago->servicio_id }}</div>
                                    <div class="text-xs text-gray-500">{{ optional($pago->fecha_pago)->format('d/m H:i') ?? '—' }} • {{ $pago->metodo_pago }}</div>
                                </div>
                                <div class="text-right font-semibold">S/ {{ number_format($pago->monto, 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
