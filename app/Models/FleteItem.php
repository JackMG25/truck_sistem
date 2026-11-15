<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class FleteItem extends Model
{
    protected $table = 'flete_items';

    protected $fillable = [
        'flete_id',
        'fecha',
        'descripcion',
        'servicio',
        'flete',
        'total',
    ];

    protected $casts = [
        'fecha' => 'date',
        'servicio' => 'decimal:2',
        'flete' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (FleteItem $item): void {
            $item->total = Money::add($item->servicio, $item->flete);
        });
    }

    public function flete(): BelongsTo
    {
        return $this->belongsTo(Flete::class, 'flete_id');
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public static function totalCents(array $items): int
    {
        $cents = 0;

        foreach ($items as $item) {
            $cents += Money::cents($item['servicio'] ?? 0) + Money::cents($item['flete'] ?? 0);
        }

        return $cents;
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public static function row(int $fleteId, array $item): array
    {
        $servicio = Money::format(Money::cents($item['servicio'] ?? 0));
        $flete = Money::format(Money::cents($item['flete'] ?? 0));
        $now = now();
        $descripcion = trim((string) ($item['descripcion'] ?? ''));

        return [
            'flete_id' => $fleteId,
            'fecha' => Carbon::parse($item['fecha'])->toDateString(),
            'descripcion' => $descripcion !== '' ? $descripcion : null,
            'servicio' => $servicio,
            'flete' => $flete,
            'total' => Money::add($servicio, $flete),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
