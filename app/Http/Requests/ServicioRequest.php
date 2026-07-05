<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cliente_id' => $this->filled('cliente_id') ? $this->input('cliente_id') : null,
            'cliente_nombre_busqueda' => trim((string) $this->input('cliente_nombre_busqueda')),
            'agencia_id' => $this->filled('agencia_id') ? $this->input('agencia_id') : null,
            'agencia_nombre_busqueda' => trim((string) $this->input('agencia_nombre_busqueda')),
            // Campos vacíos se guardan como 0; el formulario los muestra sin valor por defecto.
            'costo_transporte' => $this->filled('costo_transporte') ? $this->input('costo_transporte') : 0,
            'costo_flete' => $this->filled('costo_flete') ? $this->input('costo_flete') : 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'cliente_nombre_busqueda' => ['required_without:cliente_id', 'nullable', 'string', 'max:150'],
            'agencia_id' => ['nullable', 'integer', 'exists:agencias,id'],
            'agencia_nombre_busqueda' => ['required_without:agencia_id', 'nullable', 'string', 'max:150'],
            'tipo_servicio' => ['required', Rule::in(['ENVIO', 'RECOJO'])],
            'fecha_servicio' => ['nullable', 'date'],
            'cantidad_bultos' => ['required', 'integer', 'min:1'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'costo_transporte' => ['required', 'numeric', 'min:0'],
            'costo_flete' => ['required', 'numeric', 'min:0'],
            'estado_servicio' => ['required', Rule::in(['PENDIENTE', 'ENTREGADO'])],
            'fecha_entrega' => ['nullable', 'date', 'after_or_equal:fecha_servicio'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'cliente_nombre_busqueda' => 'cliente',
            'agencia_id' => 'agencia',
            'agencia_nombre_busqueda' => 'agencia',
            'tipo_servicio' => 'tipo de servicio',
            'fecha_servicio' => 'fecha de servicio',
            'cantidad_bultos' => 'cantidad de bultos',
            'descripcion' => 'descripción',
            'costo_transporte' => 'costo de transporte',
            'costo_flete' => 'costo de flete',
            'estado_servicio' => 'estado del servicio',
            'fecha_entrega' => 'fecha de entrega',
            'observaciones' => 'observaciones',
        ];
    }
}