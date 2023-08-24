<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LogicLive\Config\Configuracao;
use App\Http\Controllers\LogicLive\Modulos\Recompensa as ModulosRecompensa;
use App\Models\Recompensa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class RecompensaController extends Controller
{
    private $logicLive_recompensa;
    private $config;

    public function __construct(Recompensa $recompensa)
    {
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
        } catch(Throwable $e) {
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
            DB::beginTransaction();
            $recompensa->nome = $request->nome;
            $recompensa->imagem = 'nada sendo passado';
            $recompensa->pontuacao = $request->pontuacao;
            $recompensa->id_logic_live = 0;
            $recompensa->save();

            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_recompensa->criarRecompensa(['rec_nome' => $request->nome, 'rec_imagem' => 'nada sendo passado', 'rec_pontuacao' => $request->pontuacao]);

                if ($criadoLogicLive['success'] == false) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, $criadoLogicLive['msg']);
                }
                $recompensa->id_logic_live = $criadoLogicLive['data']['rec_codigo'] ;
                $recompensa->save();
            }
            DB::commit();
            return ResponseController::json(Type::success, Action::store);
        } catch(Throwable $e) {
            DB::rollBack();
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
            DB::beginTransaction();
            $recompensa = Recompensa::findOrFail($id);
            $recompensa->update($request->all());
            $recompensa->save();

            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_recompensa->atualizarRecompensa($recompensa->id_logic_live, ['rec_nome' => $request->nome, 'rec_imagem' => 'nada sendo passado', 'rec_pontuacao' => $request->pontuacao]);

                if ($criadoLogicLive['success'] == false) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::update, null, $criadoLogicLive['msg']);
                }
            }
            DB::commit();
            return ResponseController::json(Type::success, Action::update);
        } catch(Throwable $e) {
            DB::rollBack();
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
            DB::beginTransaction();
            $recompensa = Recompensa::findOrFail($id);
            $recompensa->delete();

            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_recompensa->deletarRecompensa($recompensa->id_logic_live);

                if ($criadoLogicLive['success'] == false) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::destroy, null, $criadoLogicLive['msg']);
                }
            }
            DB::commit();
            return ResponseController::json(Type::success, Action::destroy);
        } catch(Throwable $e) {
            DB::rollBack();
            return ResponseController::json(Type::error, Action::destroy);
        }
    }
}
