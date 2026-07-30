@extends('layouts.public')

@section('titulo', 'Confirmar cadastro')

@section('conteudo')
<div class="bg-white p-4 rounded border">
    <h5 class="mb-2">Encontramos um cadastro</h5>
    <p class="text-secondary small mb-4">
        Confira abaixo se este é o cadastro do seu filho(a). Assim você não precisa preencher
        os dados todos de novo.
    </p>

    @foreach ($candidatos as $candidato)
        <div class="border rounded p-3 mb-3">
            <p class="mb-1 fw-semibold">{{ $candidato->nome }}</p>
            <p class="mb-1 small text-secondary">Nascido em {{ $candidato->data_nascimento->format('d/m/Y') }}</p>
            @if ($candidato->nome_pai)
                <p class="mb-1 small text-secondary">Pai: {{ \App\Models\Aluno::mascarar($candidato->nome_pai) }}</p>
            @endif
            @if ($candidato->nome_mae)
                <p class="mb-1 small text-secondary">Mãe: {{ \App\Models\Aluno::mascarar($candidato->nome_mae) }}</p>
            @endif

            <form method="GET" action="{{ route('pre-matricula.formulario') }}" class="mt-2">
                <input type="hidden" name="aluno_id" value="{{ $candidato->id }}">
                <button type="submit" class="btn btn-primary w-100">Sim, é esse aluno</button>
            </form>
        </div>
    @endforeach

    <a href="{{ route('pre-matricula.formulario', ['nome' => $nomeBuscado, 'data_nascimento' => $dataBuscada, 'tipo_aluno' => 'Novo']) }}"
       class="btn btn-outline-secondary w-100 mt-2">
        Não é nenhum desses, é um aluno novo
    </a>
</div>
@endsection
