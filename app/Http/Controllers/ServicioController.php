<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServicioRequest;
use App\Models\Agencia;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Support\DashboardCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ServicioController extends Controller
{
    public function index(Request $request): View
    {
        // Parámetros de búsqueda y filtros
        $search = trim((string) $request->string('q'));
        $tipoServicio = trim((string) $request->string('tipo_servicio'));
        $estadoPago = trim((string) $request->string('estado_pago'));
        $startDate = trim((string) $request->string('start_date'));
        $endDate = trim((string) $request->string('end_date'));

        // Si no se especifica rango, por defecto usar el mes actual
        if ($startDate === '' && $endDate === '') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        } else {
            $start = $startDate !== '' ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
            $end = $endDate !== '' ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfMonth();
        }

        $servicios = Servicio::query()
            ->with([
                'cliente:id,nombre',
                'agencia:id,nombre',
            ])
            ->withSum('pagos as total_pagado', 'monto')
            ->withCount('pagos')
            ->when($search !== '', function ($query) use ($search) {
                if (ctype_digit($search)) {
                    $query->where('id', $search);

                    return;
                }

                $query->where(function ($q) use ($search) {
                    $q->where('descripcion', 'like', "%{$search}%")
                      ->orWhereHas('cliente', function ($q2) use ($search) {
                          $q2->where('nombre', 'like', "%{$search}%");
                      })
                      ->orWhereHas('agencia', function ($q3) use ($search) {
                          $q3->where('nombre', 'like', "%{$search}%");
                      });
                });
            })
            ->when($tipoServicio !== '', function ($query) use ($tipoServicio) {
                $query->where('tipo_servicio', $tipoServicio);
            })
            ->when($estadoPago !== '', function ($query) use ($estadoPago) {
                $query->where('estado_pago', $estadoPago);
            })
            ->whereBetween('fecha_servicio', [$start, $end])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('servicios.index', [
            'servicios' => $servicios,
            'search' => $search,
            'tipo_servicio' => $tipoServicio,
            'estado_pago' => $estadoPago,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
        ]);
    }

    public function create(): View
    {
        return view('servicios.create', [
            'servicio' => new Servicio(),
            ...$this->formOptions(),
        ]);
    }

    public function store(ServicioRequest $request): RedirectResponse
    {
        Servicio::create([
            ...$this->payload($request),
            'estado_pago' => 'PENDIENTE',
        ]);

        DashboardCache::forget();

        return redirect()
            ->route('servicios.index')
            ->with('success', 'Servicio registrado correctamente.');
    }

    public function edit(Servicio $servicio): View
    {
        return view('servicios.edit', [
            'servicio' => $servicio,
            ...$this->formOptions(),
        ]);
    }

    public function update(ServicioRequest $request, Servicio $servicio): RedirectResponse
    {
        $servicio->update($this->payload($request));

        DashboardCache::forget();

        return redirect()
            ->route('servicios.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        $servicio->delete();

        DashboardCache::forget();

        return redirect()
            ->route('servicios.index')
            ->with('success', 'Servicio eliminado correctamente.');
    }

    public function pagos(Servicio $servicio): View
    {
        $servicio->load([
            'cliente:id,nombre',
            'agencia:id,nombre',
            'pagos' => function ($query) {
                $query->latest('fecha_pago');
            },
        ]);

        $totalPagado = (float) $servicio->pagos->sum('monto');
        $saldoPendiente = max((float) $servicio->total - $totalPagado, 0);

        return view('servicios.pagos', compact('servicio', 'totalPagado', 'saldoPendiente'));
    }

    public function storePago(Request $request, Servicio $servicio): RedirectResponse
    {
        $data = $request->validate([
            'fecha_pago' => ['required', 'date'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'metodo_pago' => ['required', 'in:EFECTIVO,YAPE,PLIN,TRANSFERENCIA'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'fecha_pago' => 'fecha de pago',
            'monto' => 'monto',
            'metodo_pago' => 'método de pago',
            'observacion' => 'observación',
        ]);

        $servicio->pagos()->create($data);
        $this->syncEstadoPago($servicio);

        DashboardCache::forget();

        return redirect()
            ->route('servicios.pagos', $servicio)
            ->with('success', 'Pago registrado correctamente.');
    }

    private function formOptions(): array
    {
        return [
            'clientesOptions' => Cliente::query()->orderBy('nombre')->get(['id', 'nombre']),
            'agenciasOptions' => Agencia::query()->orderBy('nombre')->get(['id', 'nombre']),
        ];
    }

    private function payload(ServicioRequest $request): array
    {
        $data = $request->validated();
        $data['cliente_id'] = $this->resolveClienteId($data);
        $data['agencia_id'] = $this->resolveAgenciaId($data);
        $data['fecha_servicio'] = $data['fecha_servicio'] ?? Carbon::now();
        $data['fecha_entrega'] = Carbon::now();
        $data['total'] = (float) $data['costo_transporte'] + (float) $data['costo_flete'];
        unset($data['cliente_nombre_busqueda'], $data['agencia_nombre_busqueda']);

        return $data;
    }

    private function resolveClienteId(array $data): int
    {
        if (! empty($data['cliente_id'])) {
            return (int) $data['cliente_id'];
        }

        $nombre = trim((string) ($data['cliente_nombre_busqueda'] ?? ''));

        return Cliente::firstOrCreate(['nombre' => $nombre])->id;
    }

    private function resolveAgenciaId(array $data): int
    {
        if (! empty($data['agencia_id'])) {
            return (int) $data['agencia_id'];
        }

        $nombre = trim((string) ($data['agencia_nombre_busqueda'] ?? ''));

        return Agencia::firstOrCreate(['nombre' => $nombre])->id;
    }

    private function syncEstadoPago(Servicio $servicio): void
    {
        $totalPagado = (float) $servicio->pagos()->sum('monto');
        $totalServicio = (float) $servicio->total;

        if ($totalPagado <= 0) {
            $estadoPago = 'PENDIENTE';
        } elseif ($totalPagado < $totalServicio) {
            $estadoPago = 'PARCIAL';
        } else {
            $estadoPago = 'PAGADO';
        }

        $servicio->forceFill(['estado_pago' => $estadoPago])->save();
    }
}