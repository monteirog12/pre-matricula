<?php

namespace App\Console\Commands;

use App\Models\Aluno;
use App\Models\Escola;
use App\Models\PreMatricula;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportarCensoEscolar extends Command
{
    // Uso: php artisan importar:censo caminho/arquivo.csv 3
    // (o "3" é o ID da escola - confira em /admin, tela de escolas,
    // ou via: php artisan tinker --execute="dd(App\Models\Escola::pluck('nome','id'));")
    protected $signature = 'importar:censo {csv} {escola_id} {ano_letivo=2026}';

    protected $description = 'Importa o CSV do censo escolar de uma escola para as tabelas alunos/pre_matriculas, eliminando duplicidade de aluno (AEE/atividade complementar).';

    public function handle(): int
    {
        $caminho = $this->argument('csv');
        $escolaId = (int) $this->argument('escola_id');
        $anoLetivo = (int) $this->argument('ano_letivo');

        if (! file_exists($caminho)) {
            $this->error("Arquivo não encontrado: {$caminho}");
            return self::FAILURE;
        }

        $escola = Escola::find($escolaId);
        if (! $escola) {
            $this->error("Escola com ID {$escolaId} não encontrada.");
            return self::FAILURE;
        }

        $this->info("Importando para a escola: {$escola->nome} (ano letivo {$anoLetivo})");

        $linhas = $this->lerCsv($caminho);

        // Elimina duplicidade: um mesmo aluno pode aparecer em mais de
        // uma linha (turma principal + AEE + atividade complementar).
        // Mantemos so a linha da turma principal, identificada por ter
        // uma "Etapa de ensino" preenchida (diferente de "Não se aplica").
        $porAluno = [];
        foreach ($linhas as $linha) {
            $id = $linha['Identificação única'] ?? null;
            if (! $id) {
                continue;
            }
            $etapa = trim($linha['Etapa de ensino'] ?? '');
            $ehPrincipal = $etapa !== '' && strcasecmp($etapa, 'Não se aplica') !== 0;

            if (! isset($porAluno[$id]) || $ehPrincipal) {
                $porAluno[$id] = $linha;
            }
        }

        $criados = 0;
        $reaproveitados = 0;
        $puladosJaMatriculados = 0;
        $erros = [];

        foreach ($porAluno as $identificacaoUnica => $linha) {
            try {
                DB::transaction(function () use ($linha, $escolaId, $anoLetivo, &$criados, &$reaproveitados, &$puladosJaMatriculados) {
                    $nome = trim($linha['Nome'] ?? '');
                    $dataNascimentoBr = trim($linha['Data de nascimento'] ?? '');

                    if ($nome === '' || $dataNascimentoBr === '') {
                        throw new \RuntimeException("Linha sem nome ou data de nascimento válidos.");
                    }

                    $dataNascimento = \Carbon\Carbon::createFromFormat('d/m/Y', $dataNascimentoBr)->format('Y-m-d');

                    // Reaproveita o aluno se ja existir (mesmo nome + data
                    // de nascimento) - evita duplicar caso o comando seja
                    // rodado mais de uma vez com o mesmo arquivo.
                    $aluno = Aluno::where('nome', $nome)
                        ->where('data_nascimento', $dataNascimento)
                        ->first();

                    if ($aluno) {
                        $reaproveitados++;
                    } else {
                        [$naturalidade, $uf] = $this->separarMunicipioUf($linha['Município-UF de nascimento'] ?? '');

                        $aluno = Aluno::create([
                            'nome' => $nome,
                            'data_nascimento' => $dataNascimento,
                            'naturalidade' => $naturalidade,
                            'uf_naturalidade' => $uf,
                            'possui_necessidade_especial' => $this->possuiNecessidade($linha),
                            'descricao_necessidade' => $this->descricaoNecessidade($linha),
                        ]);
                        $criados++;
                    }

                    // Nao cria pre-matricula duplicada se este aluno ja
                    // tiver uma pre-matricula neste ano letivo.
                    $jaExiste = PreMatricula::where('aluno_id', $aluno->id)
                        ->where('ano_letivo', $anoLetivo)
                        ->exists();

                    if ($jaExiste) {
                        $puladosJaMatriculados++;
                        return;
                    }

                    PreMatricula::create([
                        'ano_letivo' => $anoLetivo,
                        'aluno_id' => $aluno->id,
                        'escola_id' => $escolaId,
                        'tipo_aluno' => 'Aluno da Casa',
                        'nivel_ensino' => $this->mapearNivelEnsino($linha['Etapa de ensino'] ?? ''),
                        'turno' => $this->mapearTurno($linha['Etapa de ensino'] ?? ''),
                        'situacao' => 'Promovido',
                        // "Serie que sera Matriculado" - Nome da turma do arquivo,
                        // ja que esta importacao representa a matricula de 2026.
                        'serie_ano_anterior' => trim($linha['Nome da turma'] ?? ''),
                        'participa_programa_federal' => false,
                        'observacoes' => $this->montarObservacoes($linha),
                        'status' => 'Confirmada',
                    ]);
                });
            } catch (\Throwable $e) {
                $erros[] = "{$linha['Nome']} ({$identificacaoUnica}): " . $e->getMessage();
            }
        }

        $this->newLine();
        $this->info("Alunos novos criados: {$criados}");
        $this->info("Alunos reaproveitados (já existiam): {$reaproveitados}");
        $this->info("Pulados (já tinham pré-matrícula neste ano): {$puladosJaMatriculados}");

        if ($erros) {
            $this->warn(count($erros) . " linha(s) com erro:");
            foreach ($erros as $erro) {
                $this->line(" - {$erro}");
            }
        }

        return self::SUCCESS;
    }

    private function lerCsv(string $caminho): array
    {
        $conteudo = file_get_contents($caminho);
        // Corrige encoding: arquivos do censo escolar frequentemente
        // vem em ISO-8859-1 (Latin1), nao em UTF-8.
        $conteudo = mb_convert_encoding($conteudo, 'UTF-8', mb_detect_encoding($conteudo, ['UTF-8', 'ISO-8859-1'], true) ?: 'ISO-8859-1');

        $linhasTexto = preg_split('/\r\n|\r|\n/', trim($conteudo));
        $cabecalho = str_getcsv(array_shift($linhasTexto), ';');

        $linhas = [];
        foreach ($linhasTexto as $linhaTexto) {
            if (trim($linhaTexto) === '') {
                continue;
            }
            $valores = str_getcsv($linhaTexto, ';');
            $linhas[] = array_combine($cabecalho, $valores);
        }

        return $linhas;
    }

    private function separarMunicipioUf(string $texto): array
    {
        $texto = trim($texto);
        if (preg_match('/^(.*)\s-\s([A-Z]{2})$/', $texto, $m)) {
            return [trim($m[1]), $m[2]];
        }
        return [$texto ?: null, null];
    }

    private function possuiNecessidade(array $linha): bool
    {
        $deficiencia = trim($linha['Tipo(s) de deficiência(s), transtorno(s) do espectro autista e altas habilidades ou superdotação'] ?? '');
        return $deficiencia !== '' && $deficiencia !== '--';
    }

    private function descricaoNecessidade(array $linha): ?string
    {
        $partes = [];
        foreach ([
            'Tipo(s) de deficiência(s), transtorno(s) do espectro autista e altas habilidades ou superdotação',
            'Tipo(s) de transtorno(s) que impacta(m) o desenvolvimento da aprendizagem',
            'Recursos para o uso do(a) aluno(a) em sala de aula para a participação em avaliações do Inep (Saeb)',
        ] as $campo) {
            $valor = trim($linha[$campo] ?? '');
            if ($valor !== '' && $valor !== '--') {
                $partes[] = $valor;
            }
        }
        return $partes ? implode(' | ', $partes) : null;
    }

    private function mapearNivelEnsino(string $etapa): string
    {
        $etapa = mb_strtolower($etapa);
        if (str_contains($etapa, 'eja')) {
            return 'EJA';
        }
        if (str_contains($etapa, 'infantil')) {
            return 'Educacao Infantil';
        }
        return 'Ensino Fundamental';
    }

    private function mapearTurno(string $etapa): string
    {
        // EJA so pode ser Noturno (regra ja validada no sistema);
        // os demais niveis entram como Matutino por padrao - ajustar
        // manualmente depois, turma por turma, se necessario.
        return str_contains(mb_strtolower($etapa), 'eja') ? 'Noturno' : 'Matutino';
    }

    private function montarObservacoes(array $linha): string
    {
        $cpf = trim($linha['CPF'] ?? '');
        $cpf = ($cpf !== '' && $cpf !== '--') ? $cpf : 'não informado';

        $zona = trim($linha['Localização/Zona de residência'] ?? '--');
        $sexo = trim($linha['Sexo'] ?? '--');
        $corRaca = trim($linha['Cor/Raça'] ?? '--');
        $transporte = trim($linha['Transporte escolar (Sim/Não)'] ?? '--');
        $poderPublico = trim($linha['Poder Público responsável'] ?? '');
        $veiculo = trim($linha['Tipo de veículo utilizado no transporte escolar'] ?? '');
        $codigoAntigo = trim($linha['Código da Matrícula'] ?? '');

        $transporteTexto = $transporte;
        if (strcasecmp($transporte, 'Sim') === 0 && ($poderPublico || $veiculo)) {
            $transporteTexto .= " ({$poderPublico}/{$veiculo})";
        }

        return "[Dados do Censo Escolar 2026 - importado] "
            . "CPF: {$cpf} | Sexo: {$sexo} | Cor/Raça: {$corRaca} | "
            . "Zona de residência: {$zona} | Transporte escolar: {$transporteTexto} | "
            . "Cód. matrícula (sistema anterior): {$codigoAntigo}";
    }
}
