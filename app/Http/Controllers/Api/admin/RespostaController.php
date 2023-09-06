<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Throwable;

class RespostaController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        try {
            $resposta = DB::table('respostas as r')
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
            ->join('exercicios as e', 'r.exercicio_id', '=', 'e.id')
            ->join('jogadores as j', 'r.jogador_id', '=', 'j.id')
            ->join('recompensas as re', 'e.recompensa_id', '=', 're.id')
            ->paginate(30);
            return ResponseController::json(Type::success, Action::index, $resposta);
        } catch(Throwable $e) {
            DB::rollBack();
            return ResponseController::json(Type::error, Action::store);
        }
    }
}
