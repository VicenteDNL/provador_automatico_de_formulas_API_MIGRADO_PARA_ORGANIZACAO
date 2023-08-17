<?php

namespace App\Http\Controllers\Api\LogicLive;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LogicLive\Config\Configuracao;
use App\Http\Controllers\LogicLive\Config\Game;
use App\Models\LogicLive;

class LogicLiveController extends Controller
{
    private $config;
    private $game;
    private $logicLive;

    public function __construct(LogicLive $logicLive)
    {
        $this->config = new Configuracao();
        $this->game = new Game();
        $this->logicLive = $logicLive;
    }

    public function infoModulosEndGame()
    {
        // Busca os dados do game da plataforma Logic Live

        $game = $this->game->getGame();

        if ($game['success'] == false) {
            return response()->json(['success' => false, 'msg' => $game['success'], 'data' => ''], 500);
        }

        // Busca os dados dos modulos da plataforma Logic Live
        $modulo = $this->game->getModulo();

        if ($modulo['success'] == false) {
            return response()->json(['success' => false, 'msg' => $modulo['success'], 'data' => ''], 500);
        }

        // Busca os dados do game na sua Base de Dados
        $gameMinhaBaseDados = LogicLive::where('tipo', '=', 'game')->get();

        // Busca os dados dos modulos na sua Base de Dados
        $modulo1MinhaBaseDados = LogicLive::where('tipo', '=', 'modulo1')->get();
        $modulo2MinhaBaseDados = LogicLive::where('tipo', '=', 'modulo2')->get();
        $modulo3MinhaBaseDados = LogicLive::where('tipo', '=', 'modulo3')->get();

        //Verifica sê os três modulos padroes e o game está cadastrado no Logic Live
        $estaConfiguradoNoLogicLive = count($modulo['data']) == 3 && count($game['data']) == 1;

        //Verifica sê os três modulos padroes e o game está cadastrado na sua Base de Dados
        $estaConfiguradoNaMinhaBaseDados = count($modulo1MinhaBaseDados) == 1 && count($modulo2MinhaBaseDados) == 1
        && count($modulo3MinhaBaseDados) == 1 && count($gameMinhaBaseDados) == 1;

        //sê as informações não estiverem cadastradas em nenhuma das bases, então pode ser cadastrada normalmente
        if (!$estaConfiguradoNoLogicLive && !$estaConfiguradoNaMinhaBaseDados) {
            return response()->json(['success' => true, 'msg' => 'O Game está apto para ser cadastrado no LogicLive', 'data' => ['game' => '', 'modulos' => [], 'cadastrados' => false]]);
        }

        // sê as informações estiverem cadastradas em apenas umas das bases, deve-se gerar um erro
        if (!$estaConfiguradoNoLogicLive || !$estaConfiguradoNaMinhaBaseDados) {
            if ($estaConfiguradoNoLogicLive && !$estaConfiguradoNaMinhaBaseDados) {
                return response()->json(['success' => false, 'msg' => 'Game configurado no Logic Live, mas não está configurado na sua Base de Dados ', 'data' => ''], 500);
            }

            if (!$estaConfiguradoNoLogicLive && $estaConfiguradoNaMinhaBaseDados) {
                return response()->json(['success' => false, 'msg' => 'Game configurado na sua Base de Dados , mas não está configurado no Logic Live', 'data' => ''], 500);
            }
        }
        // Verifica se o game da base de dados é o mesmo da API do Logic Live
        if ($gameMinhaBaseDados[0]->meu_id != $game['data'][0]['gam_codigo']) {
            return response()->json(['success' => false, 'msg' => 'O "GAME" cadastrado no LogicLive difere do cadastrado da sua base de dados', 'data' => ''], 500);
        }

        // Verifica se o modulo1 da base de dados é o mesmo da API do Logic Live
        $modulo1LogicLive = $this->game->getModuloId($modulo1MinhaBaseDados[0]->meu_id);

        if ($modulo1LogicLive['success'] == false) {
            return response()->json(['success' => false, 'msg' => $modulo1LogicLive['msg'], 'data' => ''], 500);
        }

        // Verifica se o  modulo2 da base de dados é o mesmo da API do Logic Live
        $modulo2LogicLive = $this->game->getModuloId($modulo2MinhaBaseDados[0]->meu_id);

        if ($modulo2LogicLive['success'] == false) {
            return response()->json(['success' => false, 'msg' => $modulo2LogicLive['msg'], 'data' => ''], 500);
        }

        // Verifica se o  modulo3 da base de dados é o mesmo da API do Logic Live
        $modulo3LogicLive = $this->game->getModuloId($modulo3MinhaBaseDados[0]->meu_id);

        if ($modulo3LogicLive['success'] == false) {
            return response()->json(['success' => false, 'msg' => $modulo3LogicLive['msg'], 'data' => ''], 500);
        }

        return response()->json(['success' => true, 'msg' => '', 'data' => ['game' => $game['data'][0], 'modulos' => [$modulo1LogicLive, $modulo2LogicLive, $modulo3LogicLive], 'cadastrados' => true]]);
    }

