<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgenciaRequest;
use App\Models\Agencia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgenciaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));

        $agencias = Agencia::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery
                        ->where('nombre', 'like', "%{$search}%")
                        ->orWhere('telefono', 'like', "%{$search}%")
                        ->orWhere('direccion', 'like', "%{$search}%")
                        ->orWhere('observaciones', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('agencias.index', compact('agencias', 'search'));
    }

    public function create(): View
    {
        return view('agencias.create', [
            'agencia' => new Agencia(),
        ]);
    }

    public function store(AgenciaRequest $request): RedirectResponse
    {
        Agencia::create($request->validated());

        return redirect()
            ->route('agencias.index')
            ->with('success', 'Agencia registrada correctamente.');
    }

    public function edit(Agencia $agencia): View
    {
        return view('agencias.edit', compact('agencia'));
    }

    public function update(AgenciaRequest $request, Agencia $agencia): RedirectResponse
    {
        $agencia->update($request->validated());

        return redirect()
            ->route('agencias.index')
            ->with('success', 'Agencia actualizada correctamente.');
    }

    public function destroy(Agencia $agencia): RedirectResponse
    {
        $agencia->delete();

        return redirect()
            ->route('agencias.index')
            ->with('success', 'Agencia eliminada correctamente.');
    }
}