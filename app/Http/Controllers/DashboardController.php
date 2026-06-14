<?php

namespace App\Http\Controllers;

use App\Models\Agencia;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Servicio;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $today = Carbon::today();

        $totalClientes = Cliente::query()->count();
        $totalAgencias = Agencia::query()->count();

        $serviciosPendientes = Servicio::where('estado_servicio', 'PENDIENTE')->count();
        $serviciosEntregados = Servicio::where('estado_servicio', 'ENTREGADO')->count();

        // Servicios cuyo estado de pago no es PAGADO
        $pagosPendientes = Servicio::where('estado_pago', '!=', 'PAGADO')->count();

        // Ingresos: sumas desde la tabla pagos (más correcto que sumar servicios)
        $ingresosDia = Pago::whereDate('fecha_pago', $today)->sum('monto');
        $ingresosMes = Pago::whereYear('fecha_pago', $today->year)
            ->whereMonth('fecha_pago', $today->month)
            ->sum('monto');

        $ultimosServicios = Servicio::with(['cliente', 'agencia'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $ultimosPagos = Pago::with('servicio')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        return view('dashboard', compact(
            'totalClientes',
            'totalAgencias',
            'serviciosPendientes',
            'serviciosEntregados',
            'pagosPendientes',
            'ingresosDia',
            'ingresosMes',
            'ultimosServicios',
            'ultimosPagos'
        ));
    }
}
