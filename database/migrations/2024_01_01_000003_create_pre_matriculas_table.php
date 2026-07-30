<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_matriculas', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('ano_letivo');
            $table->foreignId('aluno_id')->constrained('alunos')->restrictOnDelete();
            $table->foreignId('escola_id')->constrained('escolas')->restrictOnDelete();
            $table->string('protocolo', 20)->unique();

            $table->enum('tipo_aluno', ['Novo', 'Aluno da Casa']);
            $table->enum('nivel_ensino', ['Educacao Infantil', 'Ensino Fundamental', 'EJA']);
            $table->enum('turno', ['Matutino', 'Vespertino', 'Noturno']);
            $table->enum('situacao', ['Promovido', 'Repetente']);
            $table->string('serie_ano_anterior', 50)->nullable();

            $table->boolean('participa_programa_federal')->default(false);
            $table->string('qual_programa', 100)->nullable();
            $table->string('nis', 20)->nullable();

            $table->text('observacoes')->nullable();
            $table->enum('status', ['Pendente', 'Confirmada', 'Cancelada'])->default('Pendente');

            $table->timestamps();

            $table->unique(['aluno_id', 'ano_letivo']);
            $table->index('ano_letivo');
            $table->index('status');
            $table->index(['ano_letivo', 'escola_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_matriculas');
    }
};
