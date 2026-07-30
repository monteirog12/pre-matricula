<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_matricula_historico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pre_matricula_id')->constrained('pre_matriculas')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('campo_alterado', 60);
            $table->text('valor_anterior')->nullable();
            $table->text('valor_novo')->nullable();
            $table->string('motivo', 255)->nullable();
            $table->timestamp('alterado_em')->useCurrent();

            $table->index('pre_matricula_id');
            $table->index('alterado_em');
        });

        Schema::create('configuracoes', function (Blueprint $table) {
            $table->string('chave', 60)->primary();
            $table->string('valor', 255);
            $table->timestamps();
        });

        // users: adiciona campos de perfil/escola usados pelo painel admin
        Schema::table('users', function (Blueprint $table) {
            $table->enum('perfil', ['Admin', 'Operador'])->default('Operador')->after('email');
            $table->foreignId('escola_id')->nullable()->after('perfil')->constrained('escolas')->nullOnDelete();
            $table->boolean('ativo')->default(true)->after('escola_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('escola_id');
            $table->dropColumn(['perfil', 'ativo']);
        });
        Schema::dropIfExists('configuracoes');
        Schema::dropIfExists('pre_matricula_historico');
    }
};
