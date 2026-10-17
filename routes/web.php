<?php

use App\Http\Controllers\Admin\EscolaController;
use App\Http\Controllers\Admin\ConfiguracaoController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PreMatriculaController;
use App\Http\Controllers\Public\PreMatriculaFormController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Publico: pais/responsaveis, sem login ---
Route::get('/pre-matricula', [PreMatriculaFormController::class, 'verificarForm'])->name('pre-matricula.create');
Route::post('/pre-matricula/verificar', [PreMatriculaFormController::class, 'verificar'])->name('pre-matricula.verificar');
Route::get('/pre-matricula/formulario', [PreMatriculaFormController::class, 'create'])->name('pre-matricula.formulario');
Route::post('/pre-matricula', [PreMatriculaFormController::class, 'store'])->name('pre-matricula.store');
Route::get('/pre-matricula/comprovante/{protocolo}', [PreMatriculaFormController::class, 'comprovante'])->name('pre-matricula.comprovante');

require __DIR__.'/auth.php';

// --- Painel administrativo: exige login (perfil Admin ou Operador) ---
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pre-matriculas', [PreMatriculaController::class, 'index'])->name('pre-matriculas.index');
    Route::get('/pre-matriculas/exportar', [PreMatriculaController::class, 'exportar'])->name('pre-matriculas.exportar');
    Route::get('/pre-matriculas/{preMatricula}', [PreMatriculaController::class, 'show'])->name('pre-matriculas.show');
    Route::put('/pre-matriculas/{preMatricula}', [PreMatriculaController::class, 'update'])->name('pre-matriculas.update');

    // --- Somente Admin: gestao de usuarios ---
    Route::middleware('admin')->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/novo', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::patch('/usuarios/{usuario}/status', [UsuarioController::class, 'alternarStatus'])->name('usuarios.status');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

        Route::get('/configuracoes', [ConfiguracaoController::class, 'edit'])->name('configuracoes.edit');
        Route::put('/configuracoes', [ConfiguracaoController::class, 'update'])->name('configuracoes.update');

        Route::get('/escolas', [EscolaController::class, 'index'])->name('escolas.index');
        Route::get('/escolas/nova', [EscolaController::class, 'create'])->name('escolas.create');
        Route::post('/escolas', [EscolaController::class, 'store'])->name('escolas.store');
        Route::get('/escolas/{escola}/editar', [EscolaController::class, 'edit'])->name('escolas.edit');
        Route::put('/escolas/{escola}', [EscolaController::class, 'update'])->name('escolas.update');
    });
});