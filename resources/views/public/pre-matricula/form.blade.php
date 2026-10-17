@extends('layouts.public')

@section('titulo', 'Nova pré-matrícula')

@section('conteudo')
<form method="POST" action="{{ route('pre-matricula.store') }}" class="bg-white p-4 rounded border">
    @csrf
    @if ($alunoConfirmado)
        <input type="hidden" name="aluno_id" value="{{ $alunoConfirmado->id }}">
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Pré-matrícula · ano letivo {{ $anoLetivo }}</h5>
    </div>

    <h6 class="text-secondary mt-4">Tipo de aluno</h6>
    <p class="small text-secondary mb-2">Identificado automaticamente com base no cadastro informado na etapa anterior.</p>
    <div class="d-flex gap-3 mb-3" style="pointer-events:none; opacity:0.75">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo_aluno" value="Novo" id="tipoNovo"
                   {{ $tipoAluno == 'Novo' ? 'checked' : '' }}>
            <label class="form-check-label" for="tipoNovo">Aluno novo</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo_aluno" value="Aluno da Casa" id="tipoCasa"
                   {{ $tipoAluno == 'Aluno da Casa' ? 'checked' : '' }}>
            <label class="form-check-label" for="tipoCasa">Aluno da casa</label>
        </div>
    </div>

    <h6 class="text-secondary mt-4">Dados do aluno</h6>
    <div class="row g-3 mb-3">
        <div class="col-12">
            <label class="form-label">Nome completo</label>
            <input type="text" name="nome" class="form-control" value="{{ old('nome', $nomePreenchido) }}"
                   {{ $alunoConfirmado ? 'readonly' : '' }} required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Data de nascimento</label>
            <input type="date" name="data_nascimento" id="dataNascimento" class="form-control"
                   value="{{ old('data_nascimento', $dataNascimentoPreenchida) }}"
                   {{ $alunoConfirmado ? 'readonly' : '' }} required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Idade</label>
            <input type="text" id="idadeCalculada" class="form-control" disabled placeholder="calculada"
                   value="{{ $alunoConfirmado ? $alunoConfirmado->idade . ' anos' : '' }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" class="form-control" value="{{ old('telefone', $alunoConfirmado->telefone ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">RG do Aluno</label>
            <input type="text" name="rg" class="form-control" value="{{ old('rg', $alunoConfirmado->rg ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Naturalidade</label>
            <input type="text" name="naturalidade" class="form-control" value="{{ old('naturalidade', $alunoConfirmado->naturalidade ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">UF</label>
            <input type="text" name="uf_naturalidade" maxlength="2" class="form-control" value="{{ old('uf_naturalidade', $alunoConfirmado->uf_naturalidade ?? '') }}">
        </div>
        <div class="col-12">
            <label class="form-label">Endereço</label>
            <input type="text" name="endereco" class="form-control" value="{{ old('endereco', $alunoConfirmado->endereco ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Nome do pai</label>
            <input type="text" name="nome_pai" class="form-control" value="{{ old('nome_pai', $alunoConfirmado->nome_pai ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Profissão do pai</label>
            <input type="text" name="profissao_pai" class="form-control" value="{{ old('profissao_pai', $alunoConfirmado->profissao_pai ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Nome da mãe</label>
            <input type="text" name="nome_mae" class="form-control" value="{{ old('nome_mae', $alunoConfirmado->nome_mae ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Profissão da mãe</label>
            <input type="text" name="profissao_mae" class="form-control" value="{{ old('profissao_mae', $alunoConfirmado->profissao_mae ?? '') }}">
        </div>
        <div class="col-12">
            <label class="form-label">Possui alguma necessidade especial?</label>
            <select name="possui_necessidade_especial" id="necessidadeSelect" class="form-select">
                <option value="0">Não</option>
                <option value="1" {{ old('possui_necessidade_especial', $alunoConfirmado->possui_necessidade_especial ?? false) ? 'selected' : '' }}>Sim</option>
            </select>
        </div>
        <div class="col-12" id="necessidadeDetalhe" style="display:none">
            <label class="form-label">Descreva a necessidade especial</label>
            <textarea name="descricao_necessidade" class="form-control">{{ old('descricao_necessidade', $alunoConfirmado->descricao_necessidade ?? '') }}</textarea>
        </div>
    </div>

    <h6 class="text-secondary mt-4">Dados escolares</h6>
    <div class="row g-3 mb-4">
        <div class="col-12">
            <label class="form-label">Escola pretendida</label>
            <select name="escola_id" class="form-select" required>
                <option value="">Selecione...</option>
                @foreach ($escolas as $escola)
                    <option value="{{ $escola->id }}" {{ old('escola_id') == $escola->id ? 'selected' : '' }}>
                        {{ $escola->nome }} ({{ $escola->zona }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Nível de ensino</label>
            <select name="nivel_ensino" id="nivelEnsino" class="form-select" required>
                <option value="Creche">Creche</option>
                <option value="Educacao Infantil">Educação infantil</option>
                <option value="Ensino Fundamental">Ensino fundamental</option>
                <option value="EJA">EJA</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Turno</label>
            <select name="turno" id="turnoSelect" class="form-select" required>
                <option value="Matutino">Matutino</option>
                <option value="Vespertino">Vespertino</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Situação</label>
            <select name="situacao" class="form-select" required>
                <option value="Promovido">Promovido</option>
                <option value="Repetente">Repetente</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Série que será matriculado</label>
            <input type="text" name="serie_ano_anterior" placeholder="ex.: 5º Ano" class="form-control" value="{{ old('serie_ano_anterior') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Participa de programa federal</label>
            <select name="participa_programa_federal" id="programaSelect" class="form-select" onchange="toggleProgramaCampo(this.value)">
                <option value="0">Não</option>
                <option value="1" {{ old('participa_programa_federal') ? 'selected' : '' }}>Sim</option>
            </select>
        </div>
        <div id="programaDetalhe" style="display:none">
            <label class="form-label">Qual programa</label>
            <input type="text" name="qual_programa" class="form-control" value="{{ old('qual_programa') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Número do NIS</label>
            <input type="text" name="nis" class="form-control" value="{{ old('nis') }}">
        </div>
        <div class="col-12">
            <label class="form-label">Alterações / observações</label>
            <textarea name="observacoes" class="form-control">{{ old('observacoes') }}</textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100">Enviar pré-matrícula</button>
</form>

<script>
function calcIdade() {
    const v = document.getElementById('dataNascimento').value;
    if (!v) { document.getElementById('idadeCalculada').value = ''; return; }
    const nasc = new Date(v + 'T00:00:00'), hoje = new Date();
    let idade = hoje.getFullYear() - nasc.getFullYear();
    const m = hoje.getMonth() - nasc.getMonth();
    if (m < 0 || (m === 0 && hoje.getDate() < nasc.getDate())) idade--;
    document.getElementById('idadeCalculada').value = idade + ' anos';
}
// Ouve tanto "input" quanto "change": alguns navegadores (principalmente
// no celular, usando o seletor nativo de data) so disparam "change".
document.getElementById('dataNascimento').addEventListener('input', calcIdade);
document.getElementById('dataNascimento').addEventListener('change', calcIdade);
calcIdade(); // calcula já ao carregar, caso o campo venha preenchido

function toggleNecessidade(v) {
    document.getElementById('necessidadeDetalhe').style.display = v === '1' ? 'block' : 'none';
}
document.getElementById('necessidadeSelect').addEventListener('change', function () {
    toggleNecessidade(this.value);
});
toggleNecessidade(document.getElementById('necessidadeSelect').value);

function toggleProgramaCampo(v) {
    document.getElementById('programaDetalhe').style.display = v === '1' ? 'block' : 'none';
}
toggleProgramaCampo(document.getElementById('programaSelect').value);

// Turno exclusivo por nível de ensino: EJA só tem Noturno;
// os demais níveis (Creche, Educação Infantil, Ensino Fundamental)
// só têm Matutino/Vespertino.
document.getElementById('nivelEnsino').addEventListener('change', function () {
    const turno = document.getElementById('turnoSelect');
    turno.innerHTML = this.value === 'EJA'
        ? '<option value="Noturno">Noturno</option>'
        : '<option value="Matutino">Matutino</option><option value="Vespertino">Vespertino</option>';
});
</script>
@endsection
