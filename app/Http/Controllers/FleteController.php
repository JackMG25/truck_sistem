<?php

namespace App\Http\Controllers;

use App\Http\Requests\FleteRequest;
use App\Models\Cliente;
use App\Models\Flete;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class FleteController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));

        $fletes = Flete::query()
            ->with(['cliente:id,nombre', 'items'])
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
            ->latest('fecha')
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
        $data['total_flete'] = collect($items)->sum(fn ($item) => (float) $item['total']);

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

        $data['cliente_id'] = $this->resolveClienteId($request->validated());
        $data['total_flete'] = collect($items)->sum(fn ($item) => (float) $item['total']);

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

    public function download(Flete $flete): Response
    {
        $flete->load(['cliente:id,nombre', 'items']);

        $pdf = Pdf::loadView('fletes.pdf', compact('flete'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('flete-'.$flete->id.'.pdf');
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
