<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Escola;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with('escolas')->orderBy('name')->paginate(20);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $escolas = Escola::orderBy('nome')->get();

        return view('admin.usuarios.create', compact('escolas'));
    }

    public function store(Request $request)
    {
        $dados = $this->validarDados($request, senhaObrigatoria: true);

        if ($erro = $this->validarEscolasObrigatorias($dados)) {
            return back()->withErrors(['escolas' => $erro])->withInput();
        }

        $usuario = User::create([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'password' => Hash::make($dados['password']),
            'perfil' => $dados['perfil'],
            'ativo' => $dados['ativo'],
        ]);

        $usuario->escolas()->sync($dados['escolas'] ?? []);

        return redirect()->route('admin.usuarios.index')->with('sucesso', 'Usuário criado com sucesso.');
    }

    public function edit(User $usuario)
    {
        $escolas = Escola::orderBy('nome')->get();
        $usuario->load('escolas');

        return view('admin.usuarios.edit', compact('usuario', 'escolas'));
    }

    public function update(Request $request, User $usuario)
    {
        $dados = $this->validarDados($request, senhaObrigatoria: false, usuarioId: $usuario->id);

        if ($erro = $this->validarEscolasObrigatorias($dados)) {
            return back()->withErrors(['escolas' => $erro])->withInput();
        }

        $usuario->name = $dados['name'];
        $usuario->email = $dados['email'];
        $usuario->perfil = $dados['perfil'];
        $usuario->ativo = $dados['ativo'];

        if (! empty($dados['password'])) {
            $usuario->password = Hash::make($dados['password']);
        }

        $usuario->save();
        $usuario->escolas()->sync($dados['escolas'] ?? []);

        return redirect()->route('admin.usuarios.index')->with('sucesso', 'Usuário atualizado com sucesso.');
    }

    // Ativa/inativa em vez de excluir por padrao - preserva o historico
    // de alteracoes feitas por esse usuario (quem alterou o que).
    public function alternarStatus(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->withErrors(['ativo' => 'Você não pode inativar seu próprio usuário.']);
        }

        $usuario->update(['ativo' => ! $usuario->ativo]);

        return back()->with('sucesso', $usuario->ativo ? 'Usuário reativado.' : 'Usuário inativado.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->withErrors(['ativo' => 'Você não pode excluir seu próprio usuário.']);
        }

        $usuario->escolas()->detach();
        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('sucesso', 'Usuário excluído.');
    }

    private function validarDados(Request $request, bool $senhaObrigatoria, ?int $usuarioId = null): array
    {
        $regrasSenha = $senhaObrigatoria ? 'required|string|min:8|confirmed' : 'nullable|string|min:8|confirmed';

        return $request->validate([
            'name'      => 'required|string|max:150',
            'email'     => 'required|email|unique:users,email' . ($usuarioId ? ",{$usuarioId}" : ''),
            'password'  => $regrasSenha,
            'perfil'    => 'required|in:Admin,Operador',
            'ativo'     => 'required|boolean',
            'escolas'   => 'nullable|array',
            'escolas.*' => 'exists:escolas,id',
        ]);
    }

    // Operador precisa de pelo menos 1 escola vinculada, senao ele loga
    // e nao enxerga nada em lugar nenhum do sistema.
    private function validarEscolasObrigatorias(array $dados): ?string
    {
        if ($dados['perfil'] === 'Operador' && empty($dados['escolas'])) {
            return 'Selecione ao menos uma escola para um usuário com perfil Operador.';
        }

        return null;
    }
}
