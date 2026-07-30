<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Pré-matrícula') · Prefeitura de Cafarnaum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --cor-primaria: #0A5BA6;
            --cor-primaria-escura: #06305E;
            --cor-destaque: #F4A93B;
            --cor-dourado: #F9D12C;
            --cor-sucesso: #2E7D32;
            --cor-fundo: #F8F9FA;
        }
        body { font-family: 'Montserrat', sans-serif; background: var(--cor-fundo); }
        .navbar { background: #fff !important; border-bottom: 3px solid var(--cor-primaria); }
        .navbar-brand { color: var(--cor-primaria-escura) !important; font-weight: 600; }
        .btn-primary { background: var(--cor-primaria); border-color: var(--cor-primaria); }
        .btn-primary:hover { background: var(--cor-primaria-escura); border-color: var(--cor-primaria-escura); }
        .btn-outline-primary { color: var(--cor-primaria); border-color: var(--cor-primaria); }
        .btn-outline-primary:hover { background: var(--cor-primaria); border-color: var(--cor-primaria); }
        h5, h6 { color: var(--cor-primaria-escura); }
        .form-check-input:checked { background-color: var(--cor-primaria); border-color: var(--cor-primaria); }
        footer.rodape-cafarnaum { background: var(--cor-primaria-escura); color: #fff; padding: 1.5rem 0; margin-top: 2rem; font-size: 0.85rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-light mb-4">
        <div class="container d-flex align-items-center gap-2">
            <img src="{{ asset('images/logo-cafarnaum.png') }}" alt="Brasão de Cafarnaum" height="40">
            <span class="navbar-brand mb-0">Pré-matrícula Escolar</span>
        </div>
    </nav>

    <div class="container" style="max-width: 720px;">
        @if (session('sucesso'))
            <div class="alert alert-success">Pré-matrícula enviada com sucesso!</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('conteudo')
    </div>

    <footer class="rodape-cafarnaum text-center">
        Prefeitura Municipal de Cafarnaum · Sistema de Pré-matrícula Escolar
    </footer>
</body>
</html>
