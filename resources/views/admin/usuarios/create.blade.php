<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Novo usuário') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="bg-red-50 text-red-800 text-sm rounded-md p-3 mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.usuarios.store') }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nome</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">E-mail institucional</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Senha</label>
                        <input type="password" name="password"
                            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Confirmar senha</label>
                        <input type="password" name="password_confirmation"
                            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Perfil</label>
                    <select name="perfil" id="perfilSelect" onchange="document.getElementById('campoEscola').style.display = this.value === 'Operador' ? 'block' : 'none'"
                        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                        <option value="Operador" {{ old('perfil') == 'Operador' ? 'selected' : '' }}>Operador</option>
                        <option value="Admin" {{ old('perfil') == 'Admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Operador vê e edita apenas a escola vinculada. Admin tem acesso total.</p>
                </div>

                <div id="campoEscola">
                    <label class="block text-sm text-gray-600 mb-1">Escola vinculada (só para Operador)</label>
                    <select name="escola_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                        <option value="">Nenhuma (acesso a todas)</option>
                        @foreach ($escolas as $escola)
                            <option value="{{ $escola->id }}" {{ old('escola_id') == $escola->id ? 'selected' : '' }}>{{ $escola->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md text-white text-sm font-semibold"
                        style="background-color: #0A5BA6;">
                    Criar usuário
                </button>
            </form>
        </div>
    </div>

    <footer class="text-center text-white text-sm py-4 mt-8" style="background-color: #06305E;">
        Prefeitura Municipal de Cafarnaum · Sistema de Pré-matrícula Escolar
    </footer>
</x-app-layout>
