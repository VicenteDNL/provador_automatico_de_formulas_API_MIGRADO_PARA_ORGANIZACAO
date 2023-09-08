<?php

namespace App\Http\Controllers\Api\Aluno;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Aluno\Jogador\JogadorHashRequest;
use App\LogicLive\Config;
use App\LogicLive\Resources\JogadorResource;
use App\Models\Jogador;
use Throwable;

class JogadorController extends Controller
{
    private $config;

    public function __construct()
    {
        $this->config = new Config();
    }

    public function hash(JogadorHashRequest $request)
    {
        try {
            if (!$this->config->ativo()) {
                return ResponseController::json(Type::error, Action::show, null, 'integração com logic live desativada');
            }

            $jogadorResource = new JogadorResource($request->hash);
            $jogadorLogicLive = $jogadorResource->get();

            if (is_null($jogadorLogicLive)) {
                return ResponseController::json(Type::error, Action::show, null, 'erro ao validar jogador no logiclive');
            }

            $myJogador = Jogador::where('logic_live_id', $jogadorLogicLive->getJogCodigo())->first();

            if (is_null($myJogador)) {
                $myJogador = new Jogador();
            }
            $myJogador->nome = $jogadorLogicLive->getJogNome();
            $myJogador->usunome = $jogadorLogicLive->getJogUsunome();
            $myJogador->email = $jogadorLogicLive->getJogEmail();
            $myJogador->avatar = $jogadorLogicLive->getJogAvatar();
            $myJogador->token = $request->usu_hash;
            $myJogador->ativo = $jogadorLogicLive->getJogAtivo();
            $myJogador->provedor = $jogadorLogicLive->getJogProvedor();
            $myJogador->logic_live_id = $jogadorLogicLive->getJogCodigo();
            $myJogador->save();

            return ResponseController::json(Type::success, Action::show, $myJogador);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::show);
        }
    }
}
