<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FleteItem extends Model
{
    use HasFactory;

    protected $table = 'flete_items';

    // "total" = servicio + flete por fila; el total del registro se guarda en fletes.total_general.

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

    public function flete(): BelongsTo
    {
        return $this->belongsTo(Flete::class, 'flete_id');
    }
}
