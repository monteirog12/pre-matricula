@php
    $u = $usuario ?? null;
    $escolasSelecionadas = old('escolas', $u?->escolas->pluck('id')->toArray() ?? []);
@endphp

<div>
    <label class="block text-sm text-gray-600 mb-1">Nome</label>
    <input type="text" name="name" value="{{ old('name', $u->name ?? '') }}"
        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
</div>

<div>
    <label class="block text-sm text-gray-600 mb-1">E-mail institucional</label>
    <input type="email" name="email" value="{{ old('email', $u->email ?? '') }}"
        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm text-gray-600 mb-1">{{ $u ? 'Nova senha (deixe em branco para manter)' : 'Senha' }}</label>
        <input type="password" name="password"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
    </div>
    <div>
        <label class="block text-sm text-gray-600 mb-1">Confirmar senha</label>
        <input type="password" name="password_confirmation"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm text-gray-600 mb-1">Perfil</label>
        <select name="perfil" id="perfilSelect"
            onchange="document.getElementById('blocoEscolas').style.display = this.value === 'Operador' ? 'block' : 'none'"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
            <option value="Operador" {{ old('perfil', $u->perfil ?? 'Operador') == 'Operador' ? 'selected' : '' }}>Operador</option>
            <option value="Admin" {{ old('perfil', $u->perfil ?? '') == 'Admin' ? 'selected' : '' }}>Admin</option>
        </select>
    </div>
    <div>
        <label class="block text-sm text-gray-600 mb-1">Status</label>
        <select name="ativo" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
            <option value="1" {{ old('ativo', $u->ativo ?? true) == 1 ? 'selected' : '' }}>Ativo</option>
            <option value="0" {{ old('ativo', $u->ativo ?? true) == 0 ? 'selected' : '' }}>Inativo</option>
        </select>
    </div>
</div>

<div id="blocoEscolas" style="display: {{ old('perfil', $u->perfil ?? 'Operador') === 'Operador' ? 'block' : 'none' }}">
    <label class="block text-sm text-gray-600 mb-1">Escolas que este usuário pode acessar</label>
    <p class="text-xs text-gray-400 mb-2">Um Operador só vê e edita os dados das escolas marcadas aqui. Um diretor responsável por mais de uma escola pode ter várias marcadas.</p>
    <div class="grid grid-cols-2 gap-2 border border-gray-200 rounded-md p-3 max-h-48 overflow-y-auto">
        @foreach ($escolas as $escola)
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="escolas[]" value="{{ $escola->id }}"
                    {{ in_array($escola->id, $escolasSelecionadas) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-[#0A5BA6] focus:ring-[#0A5BA6]">
                {{ $escola->nome }}
            </label>
        @endforeach
    </div>
</div>
