<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aluno extends Model
{
    protected $fillable = [
        'nome', 'telefone', 'data_nascimento', 'naturalidade', 'uf_naturalidade', 'rg',
        'nome_pai', 'profissao_pai', 'nome_mae', 'profissao_mae', 'endereco',
        'possui_necessidade_especial', 'descricao_necessidade',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'possui_necessidade_especial' => 'boolean',
    ];

    // Remove parenteses, espacos e traco antes de salvar, garantindo
    // que o telefone sempre fique só com dígitos no banco - assim a
    // busca funciona independente de como o responsavel digitou.
    public function setTelefoneAttribute($value): void
    {
        $this->attributes['telefone'] = $value ? preg_replace('/\D/', '', $value) : null;
    }

    public function preMatriculas(): HasMany
    {
        return $this->hasMany(PreMatricula::class);
    }

    // Idade calculada em tempo real - nunca gravada no banco
    public function getIdadeAttribute(): int
    {
        return \Carbon\Carbon::parse($this->data_nascimento)->age;
    }

    // Mascara um nome para exibicao (ex: "Joao Silva Santos" -> "Joao S*** S***"),
    // usado na tela de confirmacao de "aluno da casa" para nao expor o nome
    // completo do responsavel caso a busca encontre a pessoa errada.
    public static function mascarar(?string $nome): string
    {
        if (! $nome) {
            return '—';
        }

        $partes = preg_split('/\s+/', trim($nome));
        $primeiro = array_shift($partes);
        $resto = array_map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)) . '***', $partes);

        return trim($primeiro . ' ' . implode(' ', $resto));
    }
}
