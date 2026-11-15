<?php

namespace App\Http\Controllers;

use App\Http\Requests\FleteRequest;
use App\Models\Cliente;
use App\Models\Flete;
use App\Models\FleteItem;
use App\Models\FletePago;
use App\Support\Money;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class FleteController extends Controller
{
    public function index(Request $request): View
    {
        $search = mb_substr(trim((string) $request->string('q')), 0, 100);
        $desde = $this->parseFilterDate($request->query('desde'));
        $hasta = $this->parseFilterDate($request->query('hasta'));

        // Si el usuario invierte el rango, se ordena para que la consulta no quede vacía.
        if ($desde !== '' && $hasta !== '' && $desde > $hasta) {
            [$desde, $hasta] = [$hasta, $desde];
        }

        $filtered = Flete::query()
            ->when($search !== '', function ($query) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';
                $parsedDate = $this->parseSearchDate($search);

                $query->where(function ($subquery) use ($search, $like, $parsedDate) {
                    $subquery->whereHas('cliente', function ($q) use ($like) {
                        $q->where('nombre', 'like', $like);
                    });

                    if ($parsedDate !== null) {
                        $subquery->orWhereDate('fecha', $parsedDate);
                    }

                    if (ctype_digit($search)) {
                        $subquery->orWhere('id', (int) $search);
                    }
                });
            })
            ->when($desde !== '', fn ($query) => $query->where('fecha', '>=', $desde))
            ->when($hasta !== '', fn ($query) => $query->where('fecha', '<=', $hasta));

        $fletes = (clone $filtered)
            ->select(['id', 'cliente_id', 'fecha', 'total_general'])
            ->with(['cliente:id,nombre'])
            ->withCount('items')
            ->withCount('pagos')
            ->withSum('pagos as total_pagado', 'monto')
            ->latest('id')
            ->paginate(10)
            ->onEachSide(1)
            ->withQueryString();

        // La página muestra 10 filas; estos montos son de todo el resultado filtrado.
        $totalGeneralCents = Money::cents((clone $filtered)->sum('total_general'));
        $pagadoCents = Money::cents(
            FletePago::query()
                ->whereIn('flete_id', (clone $filtered)->select('id'))
                ->sum('monto')
        );

        $totales = [
            'cantidad' => $fletes->total(),
            'total_general' => $totalGeneralCents / 100,
            'pagado' => $pagadoCents / 100,
            'falta' => max($totalGeneralCents - $pagadoCents, 0) / 100,
        ];

        return view('fletes.index', compact('fletes', 'search', 'desde', 'hasta', 'totales'));
    }

    public function create(): View
    {
        return view('fletes.create', [
            'flete' => new Flete(['fecha' => Carbon::now()]),
            ...$this->formOptions(),
        ]);
    }

    public function store(FleteRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $flete = Flete::query()->create([
                'cliente_id' => $this->resolveClienteId($data),
                'fecha' => $data['fecha'],
                'total_general' => 0,
            ]);

            $flete->replaceItems($data['items']);
        });

        return redirect()
            ->route('fletes.index')
            ->with('success', 'Flete registrado correctamente.');
    }

    public function edit(Flete $flete): View
    {
        $flete->load('items');

        return view('fletes.edit', [
            'flete' => $flete,
            ...$this->formOptions(),
        ]);
    }

    public function update(FleteRequest $request, Flete $flete): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $flete) {
            $locked = Flete::query()->whereKey($flete->id)->lockForUpdate()->firstOrFail();
            $nuevoTotal = FleteItem::totalCents($data['items']);

            if ($nuevoTotal < $locked->totalPagadoCents()) {
                throw ValidationException::withMessages([
                    'items' => 'El total general (S/ '.Money::format($nuevoTotal).') no puede ser menor a lo ya pagado (S/ '.Money::format($locked->totalPagadoCents()).'). Elimina o corrige pagos primero.',
                ]);
            }

            $locked->update([
                'cliente_id' => $this->resolveClienteId($data),
                'fecha' => $data['fecha'],
            ]);

            $locked->replaceItems($data['items']);
        });

        return redirect()
            ->route('fletes.index')
            ->with('success', 'Flete actualizado correctamente.');
    }

    public function destroy(Flete $flete): RedirectResponse
    {
        $flete->delete();

        return redirect()
            ->route('fletes.index')
            ->with('success', 'Flete eliminado correctamente.');
    }

    public function download(Flete $flete): Response|RedirectResponse
    {
        try {
            $pdf = Pdf::loadView('fletes.pdf', [
                'flete' => $flete->loadForPdf(),
            ]);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="flete-'.$flete->id.'.pdf"',
            ]);
        } catch (\Throwable $exception) {
            Log::error('Error al generar PDF de flete', [
                'flete_id' => $flete->id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('fletes.index')
                ->with('error', 'No se pudo generar el PDF. Verifica que DomPDF esté instalado y que storage/ tenga permisos de escritura.');
        }
    }

    public function pagos(Flete $flete): View
    {
        $flete->load([
            'cliente:id,nombre',
            'pagos' => fn ($query) => $query->latest('id'),
        ]);

        $totalPagado = $flete->totalPagado();
        $faltaPagar = $flete->faltaPagar();

        return view('fletes.pagos', compact('flete', 'totalPagado', 'faltaPagar'));
    }

    public function storePago(Request $request, Flete $flete): RedirectResponse
    {
        DB::transaction(function () use ($request, $flete) {
            $locked = Flete::query()->whereKey($flete->id)->lockForUpdate()->firstOrFail();
            $faltaCents = max(Money::cents($locked->total_general) - $locked->totalPagadoCents(), 0);

            if ($faltaCents <= 0) {
                throw ValidationException::withMessages([
                    'monto' => 'Este flete ya está pagado completamente.',
                ]);
            }

            $request->merge([
                'descripcion' => trim((string) $request->input('descripcion')),
            ]);

            $validated = $request->validate([
                'descripcion' => ['required', 'string', 'max:255'],
                'monto' => ['required', 'numeric', 'min:0.01', 'max:'.Money::format($faltaCents)],
            ], [
                'descripcion.required' => 'La descripción es obligatoria.',
                'monto.max' => 'El monto no puede exceder lo que falta por pagar (S/ '.Money::format($faltaCents).').',
                'monto.min' => 'El monto debe ser mayor a 0.',
            ], [
                'descripcion' => 'descripción',
                'monto' => 'monto',
            ]);

            $montoCents = Money::cents($validated['monto']);

            if ($montoCents > $faltaCents) {
                throw ValidationException::withMessages([
                    'monto' => 'El monto no puede exceder lo que falta por pagar (S/ '.Money::format($faltaCents).').',
                ]);
            }

            $locked->pagos()->create([
                'descripcion' => $validated['descripcion'],
                'monto' => Money::format($montoCents),
            ]);
        });

        return redirect()
            ->route('fletes.pagos', $flete)
            ->with('success', 'Pago registrado correctamente.');
    }

    public function destroyPago(Flete $flete, FletePago $pago): RedirectResponse
    {
        $owned = $flete->pagos()->whereKey($pago->id)->first();

        abort_if($owned === null, 404);

        $owned->delete();

        return redirect()
            ->route('fletes.pagos', $flete)
            ->with('success', 'Pago eliminado correctamente.');
    }

    private function formOptions(): array
    {
        return [
            'clientesOptions' => Cliente::query()->orderBy('nombre')->get(['id', 'nombre']),
        ];
    }

    private function resolveClienteId(array $data): int
    {
        if (! empty($data['cliente_id'])) {
            return (int) $data['cliente_id'];
        }

        $nombre = trim((string) ($data['cliente_nombre_busqueda'] ?? ''));

        return Cliente::query()->firstOrCreate(['nombre' => $nombre])->id;
    }

    private function parseFilterDate(mixed $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        try {
            $date = Carbon::createFromFormat('!Y-m-d', $value);

            if ($date !== false && $date->format('Y-m-d') === $value) {
                return $value;
            }
        } catch (\Throwable) {
            return '';
        }

        return '';
    }

    private function parseSearchDate(string $search): ?string
    {
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y', 'd-m-y'] as $format) {
            try {
                $date = Carbon::createFromFormat('!'.$format, $search);

                if ($date !== false && $date->format($format) === $search) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }
}
