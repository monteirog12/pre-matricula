<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreMatricula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $anoLetivo = (int) $request->input(
            'ano_letivo',
            DB::table('configuracoes')->where('chave', 'ano_letivo_ativo')->value('valor') ?? date('Y')
        );

        $base = fn () => PreMatricula::where('ano_letivo', $anoLetivo);

        $total = $base()->count();

        $porStatus = $base()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $porTurno = $base()
            ->select('turno', DB::raw('count(*) as total'))
            ->groupBy('turno')
            ->pluck('total', 'turno');

        $porZona = $base()
            ->join('escolas', 'escolas.id', '=', 'pre_matriculas.escola_id')
            ->select('escolas.zona as zona', DB::raw('count(*) as total'))
            ->groupBy('escolas.zona')
            ->pluck('total', 'zona');

        $porEscola = $base()
            ->join('escolas', 'escolas.id', '=', 'pre_matriculas.escola_id')
            ->select('escolas.nome as nome', DB::raw('count(*) as total'))
            ->groupBy('escolas.nome')
            ->orderByDesc('total')
            ->pluck('total', 'nome');

        $porNivel = $base()
            ->select('nivel_ensino', DB::raw('count(*) as total'))
            ->groupBy('nivel_ensino')
            ->pluck('total', 'nivel_ensino');

        return view('admin.dashboard', compact(
            'anoLetivo', 'total', 'porStatus', 'porTurno', 'porZona', 'porEscola', 'porNivel'
        ));
    }
}
