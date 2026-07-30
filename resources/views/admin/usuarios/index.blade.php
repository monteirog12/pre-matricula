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

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-mail</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perfil</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Escola</th>
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
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $usuario->escola->nome ?? '—' }}</td>
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
