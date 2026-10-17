<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escola_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('escola_id')->constrained('escolas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'escola_id']);
        });

        // Migra o vinculo antigo (1 escola por usuario, coluna escola_id)
        // para a nova tabela pivot, antes de remover a coluna antiga -
        // preserva o que ja estava cadastrado.
        if (Schema::hasColumn('users', 'escola_id')) {
            $usuarios = DB::table('users')->whereNotNull('escola_id')->get(['id', 'escola_id']);

            foreach ($usuarios as $usuario) {
                DB::table('escola_usuario')->insert([
                    'user_id' => $usuario->id,
                    'escola_id' => $usuario->escola_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('escola_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('escola_id')->nullable()->after('perfil')->constrained('escolas')->nullOnDelete();
        });

        Schema::dropIfExists('escola_usuario');
    }
};
