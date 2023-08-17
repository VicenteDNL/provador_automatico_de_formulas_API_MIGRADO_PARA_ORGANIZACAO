<?php

namespace App\Http\Controllers\Api\Admin\Modulos\ValidacaoFormulas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RespostaController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        // try{
        $resposta = DB::table('respostas_mvflp as r')
        ->select([
            'j.id as jogador_id',
            'j.nome',
            'j.usunome',
            'j.avatar',
            'j.email',
            'j.ativo',
            'e.id as exercicio_id',
            'e.nome as exercicio_nome',
            'e.tempo as exercicio_tempo',
            'e.qndt_erros as exercicio_qndt_erros',
            'e.ativo as exercicio_ativo',
            'r.tempo as resposta_tempo',
            'r.tentativas_invalidas as resposta_erros',
            'r.pontuacao as resposta_pontuacao',
            'r.concluida as resposta_concluida',
            'r.ativa as resposta_ativa',
            're.pontuacao as recompensas_pontuacao',
        ])
        ->join('exercicios_mvflp as e', 'r.id_exercicio', '=', 'e.id')
        ->join('jogadores as j', 'r.id_jogador', '=', 'j.id')
        ->join('recompensas as re', 'e.id_recompensa', '=', 're.id')
        ->paginate(10);
        return response()->json(['success' => true, 'msg' => '', 'data' => $resposta]);

        // }catch(\Exception $e){
        //     return response()->json(['success' => false, 'msg'=>'erro no servidor', 'data'=>''],500);
        // }
    }
}
