<?php

namespace App\Http\Controllers;

use App\Http\Requests\FleteRequest;
use App\Models\Cliente;
use App\Models\Flete;
use App\Models\FletePago;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class FleteController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));

        $fletes = Flete::query()
            ->with(['cliente:id,nombre'])
            ->withCount('items')
            ->withCount('pagos')
            ->withSum('pagos as total_pagado', 'monto')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subquery) use ($search) {
                    if (ctype_digit($search)) {
                        $subquery->where('id', $search);

                        return;
                    }

                    $parsedDate = $this->parseSearchDate($search);

                    if ($parsedDate !== null) {
                        $subquery->orWhereDate('fecha', $parsedDate);
                    }

                    $subquery->orWhereHas('cliente', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                    });
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('fletes.index', compact('fletes', 'search'));
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
        $items = $data['items'];
        unset($data['items'], $data['cliente_nombre_busqueda']);

        $data['cliente_id'] = $this->resolveClienteId($request->validated());

        $flete = Flete::create($data);
        $this->syncItems($flete, $items);

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
        $items = $data['items'];
        unset($data['items'], $data['cliente_nombre_busqueda']);

        $nuevoTotal = round(collect($items)->sum(fn ($item) => (float) $item['total']), 2);
        $totalPagado = round($flete->totalPagado(), 2);

        if ($nuevoTotal < $totalPagado) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'items' => 'El total general (S/ '.number_format($nuevoTotal, 2).') no puede ser menor a lo ya pagado (S/ '.number_format($totalPagado, 2).'). Elimina o corrige pagos primero.',
                ]);
        }

        $data['cliente_id'] = $this->resolveClienteId($request->validated());

        $flete->update($data);
        $this->syncItems($flete, $items);

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
        $faltaPagar = round($flete->faltaPagar(), 2);

        if ($faltaPagar <= 0) {
            return redirect()
                ->route('fletes.pagos', $flete)
                ->withErrors(['monto' => 'Este flete ya está pagado completamente.']);
        }

        $request->merge([
            'descripcion' => trim((string) $request->input('descripcion')),
        ]);

        $data = $request->validate([
            'descripcion' => ['required', 'string', 'max:500'],
            'monto' => ['required', 'numeric', 'min:0.01', 'max:'.$faltaPagar],
        ], [
            'descripcion.required' => 'La descripción es obligatoria.',
            'monto.max' => 'El monto no puede exceder lo que falta por pagar (S/ '.number_format($faltaPagar, 2).').',
            'monto.min' => 'El monto debe ser mayor a 0.',
        ], [
            'descripcion' => 'descripción',
            'monto' => 'monto',
        ]);

        $data['monto'] = round((float) $data['monto'], 2);

        if ($data['monto'] > $faltaPagar) {
            return redirect()
                ->route('fletes.pagos', $flete)
                ->withInput()
                ->withErrors(['monto' => 'El monto no puede exceder lo que falta por pagar (S/ '.number_format($faltaPagar, 2).').']);
        }

        $flete->pagos()->create($data);

        return redirect()
            ->route('fletes.pagos', $flete)
            ->with('success', 'Pago registrado correctamente.');
    }

    public function destroyPago(Flete $flete, FletePago $pago): RedirectResponse
    {
        abort_unless($pago->flete_id === $flete->id, 404);

        $pago->delete();

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

        return Cliente::firstOrCreate(['nombre' => $nombre])->id;
    }

    private function syncItems(Flete $flete, array $items): void
    {
        $flete->items()->delete();

        foreach ($items as $item) {
            $flete->items()->create($item);
        }

        // Total general guardado en fletes = suma de la columna total de cada ítem.
        $flete->update(['total_general' => $flete->items()->sum('total')]);
    }

    private function parseSearchDate(string $search): ?string
    {
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y', 'd-m-y'];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $search);

                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($search)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
