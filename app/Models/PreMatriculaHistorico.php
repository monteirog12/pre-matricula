<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreMatriculaHistorico extends Model
{
    public $timestamps = false;

    protected $table = 'pre_matricula_historico';

    protected $casts = [
        'alterado_em' => 'datetime',
    ];

    protected $fillable = [
        'usuario_id', 'campo_alterado', 'valor_anterior', 'valor_novo', 'motivo',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function preMatricula(): BelongsTo
    {
        return $this->belongsTo(PreMatricula::class);
    }
}
