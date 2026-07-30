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
        $usuarios = User::with('escola')->orderBy('name')->paginate(20);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $escolas = Escola::orderBy('nome')->get();

        return view('admin.usuarios.create', compact('escolas'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'name'      => 'required|string|max:150',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
            'perfil'    => 'required|in:Admin,Operador',
            'escola_id' => 'nullable|exists:escolas,id',
        ]);

        User::create([
            'name'      => $dados['name'],
            'email'     => $dados['email'],
            'password'  => Hash::make($dados['password']),
            'perfil'    => $dados['perfil'],
            'escola_id' => $dados['escola_id'] ?? null,
        ]);

        return redirect()->route('admin.usuarios.index')->with('sucesso', 'Usuário criado com sucesso.');
    }
}
