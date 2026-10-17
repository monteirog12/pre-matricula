<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Escola;
use App\Models\PreMatricula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreMatriculaController extends Controller
{
    private function anoLetivoAtivo(): int
    {
        return (int) (DB::table('configuracoes')->where('chave', 'ano_letivo_ativo')->value('valor') ?? date('Y'));
    }

    private function escolasPermitidas(): ?array
    {
        $usuario = auth()->user();

        if ($usuario->perfil === 'Admin') {
            return null;
        }

        return $usuario->escolas->pluck('id')->all();
    }

    private function queryFiltrada(Request $request)
    {
        $anoLetivo = $request->input('ano_letivo', $this->anoLetivoAtivo());

        $query = PreMatricula::with(['aluno', 'escola'])
            ->where('ano_letivo', $anoLetivo);

        $permitidas = $this->escolasPermitidas();
        if ($permitidas !== null) {
            $query->whereIn('escola_id', $permitidas ?: [0]);
        }

        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $buscaTelefone = preg_replace('/\D/', '', $busca);

            $query->where(function ($q) use ($busca, $buscaTelefone) {
                $q->where('protocolo', 'like', "%{$busca}%")
                  ->orWhereHas('aluno', function ($q2) use ($busca, $buscaTelefone) {
                      $q2->where('nome', 'like', "%{$busca}%");
                      if ($buscaTelefone !== '') {
                          $q2->orWhere('telefone', 'like', "%{$buscaTelefone}%");
                      }
                  });
            });
        }

        if ($request->filled('escola_id')) {
            $query->where('escola_id', $request->input('escola_id'));
        }
        if ($request->filled('nivel_ensino')) {
            $query->where('nivel_ensino', $request->input('nivel_ensino'));
        }
        if ($request->filled('turno')) {
            $query->where('turno', $request->input('turno'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return $query;
    }

    public function index(Request $request)
    {
        $anoLetivo = $request->input('ano_letivo', $this->anoLetivoAtivo());

        $preMatriculas = $this->queryFiltrada($request)->latest()->paginate(20)->withQueryString();

        $permitidas = $this->escolasPermitidas();
        $escolas = $permitidas === null
            ? Escola::orderBy('nome')->get()
            : Escola::whereIn('id', $permitidas ?: [0])->orderBy('nome')->get();

        return view('admin.pre-matriculas.index', compact('preMatriculas', 'escolas', 'anoLetivo'));
    }

    public function exportar(Request $request)
    {
        $preMatriculas = $this->queryFiltrada($request)->latest()->get();

        $nomeArquivo = 'pre-matriculas-' . $request->input('ano_letivo', $this->anoLetivoAtivo()) . '.csv';

        return response()->streamDownload(function () use ($preMatriculas) {
            $saida = fopen('php://output', 'w');
            fwrite($saida, "\xEF\xBB\xBF");

            fputcsv($saida, [
                'Protocolo', 'Ano letivo', 'Status', 'Nome do aluno', 'Data de nascimento',
                'Telefone', 'Endereço', 'RG', 'Naturalidade', 'UF', 'Nome do pai', 'Profissão do pai',
                'Nome da mãe', 'Profissão da mãe', 'Necessidade especial', 'Descrição da necessidade',
                'Escola', 'Zona', 'Nível de ensino', 'Turno', 'Situação', 'Série que será matriculado',
                'Participa de programa federal', 'Qual programa', 'NIS', 'Observações', 'Data do cadastro',
            ], ';');

            foreach ($preMatriculas as $pm) {
                fputcsv($saida, [
                    $pm->protocolo,
                    $pm->ano_letivo,
                    $pm->status,
                    $pm->aluno->nome,
                    $pm->aluno->data_nascimento->format('d/m/Y'),
                    $pm->aluno->telefone,
                    $pm->aluno->endereco,
                    $pm->aluno->rg,
                    $pm->aluno->naturalidade,
                    $pm->aluno->uf_naturalidade,
                    $pm->aluno->nome_pai,
                    $pm->aluno->profissao_pai,
                    $pm->aluno->nome_mae,
                    $pm->aluno->profissao_mae,
                    $pm->aluno->possui_necessidade_especial ? 'Sim' : 'Não',
                    $pm->aluno->descricao_necessidade,
                    $pm->escola->nome,
                    $pm->escola->zona,
                    $pm->nivel_ensino,
                    $pm->turno,
                    $pm->situacao,
                    $pm->serie_ano_anterior,
                    $pm->participa_programa_federal ? 'Sim' : 'Não',
                    $pm->qual_programa,
                    $pm->nis,
                    $pm->observacoes,
                    $pm->created_at->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($saida);
        }, $nomeArquivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(PreMatricula $preMatricula)
    {
        abort_unless(auth()->user()->podeAcessarEscola($preMatricula->escola_id), 403);

        $preMatricula->load(['aluno', 'escola', 'historico.usuario']);

        return view('admin.pre-matriculas.show', compact('preMatricula'));
    }

    public function update(Request $request, PreMatricula $preMatricula)
    {
        abort_unless(auth()->user()->podeAcessarEscola($preMatricula->escola_id), 403);

        $dados = $request->validate([
            'aluno.nome' => 'required|string|max:150',
            'aluno.telefone' => 'nullable|string|max:20',
            'aluno.endereco' => 'nullable|string|max:255',
            'escola_id' => 'required|exists:escolas,id',
            'turno' => 'required|in:Matutino,Vespertino,Noturno',
            'situacao' => 'required|in:Promovido,Repetente',
            'status' => 'required|in:Pendente,Confirmada,Cancelada',
            'motivo' => 'nullable|string|max:255',
        ]);

        abort_unless(auth()->user()->podeAcessarEscola($dados['escola_id']), 403);

        $turnosValidosPorNivel = [
            'Creche'             => ['Matutino', 'Vespertino'],
            'Educacao Infantil'  => ['Matutino', 'Vespertino'],
            'Ensino Fundamental' => ['Matutino', 'Vespertino'],
            'EJA'                => ['Noturno'],
        ];

        if (! in_array($dados['turno'], $turnosValidosPorNivel[$preMatricula->nivel_ensino] ?? [], true)) {
            return back()->withErrors([
                'turno' => 'Este turno não está disponível para o nível de ensino desta pré-matrícula.',
            ])->withInput();
        }

        $aluno = $preMatricula->aluno;
        $aluno->fill($dados['aluno']);

        foreach (['nome', 'telefone', 'endereco'] as $campo) {
            if ($aluno->isDirty($campo)) {
                $preMatricula->historico()->create([
                    'usuario_id'      => auth()->id(),
                    'campo_alterado'  => 'aluno_' . $campo,
                    'valor_anterior'  => $aluno->getOriginal($campo),
                    'valor_novo'      => $aluno->{$campo},
                    'motivo'          => $dados['motivo'] ?? null,
                ]);
            }
        }

        $aluno->save();

        $preMatricula->motivoAlteracao = $dados['motivo'] ?? null;
        $preMatricula->update([
            'escola_id' => $dados['escola_id'],
            'turno' => $dados['turno'],
            'situacao' => $dados['situacao'],
            'status' => $dados['status'],
        ]);

        return redirect()
            ->route('admin.pre-matriculas.show', $preMatricula)
            ->with('sucesso', 'Pré-matrícula atualizada.');
    }
}
