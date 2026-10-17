<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Escolas') }}</h2>
            <a href="{{ route('admin.escolas.create') }}"
               class="inline-flex items-center px-4 py-2 rounded-md text-white text-sm font-semibold"
               style="background-color: #0A5BA6;">
                Nova escola
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('sucesso'))
                <div class="bg-green-50 text-green-800 text-sm rounded-md p-3">{{ session('sucesso') }}</div>
            @endif

            <!-- Busca e filtros -->
            <form method="GET" class="bg-white shadow-sm sm:rounded-lg p-4 space-y-3">
                <div class="flex gap-2">
                    <input type="text" name="busca" value="{{ request('busca') }}"
                        placeholder="Buscar por nome da escola ou código INEP..."
                        class="flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md text-white text-sm font-semibold whitespace-nowrap"
                            style="background-color: #0A5BA6;">
                        Buscar
                    </button>
                    @if (request('busca') || request('zona') || request('status'))
                        <a href="{{ route('admin.escolas.index') }}"
                           class="inline-flex items-center px-4 py-2 rounded-md border border-gray-300 text-gray-600 text-sm">
                            Limpar
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-4 mt-1 border-t border-gray-100">
                    <select name="zona" onchange="this.form.submit()"
                        class="rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                        <option value="">Todas as zonas</option>
                        <option value="Urbana" {{ request('zona') == 'Urbana' ? 'selected' : '' }}>Urbana</option>
                        <option value="Rural" {{ request('zona') == 'Rural' ? 'selected' : '' }}>Rural</option>
                    </select>

                    <select name="status" onchange="this.form.submit()"
                        class="rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                        <option value="">Todos os status</option>
                        <option value="Ativa" {{ request('status') == 'Ativa' ? 'selected' : '' }}>Ativa</option>
                        <option value="Inativa" {{ request('status') == 'Inativa' ? 'selected' : '' }}>Inativa</option>
                    </select>
                </div>
            </form>

            <!-- Tabela -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código INEP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zona</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($escolas as $escola)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $escola->nome }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $escola->codigo_inep ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $escola->zona }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $escola->ativo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $escola->ativo ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.escolas.edit', $escola) }}"
                                       title="Editar"
                                       class="inline-flex items-center justify-center w-7 h-7 rounded-full text-white hover:opacity-90 transition align-middle"
                                       style="background-color: #0A5BA6;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-8.5 8.5a2 2 0 01-.878.507l-3 .857a.5.5 0 01-.618-.618l.857-3a2 2 0 01.507-.878l8.5-8.5z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">Nenhuma escola encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $escolas->links() }}</div>
        </div>
    </div>

    <footer class="text-center text-white text-sm py-4 mt-8" style="background-color: #06305E;">
        Prefeitura Municipal de Cafarnaum · Sistema de Pré-matrícula Escolar
    </footer>
</x-app-layout>
