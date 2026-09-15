<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pré-matrículas') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <!-- Busca e filtros -->
            <form method="GET" class="bg-white shadow-sm sm:rounded-lg p-4 space-y-3">
                <div class="flex gap-2">
                    <input type="text" name="busca" value="{{ request('busca') }}"
                        placeholder="Buscar por nome do aluno, protocolo ou telefone..."
                        class="flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md text-white text-sm font-semibold whitespace-nowrap"
                            style="background-color: #0A5BA6;">
                        Buscar
                    </button>
                    @if (request('busca'))
                        <a href="{{ route('admin.pre-matriculas.index') }}"
                           class="inline-flex items-center px-4 py-2 rounded-md border border-gray-300 text-gray-600 text-sm">
                            Limpar
                        </a>
                    @endif
                    <a href="{{ route('admin.pre-matriculas.exportar', request()->query()) }}"
                       class="inline-flex items-center px-4 py-2 rounded-md border text-sm whitespace-nowrap"
                       style="border-color: #0A5BA6; color: #0A5BA6;">
                        Exportar CSV
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 pt-4 mt-1 border-t border-gray-100">
                    <select name="ano_letivo" onchange="this.form.submit()"
                        class="rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                        @foreach ([date('Y'), date('Y') + 1] as $ano)
                            <option value="{{ $ano }}" {{ (int) $anoLetivo === $ano ? 'selected' : '' }}>{{ $ano }}</option>
                        @endforeach
                    </select>

                    <select name="escola_id" onchange="this.form.submit()"
                        class="rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                        <option value="">Todas as escolas</option>
                        @foreach ($escolas as $escola)
                            <option value="{{ $escola->id }}" {{ request('escola_id') == $escola->id ? 'selected' : '' }}>{{ $escola->nome }}</option>
                        @endforeach
                    </select>

                    <select name="turno" onchange="this.form.submit()"
                        class="rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                        <option value="">Todos os turnos</option>
                        <option value="Matutino" {{ request('turno') == 'Matutino' ? 'selected' : '' }}>Matutino</option>
                        <option value="Vespertino" {{ request('turno') == 'Vespertino' ? 'selected' : '' }}>Vespertino</option>
                        <option value="Noturno" {{ request('turno') == 'Noturno' ? 'selected' : '' }}>Noturno</option>
                    </select>

                    <select name="nivel_ensino" onchange="this.form.submit()"
                        class="rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                        <option value="">Todos os níveis</option>
                        <option value="Educacao Infantil" {{ request('nivel_ensino') == 'Educacao Infantil' ? 'selected' : '' }}>Educação infantil</option>
                        <option value="Ensino Fundamental" {{ request('nivel_ensino') == 'Ensino Fundamental' ? 'selected' : '' }}>Ensino fundamental</option>
                        <option value="EJA" {{ request('nivel_ensino') == 'EJA' ? 'selected' : '' }}>EJA</option>
                    </select>

                    <select name="status" onchange="this.form.submit()"
                        class="rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                        <option value="">Todos os status</option>
                        <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="Confirmada" {{ request('status') == 'Confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="Cancelada" {{ request('status') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
            </form>

            <!-- Tabela -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Protocolo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aluno</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Escola</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turno</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($preMatriculas as $pm)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $pm->protocolo }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $pm->aluno->nome }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $pm->escola->nome }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $pm->turno }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $cores = [
                                            'Pendente' => 'bg-yellow-100 text-yellow-800',
                                            'Confirmada' => 'bg-green-100 text-green-800',
                                            'Cancelada' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cores[$pm->status] }}">
                                        {{ $pm->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.pre-matriculas.show', $pm) }}"
                                       title="Ver detalhes"
                                       class="inline-flex items-center justify-center w-7 h-7 rounded-full text-white hover:opacity-90 transition align-middle"
                                       style="background-color: #0A5BA6;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 3.5c-4.5 0-8.3 2.9-9.5 6.5 1.2 3.6 5 6.5 9.5 6.5s8.3-2.9 9.5-6.5C18.3 6.4 14.5 3.5 10 3.5zm0 10.5a4 4 0 110-8 4 4 0 010 8z"/>
                                            <path d="M10 8a2 2 0 100 4 2 2 0 000-4z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400">Nenhuma pré-matrícula encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $preMatriculas->links() }}</div>
        </div>
    </div>

    <footer class="text-center text-white text-sm py-4 mt-8" style="background-color: #06305E;">
        Prefeitura Municipal de Cafarnaum · Sistema de Pré-matrícula Escolar
    </footer>
</x-app-layout>
