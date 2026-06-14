<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgenciaRequest;
use App\Http\Requests\ClienteRequest;
use App\Http\Requests\ServicioRequest;
use App\Models\Agencia;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Servicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
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
                'pagos' => function ($query) {
                    $query->latest('fecha_pago');
                },
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', $search)
                      ->orWhere('descripcion', 'like', "%{$search}%")
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
            'clientesOptions' => Cliente::query()->orderBy('nombre')->get(['id', 'nombre']),
            'agenciasOptions' => Agencia::query()->orderBy('nombre')->get(['id', 'nombre']),
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
        Servicio::create($this->payload($request));

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

        return redirect()
            ->route('servicios.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        $servicio->delete();

        return redirect()
            ->route('servicios.index')
            ->with('success', 'Servicio eliminado correctamente.');
    }

    public function storePagoInline(Request $request, Servicio $servicio): JsonResponse
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

        $pago = $servicio->pagos()->create($data);
        $this->syncEstadoPago($servicio);
        $servicio->refresh();

        return response()->json([
            'message' => 'Pago registrado correctamente.',
            'pago' => [
                'id' => $pago->id,
                'fecha_pago' => optional($pago->fecha_pago)->format('d/m/Y H:i'),
                'monto' => number_format((float) $pago->monto, 2, '.', ','),
                'metodo_pago' => $pago->metodo_pago,
                'observacion' => $pago->observacion,
            ],
            'estado_pago' => $servicio->estado_pago,
            'total_pagado' => number_format((float) $servicio->pagos()->sum('monto'), 2, '.', ','),
            'saldo' => number_format(max((float) $servicio->total - (float) $servicio->pagos()->sum('monto'), 0), 2, '.', ','),
        ]);
    }

    public function storeClienteInline(ClienteRequest $request): JsonResponse
    {
        $cliente = Cliente::create($request->validated());

        return response()->json([
            'id' => $cliente->id,
            'nombre' => $cliente->nombre,
            'message' => 'Cliente creado correctamente.',
        ]);
    }

    public function storeAgenciaInline(AgenciaRequest $request): JsonResponse
    {
        $agencia = Agencia::create($request->validated());

        return response()->json([
            'id' => $agencia->id,
            'nombre' => $agencia->nombre,
            'message' => 'Agencia creada correctamente.',
        ]);
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
        $data['fecha_servicio'] = $data['fecha_servicio'] ?? Carbon::now();
        $data['fecha_entrega'] = Carbon::now();
        $data['total'] = (float) $data['costo_transporte'] + (float) $data['costo_flete'];

        return $data;
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