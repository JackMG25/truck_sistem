<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FletePago extends Model
{
    use HasFactory;

    protected $table = 'flete_pagos';

    protected $fillable = [
        'flete_id',
        'descripcion',
        'monto',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function flete(): BelongsTo
    {
        return $this->belongsTo(Flete::class, 'flete_id');
    }
}
