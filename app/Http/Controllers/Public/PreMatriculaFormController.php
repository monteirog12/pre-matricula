<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Escola;
use App\Models\PreMatricula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreMatriculaFormController extends Controller
{
    private function anoLetivoAtivo(): int
    {
        return (int) (DB::table('configuracoes')->where('chave', 'ano_letivo_ativo')->value('valor') ?? date('Y'));
    }

    // Etapa 1: so pede nome + data de nascimento, antes de mostrar o
    // formulario inteiro. E aqui que decidimos Novo x Aluno da Casa.
    public function verificarForm()
    {
        return view('public.pre-matricula.verificar', [
            'anoLetivo' => $this->anoLetivoAtivo(),
        ]);
    }

    public function verificar(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:150',
            'data_nascimento' => 'required|date',
        ]);

        $anoLetivo = $this->anoLetivoAtivo();

        // 1) Ja existe pre-matricula desse aluno neste ano letivo?
        $jaMatriculado = PreMatricula::with(['aluno', 'escola'])
            ->where('ano_letivo', $anoLetivo)
            ->whereHas('aluno', function ($q) use ($dados) {
                $q->where('nome', $dados['nome'])
                  ->where('data_nascimento', $dados['data_nascimento']);
            })
            ->first();

        if ($jaMatriculado) {
            return view('public.pre-matricula.ja-matriculado', compact('jaMatriculado'));
        }

        // 2) Existe cadastro de anos anteriores (aluno da casa)?
        $candidatos = Aluno::where('nome', $dados['nome'])
            ->where('data_nascimento', $dados['data_nascimento'])
            ->get();

        if ($candidatos->isNotEmpty()) {
            return view('public.pre-matricula.confirmar-aluno', [
                'candidatos' => $candidatos,
                'nomeBuscado' => $dados['nome'],
                'dataBuscada' => $dados['data_nascimento'],
            ]);
        }

        // 3) Nao encontrou nada: segue como aluno novo, ja com os
        // 2 campos preenchidos levados adiante.
        return redirect()->route('pre-matricula.formulario', [
            'nome' => $dados['nome'],
            'data_nascimento' => $dados['data_nascimento'],
            'tipo_aluno' => 'Novo',
        ]);
    }

    // Formulario completo - aluno novo (em branco) ou aluno da casa
    // confirmado (pre-preenchido, exceto dados escolares).
    public function create(Request $request)
    {
        $escolas = Escola::where('ativo', true)->orderBy('nome')->get();

        $alunoId = $request->query('aluno_id');
        $alunoConfirmado = $alunoId ? Aluno::find($alunoId) : null;

        return view('public.pre-matricula.form', [
            'anoLetivo' => $this->anoLetivoAtivo(),
            'escolas' => $escolas,
            'tipoAluno' => $alunoConfirmado ? 'Aluno da Casa' : 'Novo',
            'alunoConfirmado' => $alunoConfirmado,
            'nomePreenchido' => $alunoConfirmado?->nome ?? $request->query('nome'),
            'dataNascimentoPreenchida' => $alunoConfirmado?->data_nascimento?->format('Y-m-d') ?? $request->query('data_nascimento'),
        ]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'aluno_id'           => 'nullable|exists:alunos,id',
            'tipo_aluno'         => 'required|in:Novo,Aluno da Casa',
            'nome'               => 'required|string|max:150',
            'data_nascimento'    => 'required|date',
            'telefone'           => 'nullable|string|max:20',
            'naturalidade'       => 'nullable|string|max:100',
            'uf_naturalidade'    => 'nullable|string|max:2',
            'rg'                 => 'nullable|string|max:20',
            'nome_pai'           => 'nullable|string|max:150',
            'profissao_pai'      => 'nullable|string|max:100',
            'nome_mae'           => 'nullable|string|max:150',
            'profissao_mae'      => 'nullable|string|max:100',
            'endereco'           => 'nullable|string|max:255',
            'possui_necessidade_especial' => 'boolean',
            'descricao_necessidade'       => 'nullable|string',
            'escola_id'          => 'required|exists:escolas,id',
            'nivel_ensino'       => 'required|in:Educacao Infantil,Ensino Fundamental,EJA',
            'turno'              => 'required|in:Matutino,Vespertino,Noturno',
            'situacao'           => 'required|in:Promovido,Repetente',
            'serie_ano_anterior' => 'nullable|string|max:50',
            'participa_programa_federal' => 'boolean',
            'qual_programa'      => 'nullable|string|max:100',
            'nis'                => 'nullable|string|max:20',
            'observacoes'        => 'nullable|string',
        ]);

        // Regra de negocio: turno exclusivo por nivel de ensino.
        $turnosValidosPorNivel = [
            'Educacao Infantil'  => ['Matutino', 'Vespertino'],
            'Ensino Fundamental' => ['Matutino', 'Vespertino'],
            'EJA'                => ['Noturno'],
        ];

        if (! in_array($dados['turno'], $turnosValidosPorNivel[$dados['nivel_ensino']] ?? [], true)) {
            return back()->withErrors([
                'turno' => 'Este turno não está disponível para o nível de ensino selecionado.',
            ])->withInput();
        }

        $anoLetivo = $this->anoLetivoAtivo();

        // Trava de seguranca: mesmo que o aluno_id venha do formulario,
        // confere de novo (no servidor) se ja nao existe pre-matricula
        // pra esse aluno neste ano - evita duplicidade mesmo que o pai
        // tenha aberto o formulario em duas abas, por exemplo.
        if (! empty($dados['aluno_id'])) {
            $existe = PreMatricula::where('aluno_id', $dados['aluno_id'])
                ->where('ano_letivo', $anoLetivo)
                ->exists();

            if ($existe) {
                return back()->withErrors(['nome' => 'Este aluno já possui uma pré-matrícula para o ano letivo atual.']);
            }
        }

        $preMatricula = DB::transaction(function () use ($dados, $anoLetivo) {
            if (! empty($dados['aluno_id'])) {
                // Aluno da casa: reaproveita o cadastro, atualizando so
                // os dados que podem ter mudado de um ano pro outro.
                $aluno = Aluno::findOrFail($dados['aluno_id']);
                $aluno->update([
                    'telefone' => $dados['telefone'] ?? null,
                    'endereco' => $dados['endereco'] ?? null,
                    'possui_necessidade_especial' => $dados['possui_necessidade_especial'] ?? false,
                    'descricao_necessidade' => $dados['descricao_necessidade'] ?? null,
                ]);
            } else {
                $aluno = Aluno::create([
                    'nome' => $dados['nome'],
                    'telefone' => $dados['telefone'] ?? null,
                    'data_nascimento' => $dados['data_nascimento'],
                    'naturalidade' => $dados['naturalidade'] ?? null,
                    'uf_naturalidade' => $dados['uf_naturalidade'] ?? null,
                    'rg' => $dados['rg'] ?? null,
                    'nome_pai' => $dados['nome_pai'] ?? null,
                    'profissao_pai' => $dados['profissao_pai'] ?? null,
                    'nome_mae' => $dados['nome_mae'] ?? null,
                    'profissao_mae' => $dados['profissao_mae'] ?? null,
                    'endereco' => $dados['endereco'] ?? null,
                    'possui_necessidade_especial' => $dados['possui_necessidade_especial'] ?? false,
                    'descricao_necessidade' => $dados['descricao_necessidade'] ?? null,
                ]);
            }

            return PreMatricula::create([
                'ano_letivo' => $anoLetivo,
                'aluno_id' => $aluno->id,
                'escola_id' => $dados['escola_id'],
                'tipo_aluno' => $dados['tipo_aluno'],
                'nivel_ensino' => $dados['nivel_ensino'],
                'turno' => $dados['turno'],
                'situacao' => $dados['situacao'],
                'serie_ano_anterior' => $dados['serie_ano_anterior'] ?? null,
                'participa_programa_federal' => $dados['participa_programa_federal'] ?? false,
                'qual_programa' => $dados['qual_programa'] ?? null,
                'nis' => $dados['nis'] ?? null,
                'observacoes' => $dados['observacoes'] ?? null,
            ]);
        });

        return redirect()
            ->route('pre-matricula.comprovante', $preMatricula->protocolo)
            ->with('sucesso', true);
    }

    public function comprovante(string $protocolo)
    {
        $preMatricula = PreMatricula::with(['aluno', 'escola'])
            ->where('protocolo', $protocolo)
            ->firstOrFail();

        return view('public.pre-matricula.comprovante', compact('preMatricula'));
    }
}
