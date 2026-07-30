@extends('layouts.public')

@section('titulo', 'Comprovante de pré-matrícula')

@section('conteudo')
<div class="bg-white p-4 rounded border">
    <h5 class="mb-3">Comprovante de pré-matrícula</h5>

    <table class="table">
        <tr><td class="text-secondary">Protocolo</td><td class="text-end fw-semibold">{{ $preMatricula->protocolo }}</td></tr>
        <tr><td class="text-secondary">Aluno</td><td class="text-end">{{ $preMatricula->aluno->nome }}</td></tr>
        <tr><td class="text-secondary">Escola</td><td class="text-end">{{ $preMatricula->escola->nome }}</td></tr>
        <tr><td class="text-secondary">Ano/série</td><td class="text-end">{{ $preMatricula->ano_letivo }} · {{ $preMatricula->nivel_ensino }}</td></tr>
        <tr><td class="text-secondary">Turno</td><td class="text-end">{{ $preMatricula->turno }}</td></tr>
        <tr><td class="text-secondary">Local e data</td><td class="text-end">Cafarnaum/BA, {{ $preMatricula->created_at->format('d/m/Y') }}</td></tr>
    </table>

    <button class="btn btn-outline-primary w-100" onclick="window.print()">Imprimir comprovante</button>
</div>
@endsection
