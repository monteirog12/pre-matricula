<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfiguracaoController extends Controller
{
    public function edit()
    {
        $anoLetivoAtivo = (int) (DB::table('configuracoes')->where('chave', 'ano_letivo_ativo')->value('valor') ?? date('Y'));

        return view('admin.configuracoes.edit', compact('anoLetivoAtivo'));
    }

    public function update(Request $request)
    {
        $dados = $request->validate([
            'ano_letivo_ativo' => 'required|integer|min:2000|max:2100',
        ]);

        DB::table('configuracoes')->updateOrInsert(
            ['chave' => 'ano_letivo_ativo'],
            ['valor' => (string) $dados['ano_letivo_ativo'], 'updated_at' => now()]
        );

        return redirect()
            ->route('admin.configuracoes.edit')
            ->with('sucesso', 'Ano letivo ativo atualizado para ' . $dados['ano_letivo_ativo'] . '.');
    }
}
