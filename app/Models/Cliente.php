<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
    ];

    public function fletes(): HasMany
    {
        return $this->hasMany(Flete::class, 'cliente_id');
    }
}
