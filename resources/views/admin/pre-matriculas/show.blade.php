<x-app-layout>
    <x-slot name="header">
        <div class="inline-flex items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $preMatricula->protocolo }}
            </h2>
            @php
                $cores = [
                    'Pendente' => 'bg-yellow-100 text-yellow-800',
                    'Confirmada' => 'bg-green-100 text-green-800',
                    'Cancelada' => 'bg-red-100 text-red-800',
                ];
            @endphp
            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cores[$preMatricula->status] }}">
                {{ $preMatricula->status }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('sucesso'))
                <div class="bg-green-50 text-green-800 text-sm rounded-md p-3">{{ session('sucesso') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.pre-matriculas.update', $preMatricula) }}"
                  class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Dados do aluno</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm text-gray-600 mb-1">Nome</label>
                            <input type="text" name="aluno[nome]" value="{{ $preMatricula->aluno->nome }}"
                                   class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Telefone</label>
                            <input type="text" name="aluno[telefone]" value="{{ $preMatricula->aluno->telefone }}"
                                   class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Data de nascimento</label>
                            <input type="text" value="{{ \Carbon\Carbon::parse($preMatricula->aluno->data_nascimento)->format('d/m/Y') }}" disabled
                                   class="w-full rounded-md border-gray-200 text-sm bg-gray-50 text-gray-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm text-gray-600 mb-1">Endereço</label>
                            <input type="text" name="aluno[endereco]" value="{{ $preMatricula->aluno->endereco }}"
                                   class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Idade</label>
                            <input type="text" value="{{ $preMatricula->aluno->idade }} anos" disabled
                                   class="w-full rounded-md border-gray-200 text-sm bg-gray-50 text-gray-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">RG</label>
                            <input type="text" value="{{ $preMatricula->aluno->rg }}" disabled
                                   class="w-full rounded-md border-gray-200 text-sm bg-gray-50 text-gray-500">
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Dados escolares</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm text-gray-600 mb-1">Escola</label>
                            <select name="escola_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                                @foreach (\App\Models\Escola::orderBy('nome')->get() as $escola)
                                    <option value="{{ $escola->id }}" {{ $preMatricula->escola_id == $escola->id ? 'selected' : '' }}>
                                        {{ $escola->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Turno</label>
                            <select name="turno" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                                @if ($preMatricula->nivel_ensino === 'EJA')
                                    <option value="Noturno" selected>Noturno</option>
                                @else
                                    <option value="Matutino" {{ $preMatricula->turno == 'Matutino' ? 'selected' : '' }}>Matutino</option>
                                    <option value="Vespertino" {{ $preMatricula->turno == 'Vespertino' ? 'selected' : '' }}>Vespertino</option>
                                @endif
                            </select>
                            <p class="text-xs text-gray-400 mt-1">
                                Nível de ensino: {{ $preMatricula->nivel_ensino }} — turnos disponíveis restritos automaticamente.
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Situação</label>
                            <select name="situacao" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                                <option value="Promovido" {{ $preMatricula->situacao == 'Promovido' ? 'selected' : '' }}>Promovido</option>
                                <option value="Repetente" {{ $preMatricula->situacao == 'Repetente' ? 'selected' : '' }}>Repetente</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Status</label>
                            <select name="status" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                                <option value="Pendente" {{ $preMatricula->status == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                                <option value="Confirmada" {{ $preMatricula->status == 'Confirmada' ? 'selected' : '' }}>Confirmada</option>
                                <option value="Cancelada" {{ $preMatricula->status == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Motivo da alteração (opcional)</label>
                            <input type="text" name="motivo" placeholder="ex.: correção a pedido do responsável"
                                   class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md text-white text-sm font-semibold"
                            style="background-color: #0A5BA6;">
                        Salvar alterações
                    </button>
                </div>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Histórico de alterações</h3>
                <div class="divide-y divide-gray-100">
                    @forelse ($preMatricula->historico as $registro)
                        <div class="flex justify-between items-start py-3 text-sm">
                            <span class="text-gray-700">
                                Campo <strong>{{ \App\Models\PreMatricula::$rotulosCampos[$registro->campo_alterado] ?? $registro->campo_alterado }}</strong> alterado de
                                "{{ $registro->valor_anterior }}" para "{{ $registro->valor_novo }}"
                                @if ($registro->motivo) — {{ $registro->motivo }} @endif
                            </span>
                            <span class="text-gray-400 whitespace-nowrap ml-4">
                                {{ $registro->usuario->name ?? 'Responsável' }} · {{ $registro->alterado_em->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-2">Nenhuma alteração registrada ainda.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <footer class="text-center text-white text-sm py-4 mt-8" style="background-color: #06305E;">
        Prefeitura Municipal de Cafarnaum · Sistema de Pré-matrícula Escolar
    </footer>
</x-app-layout>
