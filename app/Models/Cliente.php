<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'telefono',
        'direccion',
        'observaciones',
    ];

    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'cliente_id');
    }

    public function fletes()
    {
        return $this->hasMany(Flete::class, 'cliente_id');
    }
}
