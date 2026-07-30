<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Escola;
use App\Models\PreMatricula;
use Illuminate\Http\Request;

class PreMatriculaController extends Controller
{
    // Listagem com os filtros do painel: ano, escola, nivel, turno, status
    public function index(Request $request)
    {
        $anoLetivo = $request->input('ano_letivo', date('Y'));

        $query = PreMatricula::with(['aluno', 'escola'])
            ->where('ano_letivo', $anoLetivo);

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

        $preMatriculas = $query->latest()->paginate(20)->withQueryString();
        $escolas = Escola::orderBy('nome')->get();

        return view('admin.pre-matriculas.index', compact('preMatriculas', 'escolas', 'anoLetivo'));
    }

    // Tela de detalhe: mostra TODOS os campos do aluno + dados escolares
    // + o historico de alteracoes daquela pre-matricula.
    public function show(PreMatricula $preMatricula)
    {
        $preMatricula->load(['aluno', 'escola', 'historico.usuario']);

        return view('admin.pre-matriculas.show', compact('preMatricula'));
    }

    // Atualiza dados do aluno e/ou da pre-matricula.
    // O model PreMatricula ja registra automaticamente no historico
    // (evento "updating") os campos auditados que mudarem.
    public function update(Request $request, PreMatricula $preMatricula)
    {
        $dados = $request->validate([
            // dados do aluno (podem ser corrigidos pelo operador)
            'aluno.nome' => 'required|string|max:150',
            'aluno.telefone' => 'nullable|string|max:20',
            'aluno.endereco' => 'nullable|string|max:255',

            // dados da pre-matricula
            'escola_id' => 'required|exists:escolas,id',
            'turno' => 'required|in:Matutino,Vespertino,Noturno',
            'situacao' => 'required|in:Promovido,Repetente',
            'status' => 'required|in:Pendente,Confirmada,Cancelada',
            'motivo' => 'nullable|string|max:255',
        ]);

        // Regra de negocio: turno exclusivo por nivel de ensino
        // (o nivel de ensino em si nao e editavel nesta tela).
        $turnosValidosPorNivel = [
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

        // Audita as mudancas nos dados do aluno antes de salvar,
        // usando o mesmo historico da pre-matricula em questao.
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
