<?php

namespace App\Http\Controllers\Api\admin\modulos;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LogicLive\config\Configuracao;
use App\Http\Controllers\LogicLive\modulos\Recompensa as ModulosRecompensa;
use App\Recompensa;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RecompensaController extends Controller
{
    private $recompensa;
    private $logicLive_recompensa;
    private $config;

    public function __construct(Recompensa $recompensa)
    {
        $this->recompensa = $recompensa;
        $this->logicLive_recompensa = new ModulosRecompensa();
        $this->config = new Configuracao();
    }

    /**
     * @return Response
     */
    public function index()
    {
        try {
            $data = Recompensa::all();
            return  ResponseController::json(Type::success, Action::index, $data);
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::index);
        }
    }

    /**
     * @param  Request    $request
     * @param  Recompensa $recompensa
     * @return Response
     */
    public function store(Request $request, Recompensa $recompensa)
    {
        try {
            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_recompensa->criarRecompensa(['rec_nome' => $request->nome, 'rec_imagem' => 'nada sendo passado', 'rec_pontuacao' => $request->pontuacao]);

                if ($criadoLogicLive['success'] == false) {
                    return ResponseController::json(Type::error, Action::store, null, $criadoLogicLive['msg']);
                }
            }
            $recompensa->nome = $request->nome;
            $recompensa->imagem = 'nada sendo passado';
            $recompensa->pontuacao = $request->pontuacao;
            $recompensa->id_logic_live = isset($criadoLogicLive) ? $criadoLogicLive['data']['rec_codigo'] : 0;
            $recompensa->save();

            return ResponseController::json(Type::success, Action::store);
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::store);
        }
    }

    /**
     * @param  int      $id
     * @return Response
     */
    public function show(int $id)
    {
        //
    }

    /**
     * @param  Request  $request
     * @param  int      $id
     * @return Response
     */
    public function update(Request $request, int $id)
    {
        try {
            $recompensa = Recompensa::findOrFail($id);

            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_recompensa->atualizarRecompensa($recompensa->id_logic_live, ['rec_nome' => $request->nome, 'rec_imagem' => 'nada sendo passado', 'rec_pontuacao' => $request->pontuacao]);

                if ($criadoLogicLive['success'] == false) {
                    return ResponseController::json(Type::error, Action::update, null, $criadoLogicLive['msg']);
                }
            }

            $recompensa->update($request->all());
            $recompensa->save();
            return ResponseController::json(Type::success, Action::update);
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::update);
        }
    }

    /**
     * @param  int      $id
     * @return Response
     */
    public function destroy(int $id)
    {
        try {
            $recompensa = Recompensa::findOrFail($id);

            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_recompensa->deletarRecompensa($recompensa->id_logic_live);

                if ($criadoLogicLive['success'] == false) {
                    return ResponseController::json(Type::error, Action::destroy, null, $criadoLogicLive['msg']);
                }
            }
            $recompensa->delete();
            return ResponseController::json(Type::success, Action::destroy);
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::destroy);
        }
    }
}
