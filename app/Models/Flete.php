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
        'total_flete',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total_flete' => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FleteItem::class, 'flete_id');
    }
}
