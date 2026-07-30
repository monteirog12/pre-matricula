<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Configurações') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">

            @if (session('sucesso'))
                <div class="bg-green-50 text-green-800 text-sm rounded-md p-3 mb-4">{{ session('sucesso') }}</div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 text-red-800 text-sm rounded-md p-3 mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.configuracoes.update') }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Ano letivo ativo</label>
                    <input type="number" name="ano_letivo_ativo" value="{{ old('ano_letivo_ativo', $anoLetivoAtivo) }}"
                        class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                    <p class="text-xs text-gray-400 mt-2">
                        Este é o ano letivo usado por padrão no formulário público de pré-matrícula
                        e nos filtros do painel administrativo. Troque este valor no início de cada
                        novo ciclo de pré-matrícula (ex: em dezembro/janeiro, antes de abrir para o
                        próximo ano letivo).
                    </p>
                </div>

                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md text-white text-sm font-semibold"
                        style="background-color: #0A5BA6;">
                    Salvar
                </button>
            </form>
        </div>
    </div>

    <footer class="text-center text-white text-sm py-4 mt-8" style="background-color: #06305E;">
        Prefeitura Municipal de Cafarnaum · Sistema de Pré-matrícula Escolar
    </footer>
</x-app-layout>
