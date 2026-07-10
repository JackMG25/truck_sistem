<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->map(function ($item) {
                return [
                    'fecha' => $item['fecha'] ?? null,
                    'descripcion' => trim((string) ($item['descripcion'] ?? '')),
                    'servicio' => $item['servicio'] ?? 0,
                    'flete' => $item['flete'] ?? 0,
                    'total' => $item['total'] ?? 0,
                ];
            })
            ->filter(function ($item) {
                return $item['fecha'] !== null
                    || $item['descripcion'] !== ''
                    || (float) $item['servicio'] > 0
                    || (float) $item['flete'] > 0
                    || (float) $item['total'] > 0;
            })
            ->values()
            ->all();

        $this->merge([
            'cliente_id' => $this->filled('cliente_id') ? $this->input('cliente_id') : null,
            'cliente_nombre_busqueda' => trim((string) $this->input('cliente_nombre_busqueda')),
            'items' => $items,
        ]);
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'cliente_nombre_busqueda' => ['required_without:cliente_id', 'nullable', 'string', 'max:150'],
            'fecha' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.fecha' => ['required', 'date'],
            'items.*.descripcion' => ['nullable', 'string', 'max:500'],
            'items.*.servicio' => ['required', 'numeric', 'min:0'],
            'items.*.flete' => ['required', 'numeric', 'min:0'],
            'items.*.total' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'cliente_nombre_busqueda' => 'cliente',
            'fecha' => 'fecha',
            'items' => 'productos',
            'items.*.fecha' => 'fecha del producto',
            'items.*.descripcion' => 'descripción',
            'items.*.servicio' => 'servicio',
            'items.*.flete' => 'flete',
            'items.*.total' => 'total',
        ];
    }
}