    public function criarModulosEndGame()
    {
        $gameBase = LogicLive::where('tipo', '=', 'game')->get();
        $gameApi = $this->game->getGame()['data'];
        $moduloBase = LogicLive::where('tipo', '=', ['modulo1', 'modulo2', 'modulo3'])->get();
        $moduloApi = $this->game->getGame()['data'];

        if (count($gameBase) == 0 && count($gameApi) == 0 && count($moduloBase) == 0 && count($moduloApi) == 0) {
            $gameModulos = $this->game->criarGameEndModulos();

            if ($gameModulos['success']) {
                $gameCriado = $gameModulos['data']['game'];
                $modulosCriado = $gameModulos['data']['modulos'];
                $niveisCriado = $gameModulos['data']['niveis'];
                $exerciciosCriado = $gameModulos['data']['exercicios'];
                $recompensasCriado = $gameModulos['data']['recompensas'];

                // Criando Game
                $logicLive_game = new LogicLive();
                $logicLive_game->tipo = 'game';
                $logicLive_game->meu_id = $gameCriado['gam_codigo'];
                $logicLive_game->nome = $gameCriado['gam_nome'];
                $logicLive_game->descricao = $gameCriado['gam_descricao'];
                $logicLive_game->ativo = $gameCriado['gam_ativo'];
                $logicLive_game->save();

                // Criando Modulo 1
                $logicLive_modulo1 = new LogicLive();
                $logicLive_modulo1->tipo = 'modulo1';
                $logicLive_modulo1->meu_id = $modulosCriado[0]['mod_codigo'];
                $logicLive_modulo1->game_id = $modulosCriado[0]['gam_codigo'];
                $logicLive_modulo1->hash = $modulosCriado[0]['mod_hash'];
                $logicLive_modulo1->nome = $modulosCriado[0]['mod_nome'];
                $logicLive_modulo1->descricao = $modulosCriado[0]['mod_descricao'];
                $logicLive_modulo1->ativo = $modulosCriado[0]['mod_ativo'];
                $logicLive_modulo1->save();

                // Criando Modulo 2
                $logicLive_modulo2 = new LogicLive();
                $logicLive_modulo2->tipo = 'modulo2';
                $logicLive_modulo2->meu_id = $modulosCriado[1]['mod_codigo'];
                $logicLive_modulo2->game_id = $modulosCriado[1]['gam_codigo'];
                $logicLive_modulo2->hash = $modulosCriado[1]['mod_hash'];
                $logicLive_modulo2->nome = $modulosCriado[1]['mod_nome'];
                $logicLive_modulo2->descricao = $modulosCriado[1]['mod_descricao'];
                $logicLive_modulo2->ativo = $modulosCriado[1]['mod_ativo'];
                $logicLive_modulo2->save();

                // Criando Modulo 3
                $logicLive_modulo3 = new LogicLive();
                $logicLive_modulo3->tipo = 'modulo3';
                $logicLive_modulo3->meu_id = $modulosCriado[2]['mod_codigo'];
                $logicLive_modulo3->game_id = $modulosCriado[2]['gam_codigo'];
                $logicLive_modulo3->hash = $modulosCriado[2]['mod_hash'];
                $logicLive_modulo3->nome = $modulosCriado[2]['mod_nome'];
                $logicLive_modulo3->descricao = $modulosCriado[2]['mod_descricao'];
                $logicLive_modulo3->ativo = $modulosCriado[2]['mod_ativo'];
                $logicLive_modulo3->save();

                // Criando Nivel Modulo 2
                $logicLive_nivel_modulo2 = new LogicLive();
                $logicLive_nivel_modulo2->tipo = 'nivel_modulo2';
                $logicLive_nivel_modulo2->meu_id = $niveisCriado[0]['niv_codigo'];
                $logicLive_nivel_modulo2->modulo_id = $niveisCriado[0]['mod_codigo'];
                $logicLive_nivel_modulo2->nome = $niveisCriado[0]['niv_nome'];
                $logicLive_nivel_modulo2->descricao = $niveisCriado[0]['niv_descricao'];
                $logicLive_nivel_modulo2->ativo = $niveisCriado[0]['niv_ativo'];
                $logicLive_nivel_modulo2->save();

                // Criando Nivel Modulo 3
                $logicLive_nivel_modulo3 = new LogicLive();
                $logicLive_nivel_modulo3->tipo = 'nivel_modulo3';
                $logicLive_nivel_modulo3->meu_id = $niveisCriado[1]['niv_codigo'];
                $logicLive_nivel_modulo3->modulo_id = $niveisCriado[1]['mod_codigo'];
                $logicLive_nivel_modulo3->nome = $niveisCriado[1]['niv_nome'];
                $logicLive_nivel_modulo3->descricao = $niveisCriado[1]['niv_descricao'];
                $logicLive_nivel_modulo3->ativo = $niveisCriado[1]['niv_ativo'];
                $logicLive_nivel_modulo3->save();

                // Criando Exercicio 1  Modulo 2
                $logicLive_exercicio1_modulo2 = new LogicLive();
                $logicLive_exercicio1_modulo2->tipo = 'exercicio1_modulo2';
                $logicLive_exercicio1_modulo2->meu_id = $exerciciosCriado[0]['exe_codigo'];
                $logicLive_exercicio1_modulo2->recompensa_id = $exerciciosCriado[0]['rec_codigo'];
                $logicLive_exercicio1_modulo2->nivel_id = $exerciciosCriado[0]['niv_codigo'];
                $logicLive_exercicio1_modulo2->nome = $exerciciosCriado[0]['exe_nome'];
                $logicLive_exercicio1_modulo2->descricao = $exerciciosCriado[0]['exe_descricao'];
                $logicLive_exercicio1_modulo2->hash = $exerciciosCriado[0]['exe_hash'];
                $logicLive_exercicio1_modulo2->link = $exerciciosCriado[0]['exe_link'];
                $logicLive_exercicio1_modulo2->ativo = $exerciciosCriado[0]['exe_ativo'];
                $logicLive_exercicio1_modulo2->save();

                // Criando Exercicio 2  Modulo 2
                $logicLive_exercicio2_modulo2 = new LogicLive();
                $logicLive_exercicio2_modulo2->tipo = 'exercicio2_modulo2';
                $logicLive_exercicio2_modulo2->meu_id = $exerciciosCriado[1]['exe_codigo'];
                $logicLive_exercicio2_modulo2->recompensa_id = $exerciciosCriado[1]['rec_codigo'];
                $logicLive_exercicio2_modulo2->nivel_id = $exerciciosCriado[1]['niv_codigo'];
                $logicLive_exercicio2_modulo2->nome = $exerciciosCriado[1]['exe_nome'];
                $logicLive_exercicio2_modulo2->descricao = $exerciciosCriado[1]['exe_descricao'];
                $logicLive_exercicio2_modulo2->hash = $exerciciosCriado[1]['exe_hash'];
                $logicLive_exercicio2_modulo2->link = $exerciciosCriado[1]['exe_link'];
                $logicLive_exercicio2_modulo2->ativo = $exerciciosCriado[1]['exe_ativo'];
                $logicLive_exercicio2_modulo2->save();

                // Criando Exercicio 1  Modulo 3
                $logicLive_exercicio1_modulo3 = new LogicLive();
                $logicLive_exercicio1_modulo3->tipo = 'exercicio1_modulo2';
                $logicLive_exercicio1_modulo3->meu_id = $exerciciosCriado[2]['exe_codigo'];
                $logicLive_exercicio1_modulo3->recompensa_id = $exerciciosCriado[2]['rec_codigo'];
                $logicLive_exercicio1_modulo3->nivel_id = $exerciciosCriado[2]['niv_codigo'];
                $logicLive_exercicio1_modulo3->nome = $exerciciosCriado[2]['exe_nome'];
                $logicLive_exercicio1_modulo3->descricao = $exerciciosCriado[2]['exe_descricao'];
                $logicLive_exercicio1_modulo3->hash = $exerciciosCriado[2]['exe_hash'];
                $logicLive_exercicio1_modulo3->link = $exerciciosCriado[2]['exe_link'];
                $logicLive_exercicio1_modulo3->ativo = $exerciciosCriado[2]['exe_ativo'];
                $logicLive_exercicio1_modulo3->save();

                // Criando Recompensa 1
                $logicLive_recompensa1 = new LogicLive();
                $logicLive_recompensa1->tipo = 'recompensa1';
                $logicLive_recompensa1->meu_id = $recompensasCriado[0]['rec_codigo'];
                $logicLive_recompensa1->nome = $recompensasCriado[0]['rec_nome'];
                $logicLive_recompensa1->ativo = true;
                $logicLive_recompensa1->save();

                // Criando Recompensa 2
                $logicLive_recompensa2 = new LogicLive();
                $logicLive_recompensa2->tipo = 'recompensa1';
                $logicLive_recompensa2->meu_id = $recompensasCriado[1]['rec_codigo'];
                $logicLive_recompensa2->nome = $recompensasCriado[1]['rec_nome'];
                $logicLive_recompensa2->ativo = true;
                $logicLive_recompensa2->save();

                return response()->json(['success' => true, 'msg' => 'criado com sucesso!', 'data' => ['game' => $gameCriado, 'modulos' => $modulosCriado, 'cadastrados' => true]]);
            } else {
                return response()->json(['success' => false, 'msg' => $gameModulos['msg'], 'data' => '']);
            }
        } else {
            return response()->json(['success' => false, 'msg' => 'Conflito na base de dados', 'data' => ''], 500);
        }
    }
}
