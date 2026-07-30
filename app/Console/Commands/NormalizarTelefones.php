<?php

namespace App\Console\Commands;

use App\Models\Aluno;
use Illuminate\Console\Command;

class NormalizarTelefones extends Command
{
    protected $signature = 'telefones:normalizar';

    protected $description = 'Remove mascara (parenteses, espacos, traco) dos telefones ja cadastrados, deixando so os digitos.';

    public function handle(): int
    {
        $total = 0;

        Aluno::chunk(100, function ($alunos) use (&$total) {
            foreach ($alunos as $aluno) {
                // Reatribuir o mesmo valor aciona o setTelefoneAttribute()
                // do model, que normaliza (remove tudo que nao e digito).
                $aluno->telefone = $aluno->telefone;
                $aluno->save();
                $total++;
            }
        });

        $this->info("Telefones normalizados: {$total} aluno(s) verificado(s).");

        return self::SUCCESS;
    }
}
