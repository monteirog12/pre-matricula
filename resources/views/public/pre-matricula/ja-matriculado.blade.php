@extends('layouts.public')

@section('titulo', 'Aluno já matriculado')

@section('conteudo')
<div class="bg-white p-4 rounded border">
    <h5 class="mb-3">Este aluno já possui uma pré-matrícula</h5>
    <p class="text-secondary">
        Encontramos uma pré-matrícula para <strong>{{ $jaMatriculado->aluno->nome }}</strong>
        no ano letivo de {{ $jaMatriculado->ano_letivo }}.
    </p>

    <table class="table">
        <tr><td class="text-secondary">Protocolo</td><td class="text-end fw-semibold">{{ $jaMatriculado->protocolo }}</td></tr>
        <tr><td class="text-secondary">Escola</td><td class="text-end">{{ $jaMatriculado->escola->nome }}</td></tr>
        <tr><td class="text-secondary">Status</td><td class="text-end">{{ $jaMatriculado->status }}</td></tr>
    </table>

    <a href="{{ route('pre-matricula.comprovante', $jaMatriculado->protocolo) }}" class="btn btn-outline-primary w-100">
        Ver comprovante
    </a>

    <p class="text-secondary small mt-3 mb-0">
        Se você acredita que algum dado dessa matrícula precisa ser corrigido, entre em contato
        com a Secretaria de Educação — não é possível criar uma nova pré-matrícula para um aluno
        que já está registrado neste ano letivo.
    </p>
</div>
@endsection
