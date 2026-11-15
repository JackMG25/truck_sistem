<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flete extends Model
{
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

    public function totalPagadoCents(): int
    {
        if ($this->relationLoaded('pagos')) {
            return (int) $this->pagos->sum(fn (FletePago $pago) => Money::cents($pago->monto));
        }

        return Money::cents($this->pagos()->sum('monto'));
    }

    public function totalPagado(): float
    {
        return $this->totalPagadoCents() / 100;
    }

    public function faltaPagar(): float
    {
        return max(Money::cents($this->total_general) - $this->totalPagadoCents(), 0) / 100;
    }

    /**
     * Reemplaza el detalle y guarda el total general como suma de cada fila.
     * El total de la fila es servicio + flete; no se acepta el total enviado por el navegador.
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    public function replaceItems(array $items): void
    {
        $this->items()->delete();

        $rows = [];

        foreach ($items as $item) {
            $rows[] = FleteItem::row($this->id, $item);
        }

        if ($rows !== []) {
            FleteItem::query()->insert($rows);
        }

        $this->update([
            'total_general' => Money::format(FleteItem::totalCents($items)),
        ]);
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
