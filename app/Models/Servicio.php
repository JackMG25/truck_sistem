<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $attributes = [
        'estado_pago' => 'PENDIENTE',
    ];

    protected $fillable = [
        'cliente_id',
        'agencia_id',
        'tipo_servicio',
        'fecha_servicio',
        'cantidad_bultos',
        'descripcion',
        'costo_transporte',
        'costo_flete',
        'total',
        'estado_servicio',
        'estado_pago',
        'fecha_entrega',
        'observaciones',
    ];

    protected $casts = [
        'fecha_servicio' => 'datetime',
        'fecha_entrega' => 'datetime',
        'costo_transporte' => 'decimal:2',
        'costo_flete' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function agencia(): BelongsTo
    {
        return $this->belongsTo(Agencia::class, 'agencia_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'servicio_id');
    }

    public function getSaldoPendienteAttribute(): float
    {
        return max((float) $this->total - $this->resolveTotalPagado(), 0);
    }

    public function estaCompletamentePagado(): bool
    {
        return $this->saldo_pendiente <= 0;
    }

    protected function resolveTotalPagado(): float
    {
        if ($this->total_pagado !== null) {
            return (float) $this->total_pagado;
        }

        if ($this->relationLoaded('pagos')) {
            return (float) $this->pagos->sum('monto');
        }

        return (float) $this->pagos()->sum('monto');
    }
}