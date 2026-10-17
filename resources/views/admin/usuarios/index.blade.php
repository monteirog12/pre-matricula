<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Usuários') }}</h2>
            <a href="{{ route('admin.usuarios.create') }}"
               class="inline-flex items-center px-4 py-2 rounded-md text-white text-sm font-semibold"
               style="background-color: #0A5BA6;">
                Novo usuário
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('sucesso'))
                <div class="bg-green-50 text-green-800 text-sm rounded-md p-3">{{ session('sucesso') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 text-red-800 text-sm rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-mail</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perfil</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Escolas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($usuarios as $usuario)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $usuario->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $usuario->email }}</td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $usuario->perfil === 'Admin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $usuario->perfil }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $usuario->perfil === 'Admin' ? 'Todas' : ($usuario->escolas->pluck('nome')->implode(', ') ?: '—') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $usuario->ativo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $usuario->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                    <a href="{{ route('admin.usuarios.edit', $usuario) }}"
                                       title="Editar"
                                       class="inline-flex items-center justify-center w-7 h-7 rounded-full text-white hover:opacity-90 transition"
                                       style="background-color: #0A5BA6;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-8.5 8.5a2 2 0 01-.878.507l-3 .857a.5.5 0 01-.618-.618l.857-3a2 2 0 01.507-.878l8.5-8.5z"/>
                                        </svg>
                                    </a>

                                    @if ($usuario->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.usuarios.status', $usuario) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="{{ $usuario->ativo ? 'Inativar' : 'Reativar' }}"
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-full text-white hover:opacity-90 transition"
                                                style="background-color: {{ $usuario->ativo ? '#F4A93B' : '#2E7D32' }};">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}" class="inline"
                                              onsubmit="return confirm('Excluir este usuário definitivamente? Esta ação não pode ser desfeita.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Excluir"
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-full text-white hover:opacity-90 transition"
                                                style="background-color: #D64545;">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8 2a1 1 0 000 2h4a1 1 0 100-2H8zM3 5a1 1 0 011-1h12a1 1 0 110 2h-.5l-.7 10.2A2 2 0 0112.8 18H7.2a2 2 0 01-2-1.8L4.5 6H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>{{ $usuarios->links() }}</div>
        </div>
    </div>

    <footer class="text-center text-white text-sm py-4 mt-8" style="background-color: #06305E;">
        Prefeitura Municipal de Cafarnaum · Sistema de Pré-matrícula Escolar
    </footer>
</x-app-layout>
