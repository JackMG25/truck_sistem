<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agencia extends Model
{
    use HasFactory;

    protected $table = 'agencias';

    protected $fillable = [
        'nombre',
        'telefono',
        'direccion',
        'observaciones',
    ];

    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class, 'agencia_id');
    }
}