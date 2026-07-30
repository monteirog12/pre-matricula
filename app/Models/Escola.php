<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Escola extends Model
{
    protected $fillable = [
        'nome', 'endereco', 'responsavel', 'telefone', 'codigo_inep', 'zona', 'ativo',
    ];

    public function preMatriculas(): HasMany
    {
        return $this->hasMany(PreMatricula::class);
    }
}
