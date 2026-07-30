<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Painel administrativo') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filtro de ano letivo -->
            <form method="GET" class="flex justify-end">
                <select name="ano_letivo" onchange="this.form.submit()"
                    class="rounded-md border-gray-300 text-sm shadow-sm focus:border-[#0A5BA6] focus:ring-[#0A5BA6]">
                    @foreach ([date('Y'), date('Y') + 1] as $ano)
                        <option value="{{ $ano }}" {{ (int) $anoLetivo === $ano ? 'selected' : '' }}>Ano letivo {{ $ano }}</option>
                    @endforeach
                </select>
            </form>

            <!-- Cartoes de resumo -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total de pré-matrículas</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $total }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Pendentes</p>
                    <p class="text-2xl font-semibold text-yellow-600">{{ $porStatus['Pendente'] ?? 0 }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Confirmadas</p>
                    <p class="text-2xl font-semibold text-green-600">{{ $porStatus['Confirmada'] ?? 0 }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Canceladas</p>
                    <p class="text-2xl font-semibold text-red-600">{{ $porStatus['Cancelada'] ?? 0 }}</p>
                </div>
            </div>

            <!-- Graficos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-sm font-semibold text-gray-600 mb-3">Pré-matrículas por escola</p>
                    <div style="position:relative;height:260px">
                        <canvas id="chartEscolas"></canvas>
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-sm font-semibold text-gray-600 mb-3">Por status</p>
                    <div style="position:relative;height:260px">
                        <canvas id="chartStatus"></canvas>
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-sm font-semibold text-gray-600 mb-3">Zona urbana x rural</p>
                    <div style="position:relative;height:260px">
                        <canvas id="chartZona"></canvas>
                    </div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-sm font-semibold text-gray-600 mb-3">Por turno</p>
                    <div style="position:relative;height:260px">
                        <canvas id="chartTurno"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="text-center text-white text-sm py-4 mt-8" style="background-color: #06305E;">
        Prefeitura Municipal de Cafarnaum · Sistema de Pré-matrícula Escolar
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
        const porEscola = @json($porEscola);
        const porStatus = @json($porStatus);
        const porZona = @json($porZona);
        const porTurno = @json($porTurno);

        new Chart(document.getElementById('chartEscolas'), {
            type: 'bar',
            data: {
                labels: Object.keys(porEscola),
                datasets: [{ data: Object.values(porEscola), backgroundColor: '#0A5BA6', borderRadius: 4 }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } },
                responsive: true, maintainAspectRatio: false
            }
        });

        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(porStatus),
                datasets: [{
                    data: Object.values(porStatus),
                    backgroundColor: ['#F4A93B', '#2E7D32', '#D64545']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        new Chart(document.getElementById('chartZona'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(porZona),
                datasets: [{ data: Object.values(porZona), backgroundColor: ['#0A5BA6', '#F9D12C'] }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        new Chart(document.getElementById('chartTurno'), {
            type: 'bar',
            data: {
                labels: Object.keys(porTurno),
                datasets: [{ data: Object.values(porTurno), backgroundColor: '#06305E', borderRadius: 4 }]
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true } },
                responsive: true, maintainAspectRatio: false
            }
        });
    </script>
</x-app-layout>
