@extends('layouts.public')

@section('titulo', 'Iniciar pré-matrícula')

@section('conteudo')
<form method="POST" action="{{ route('pre-matricula.verificar') }}" class="bg-white p-4 rounded border">
    @csrf
    <h5 class="mb-2">Pré-matrícula · ano letivo {{ $anoLetivo }}</h5>
    <p class="text-secondary small mb-4">
        Antes de começar, precisamos confirmar se o aluno já possui algum cadastro no sistema
        (de uma pré-matrícula de anos anteriores).
    </p>

    <div class="mb-3">
        <label class="form-label">Nome completo do aluno</label>
        <input type="text" name="nome" class="form-control" value="{{ old('nome') }}" required>
    </div>

    <div class="mb-4">
        <label class="form-label">Data de nascimento</label>
        <input type="date" name="data_nascimento" class="form-control" value="{{ old('data_nascimento') }}" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Continuar</button>
</form>
@endsection
