<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alunos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('telefone', 20)->nullable();
            $table->date('data_nascimento');
            $table->string('naturalidade', 100)->nullable();
            $table->char('uf_naturalidade', 2)->nullable();
            $table->string('rg', 20)->nullable();
            $table->string('nome_pai', 150)->nullable();
            $table->string('profissao_pai', 100)->nullable();
            $table->string('nome_mae', 150)->nullable();
            $table->string('profissao_mae', 100)->nullable();
            $table->string('endereco', 255)->nullable();
            $table->boolean('possui_necessidade_especial')->default(false);
            $table->text('descricao_necessidade')->nullable();
            $table->timestamps();

            $table->index('nome');
            $table->index('data_nascimento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};
