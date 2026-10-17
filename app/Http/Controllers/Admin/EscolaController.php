<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Escola;
use Illuminate\Http\Request;

class EscolaController extends Controller
{
    public function index(Request $request)
    {
        $query = Escola::query();

        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('codigo_inep', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('zona')) {
            $query->where('zona', $request->input('zona'));
        }

        if ($request->filled('status')) {
            $query->where('ativo', $request->input('status') === 'Ativa' ? 1 : 0);
        }

        $escolas = $query->orderBy('nome')->paginate(20)->withQueryString();

        return view('admin.escolas.index', compact('escolas'));
    }

    public function create()
    {
        return view('admin.escolas.create');
    }

    public function store(Request $request)
    {
        $dados = $this->validarDados($request);

        Escola::create($dados);

        return redirect()
            ->route('admin.escolas.index')
            ->with('sucesso', 'Escola cadastrada com sucesso.');
    }

    public function edit(Escola $escola)
    {
        return view('admin.escolas.edit', compact('escola'));
    }

    public function update(Request $request, Escola $escola)
    {
        $dados = $this->validarDados($request, $escola->id);

        $escola->update($dados);

        return redirect()
            ->route('admin.escolas.index')
            ->with('sucesso', 'Escola atualizada com sucesso.');
    }

    private function validarDados(Request $request, ?int $escolaId = null): array
    {
        $dados = $request->validate([
            'nome'          => 'required|string|max:150',
            'codigo_inep'   => 'nullable|string|max:20|unique:escolas,codigo_inep' . ($escolaId ? ",{$escolaId}" : ''),
            'endereco'      => 'nullable|string|max:255',
            'responsavel'   => 'nullable|string|max:150',
            'telefone'      => 'nullable|string|max:20',
            'zona'          => 'required|in:Urbana,Rural',
            'ativo'         => 'required|boolean',
        ]);

        return $dados;
    }
}
