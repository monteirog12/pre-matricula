<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pre_matriculas MODIFY COLUMN nivel_ensino ENUM('Creche', 'Educacao Infantil', 'Ensino Fundamental', 'EJA') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pre_matriculas MODIFY COLUMN nivel_ensino ENUM('Educacao Infantil', 'Ensino Fundamental', 'EJA') NOT NULL");
    }
};
