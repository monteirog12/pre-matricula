<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class PreMatricula extends Model
{
    protected $fillable = [
        'ano_letivo', 'aluno_id', 'escola_id', 'protocolo', 'tipo_aluno',
        'nivel_ensino', 'turno', 'situacao', 'serie_ano_anterior',
        'participa_programa_federal', 'qual_programa', 'nis', 'observacoes', 'status',
    ];

    protected $casts = [
        'participa_programa_federal' => 'boolean',
    ];

    // Preenchido pelo controller antes do update(), para o motivo
    // informado no formulario tambem ser gravado no historico.
    public ?string $motivoAlteracao = null;

    // Campos que, se alterados por um usuario logado, geram uma linha
    // em pre_matricula_historico (auditoria). Timestamps ficam de fora.
    protected array $camposAuditados = [
        'escola_id', 'turno', 'nivel_ensino', 'situacao', 'status',
        'serie_ano_anterior', 'nis', 'observacoes',
    ];

    protected static function booted(): void
    {
        // Gera o protocolo automaticamente ao criar (ex: CAF2027-000123)
        static::creating(function (PreMatricula $pm) {
            if (empty($pm->protocolo)) {
                $pm->protocolo = static::gerarProtocolo($pm->ano_letivo);
            }
        });

        // Registra no historico cada campo auditado que mudou,
        // somente quando a alteracao parte de um usuario logado
        // (edicao pelo admin/operador), nao no cadastro inicial.
        static::updating(function (PreMatricula $pm) {
            if (! Auth::check()) {
                return;
            }
            foreach ($pm->camposAuditados as $campo) {
                if ($pm->isDirty($campo)) {
                    [$valorAnterior, $valorNovo] = static::valoresLegiveis(
                        $campo,
                        $pm->getOriginal($campo),
                        $pm->{$campo}
                    );

                    $pm->historico()->create([
                        'usuario_id'      => Auth::id(),
                        'campo_alterado'  => $campo,
                        'valor_anterior'  => $valorAnterior,
                        'valor_novo'      => $valorNovo,
                        'motivo'          => $pm->motivoAlteracao,
                    ]);
                }
            }
        });
    }

    // Converte valores "tecnicos" (como um ID de escola) em algo legivel
    // antes de gravar no historico, para o admin nao ver numeros soltos.
    protected static function valoresLegiveis(string $campo, $anterior, $novo): array
    {
        if ($campo === 'escola_id') {
            return [
                optional(Escola::find($anterior))->nome ?? $anterior,
                optional(Escola::find($novo))->nome ?? $novo,
            ];
        }

        return [$anterior, $novo];
    }

    // Rotulos amigaveis para exibir o nome do campo alterado na tela,
    // em vez do nome tecnico da coluna do banco.
    public static array $rotulosCampos = [
        'escola_id'          => 'Escola',
        'turno'              => 'Turno',
        'nivel_ensino'       => 'Nível de ensino',
        'situacao'           => 'Situação',
        'status'             => 'Status',
        'serie_ano_anterior' => 'Série/ano anterior',
        'nis'                => 'NIS',
        'observacoes'        => 'Observações',
        'aluno_nome'         => 'Nome do aluno',
        'aluno_telefone'     => 'Telefone',
        'aluno_endereco'     => 'Endereço',
    ];

    public static function gerarProtocolo(int $anoLetivo): string
    {
        $ultimoNumero = static::where('ano_letivo', $anoLetivo)->count() + 1;

        return sprintf('CAF%d-%06d', $anoLetivo, $ultimoNumero);
    }

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }

    public function escola(): BelongsTo
    {
        return $this->belongsTo(Escola::class);
    }

    public function historico(): HasMany
    {
        return $this->hasMany(PreMatriculaHistorico::class)->latest('alterado_em');
    }
}
