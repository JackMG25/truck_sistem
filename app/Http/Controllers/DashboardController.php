<?php

namespace App\Http\Controllers;

use App\Models\Agencia;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Servicio;
use App\Support\DashboardCache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $data = Cache::remember(
            DashboardCache::KEY,
            DashboardCache::TTL,
            fn () => $this->buildDashboardData()
        );

        return view('dashboard', $data);
    }

    private function buildDashboardData(): array
    {
        $today = Carbon::today();

        $totalClientes = Cliente::query()->count();
        $totalAgencias = Agencia::query()->count();

        // Una sola consulta para todos los contadores y el total facturado
        $servicioStats = Servicio::query()
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN estado_servicio = 'PENDIENTE' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado_servicio = 'ENTREGADO' THEN 1 ELSE 0 END) as entregados,
                SUM(CASE WHEN estado_pago = 'PENDIENTE' THEN 1 ELSE 0 END) as pagos_pendientes,
                SUM(CASE WHEN estado_pago = 'PARCIAL' THEN 1 ELSE 0 END) as pagos_parciales,
                SUM(CASE WHEN estado_pago != 'PAGADO' THEN 1 ELSE 0 END) as con_deuda,
                COALESCE(SUM(total), 0) as total_facturado
            ")
            ->first();

        $totalCobrado = (float) Pago::query()->sum('monto');
        $ingresosDia = (float) Pago::query()->whereDate('fecha_pago', $today)->sum('monto');
        $ingresosMes = (float) Pago::query()
            ->whereYear('fecha_pago', $today->year)
            ->whereMonth('fecha_pago', $today->month)
            ->sum('monto');

        // Deuda total en SQL: evita cargar todos los servicios impagos en memoria
        $totalDeuda = (float) DB::table('servicios')
            ->where('estado_pago', '!=', 'PAGADO')
            ->leftJoin(
                DB::raw('(SELECT servicio_id, SUM(monto) as total_pagado FROM pagos GROUP BY servicio_id) as pagos_agrupados'),
                'servicios.id',
                '=',
                'pagos_agrupados.servicio_id'
            )
            ->selectRaw('COALESCE(SUM(CASE WHEN (servicios.total - COALESCE(pagos_agrupados.total_pagado, 0)) > 0 THEN servicios.total - COALESCE(pagos_agrupados.total_pagado, 0) ELSE 0 END), 0) as deuda')
            ->value('deuda');

        // Top 5 deudas: orden en SQL, solo 5 filas con relaciones
        $serviciosConDeuda = Servicio::query()
            ->with(['cliente:id,nombre', 'agencia:id,nombre'])
            ->withSum('pagos', 'monto')
            ->where('estado_pago', '!=', 'PAGADO')
            ->select('servicios.*')
            ->selectRaw('(servicios.total - COALESCE((SELECT SUM(monto) FROM pagos WHERE pagos.servicio_id = servicios.id), 0)) as saldo_orden')
            ->orderByDesc('saldo_orden')
            ->limit(5)
            ->get()
            ->each(function (Servicio $servicio) {
                $servicio->total_pagado = $servicio->pagos_sum_monto;
            });

        $serviciosPendientesRecientes = Servicio::with(['cliente:id,nombre', 'agencia:id,nombre'])
            ->where('estado_servicio', 'PENDIENTE')
            ->orderBy('fecha_servicio')
            ->limit(5)
            ->get();

        $ultimosServicios = Servicio::with(['cliente:id,nombre', 'agencia:id,nombre'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $ultimosPagos = Pago::with(['servicio.cliente:id,nombre'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        return [
            'totalClientes' => $totalClientes,
            'totalAgencias' => $totalAgencias,
            'totalServicios' => (int) $servicioStats->total,
            'serviciosPendientes' => (int) $servicioStats->pendientes,
            'serviciosEntregados' => (int) $servicioStats->entregados,
            'pagosPendientes' => (int) $servicioStats->pagos_pendientes,
            'pagosParciales' => (int) $servicioStats->pagos_parciales,
            'serviciosConDeudaCount' => (int) $servicioStats->con_deuda,
            'totalDeuda' => $totalDeuda,
            'serviciosConDeuda' => $serviciosConDeuda,
            'totalFacturado' => (float) $servicioStats->total_facturado,
            'totalCobrado' => $totalCobrado,
            'ingresosDia' => $ingresosDia,
            'ingresosMes' => $ingresosMes,
            'serviciosPendientesRecientes' => $serviciosPendientesRecientes,
            'ultimosServicios' => $ultimosServicios,
            'ultimosPagos' => $ultimosPagos,
        ];
    }
}
