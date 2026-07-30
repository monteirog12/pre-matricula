<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escolas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('endereco', 255)->nullable();
            $table->string('responsavel', 150)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('codigo_inep', 20)->nullable()->unique();
            $table->enum('zona', ['Urbana', 'Rural'])->default('Urbana');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escolas');
    }
};
