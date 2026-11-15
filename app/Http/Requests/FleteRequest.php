<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->map(function ($item) {
                $descripcion = trim((string) ($item['descripcion'] ?? ''));

                return [
                    'fecha' => $item['fecha'] ?? null,
                    'descripcion' => $descripcion,
                    'servicio' => $item['servicio'] ?? 0,
                    'flete' => $item['flete'] ?? 0,
                ];
            })
            ->filter(function ($item) {
                // Una fila solo con fecha no es un producto. El total lo calcula el servidor.
                return $item['descripcion'] !== ''
                    || (float) $item['servicio'] > 0
                    || (float) $item['flete'] > 0;
            })
            ->values()
            ->all();

        $this->merge([
            'cliente_id' => $this->filled('cliente_id') ? $this->input('cliente_id') : null,
            'cliente_nombre_busqueda' => preg_replace('/\s+/u', ' ', trim((string) $this->input('cliente_nombre_busqueda'))) ?? '',
            'items' => $items,
        ]);
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'cliente_nombre_busqueda' => ['required_without:cliente_id', 'nullable', 'string', 'max:255'],
            'fecha' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1', 'max:80'],
            'items.*.fecha' => ['required', 'date'],
            'items.*.descripcion' => ['nullable', 'string', 'max:255'],
            'items.*.servicio' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'items.*.flete' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
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
        ];
    }
}
