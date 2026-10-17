@php $e = $escola ?? null; @endphp

<div>
    <label class="block text-sm text-gray-600 mb-1">Nome da escola</label>
    <input type="text" name="nome" value="{{ old('nome', $e->nome ?? '') }}"
        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
</div>

<div>
    <label class="block text-sm text-gray-600 mb-1">Código INEP</label>
    <input type="text" name="codigo_inep" value="{{ old('codigo_inep', $e->codigo_inep ?? '') }}"
        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
</div>

<div>
    <label class="block text-sm text-gray-600 mb-1">Endereço</label>
    <input type="text" name="endereco" value="{{ old('endereco', $e->endereco ?? '') }}"
        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm text-gray-600 mb-1">Diretora/Responsável</label>
        <input type="text" name="responsavel" value="{{ old('responsavel', $e->responsavel ?? '') }}"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
    </div>
    <div>
        <label class="block text-sm text-gray-600 mb-1">Telefone</label>
        <input type="text" name="telefone" value="{{ old('telefone', $e->telefone ?? '') }}"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm text-gray-600 mb-1">Zona</label>
        <select name="zona" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
            <option value="Urbana" {{ old('zona', $e->zona ?? '') == 'Urbana' ? 'selected' : '' }}>Urbana</option>
            <option value="Rural" {{ old('zona', $e->zona ?? '') == 'Rural' ? 'selected' : '' }}>Rural</option>
        </select>
    </div>
    <div>
        <label class="block text-sm text-gray-600 mb-1">Status</label>
        <select name="ativo" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
            <option value="1" {{ old('ativo', $e->ativo ?? true) == 1 ? 'selected' : '' }}>Ativa</option>
            <option value="0" {{ old('ativo', $e->ativo ?? true) == 0 ? 'selected' : '' }}>Inativa</option>
        </select>
        <p class="text-xs text-gray-400 mt-1">Escolas inativas não aparecem no formulário público de pré-matrícula.</p>
    </div>
</div>
