<?php

namespace Database\Seeders;

use App\Models\Escola;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('configuracoes')->updateOrInsert(
            ['chave' => 'ano_letivo_ativo'],
            ['valor' => (string) (date('Y') + 1)]
        );

        Escola::insert([
            ['codigo_escola' => 'ESC-001', 'nome' => 'Escola Municipal Sede', 'zona' => 'Urbana', 'codigo_inep' => '29000001', 'created_at' => now(), 'updated_at' => now()],
            ['codigo_escola' => 'ESC-002', 'nome' => 'Escola Municipal Zona Rural 1', 'zona' => 'Rural', 'codigo_inep' => '29000002', 'created_at' => now(), 'updated_at' => now()],
        ]);

        User::create([
            'name' => 'Administrador',
            'email' => 'admin@cafarnaum.ba.gov.br',
            'password' => Hash::make('trocar-esta-senha'),
            'perfil' => 'Admin',
        ]);
    }
}
