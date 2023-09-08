<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\Recompensa\RecompensaStoreRequest;
use App\Http\Requests\API\Admin\Recompensa\RecompensaUpdateRequest;
use App\LogicLive\Common\Models\RecompensaModel;
use App\LogicLive\Config;
use App\LogicLive\Resources\RecompensaResource;
use App\Models\Recompensa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class RecompensaController extends Controller
{
    private $recompensaResource;
    private $recompensa;
    private $config;

    public function __construct(Recompensa $recompensa)
    {
        $this->recompensa = $recompensa;
        $this->recompensaResource = new RecompensaResource();
        $this->config = new Config();
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
    public function store(RecompensaStoreRequest $request, Recompensa $recompensa)
    {
        try {
            DB::beginTransaction();
            $recompensa->nome = $request->nome;
            $recompensa->imagem = 'nada sendo passado';
            $recompensa->pontuacao = $request->pontuacao;
            $recompensa->logic_live_id = null;
            $recompensa->save();

            if ($this->config->ativo()) {
                $recompensaModel = new RecompensaModel([
                    'rec_nome'      => $request->nome,
                    'rec_imagem'    => 'vazio',
                    'rec_pontuacao' => $request->pontuacao,
                ]);
                $recompensaLogicLive = $this->recompensaResource->create($recompensaModel);

                if (is_null($recompensaLogicLive)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, 'Erro ao criar recompensa no Logic live');
                }
                $recompensa->logic_live_id = $recompensaLogicLive->getRecCodigo() ;
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
    public function update(RecompensaUpdateRequest $request, int $id)
    {
        try {
            DB::beginTransaction();
            $recompensa = Recompensa::findOrFail($id);
            $recompensa->update($request->all());
            $recompensa->save();

            if ($this->config->ativo() && !is_null($recompensa->logic_live_id)) {
                $recompensaModel = new RecompensaModel([
                    'rec_nome'      => $request->nome,
                    'rec_imagem'    => 'vazio',
                    'rec_pontuacao' => $request->pontuacao,
                ]);
                $recompensaLogicLive = $this->recompensaResource->update($recompensa->logic_live_id, $recompensaModel);

                if (is_null($recompensaLogicLive)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, 'Erro ao editar recompensa no Logic live');
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

            if ($this->config->ativo() && !is_null($recompensa->logic_live_id)) {
                $recompensaLogicLive = $this->recompensaResource->delete($recompensa->logic_live_id);

                if (is_null($recompensaLogicLive)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, 'Erro ao deletar recompensa no Logic live');
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
