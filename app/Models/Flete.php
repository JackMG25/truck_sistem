<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flete extends Model
{
    use HasFactory;

    protected $table = 'fletes';

    protected $fillable = [
        'cliente_id',
        'fecha',
        'total_general',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total_general' => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FleteItem::class, 'flete_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(FletePago::class, 'flete_id');
    }

    public function totalPagado(): float
    {
        if ($this->relationLoaded('pagos')) {
            return (float) $this->pagos->sum('monto');
        }

        return (float) $this->pagos()->sum('monto');
    }

    public function faltaPagar(): float
    {
        return max((float) $this->total_general - $this->totalPagado(), 0);
    }

    /** Carga solo lo necesario para generar el PDF del registro. */
    public function loadForPdf(): static
    {
        $this->load([
            'cliente:id,nombre',
            'items',
            'pagos' => fn ($query) => $query->orderBy('id'),
        ]);

        return $this;
    }
}
