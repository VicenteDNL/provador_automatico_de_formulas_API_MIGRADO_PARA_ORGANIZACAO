<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\Nivel\NivelStoreRequest;
use App\Http\Requests\API\Admin\Nivel\NivelUpdateRequest;
use App\LogicLive\Common\Enums\Types;
use App\LogicLive\Common\Models\NivelModel;
use App\LogicLive\Config;
use App\LogicLive\Managers\ValidacaoFormulas\ValidacaoFormulasModulo;
use App\LogicLive\Resources\NivelResource;
use App\Models\LogicLive;
use App\Models\Nivel;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class NivelController extends Controller
{
    private $niveis;
    private $config;
    private $nivelResource;

    public function __construct(Nivel $niveis)
    {
        $this->niveis = $niveis;
        $this->config = new Config();
        $this->nivelResource = new NivelResource();
    }

    /**
     * @return Response
     */
    public function index()
    {
        try {
            $data = $this->niveis
            ->with('recompensa')
            ->orderBy('created_at', 'desc')->paginate(10);
            return ResponseController::json(Type::success, Action::index, $data);
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::index);
        }
    }

    /**
     *
     * @param  NivelStoreRequest $request
     * @param  Nivel             $nivel
     * @param  Nivel             $nivel
     * @return Response
     */
    public function store(NivelStoreRequest $request, Nivel $nivel)
    {
        try {
            DB::beginTransaction();
            $nivel->logic_live_id = null;
            $nivel->nome = $request->nome;
            $nivel->descricao = $request->descricao;
            $nivel->ativo = $request->ativo;
            $nivel->recompensa_id = $request->recompensa_id;
            $nivel->save();

            if ($this->config->ativo()) {
                $baseDados = LogicLive::where(['tipo' => Types::MODULO->descricao(), 'modelo' => ValidacaoFormulasModulo::class])->first();
                $recompensa = $nivel->recompensa()->first();

                if (is_null($baseDados)) {
                    return ResponseController::json(Type::error, Action::store, null, 'Crie o módulo para para conseguir cadastrar os níveis');
                }

                if (!is_null($recompensa)) {
                    if (is_null($recompensa->logic_live_id)) {
                        return ResponseController::json(Type::error, Action::store, null, 'A recompensa selecionada não está vinculada ao Logic live');
                    }
                    $recompensa = $recompensa->logic_live_id;
                }

                $nivelModels = new NivelModel([
                    'mod_codigo'    => $baseDados->meu_id,
                    'niv_nome'      => $request->nome,
                    'niv_descricao' => $request->descricao,
                    'niv_ativo'     => $request->ativo,
                    'rec_codigo'    => $recompensa,
                ]);
                $nivelLogicLive = $this->nivelResource->create($nivelModels);

                if (is_null($nivelLogicLive)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, 'Erro ao criar nível no Logic live');
                }

                $nivel->logic_live_id = $nivelLogicLive->getNivCodigo();
                $nivel->save();
            }

            DB::commit();
            return  ResponseController::json(Type::success, Action::store);
        } catch(Exception $e) {
            DB::rollBack();
            return  ResponseController::json(Type::error, Action::store);
        }
    }

    /**
     * @param  int      $id
     * @return Response
     */
    public function show(int $id)
    {
        $data = $this->niveis->find($id);
        return ResponseController::json(Type::success, Action::show, $data);
    }

    /**
     *
     * @param  NivelUpdateRequest $request
     * @param  int                $id
     * @return Response
     */
    public function update(NivelUpdateRequest $request, int $id)
    {
        try {
            DB::beginTransaction();
            $nivel = Nivel::findOrFail($id);
            $nivel->update($request->all());
            $nivel->save();

            if ($this->config->ativo() && !is_null($nivel->logic_live_id)) {
                $recompensa = $nivel->recompensa()->first();

                if (!is_null($recompensa)) {
                    if (is_null($recompensa->logic_live_id)) {
                        return ResponseController::json(Type::error, Action::store, null, 'A recompensa selecionada não está vinculada ao Logic live');
                    }
                    $recompensa = $recompensa->logic_live_id;
                }

                $nivelModels = new NivelModel([
                    'niv_nome'       => $request->nome,
                    'niv_descricao'  => $request->descricao,
                    'niv_ativo'      => $request->ativo,
                    'rec_codigo'     => $recompensa,
                ]);
                $nivelLogicLive = $this->nivelResource->update($nivel->logic_live_id, $nivelModels);

                if (is_null($nivelLogicLive)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, 'Erro ao editar nível no Logic live');
                }
            }
            DB::commit();
            return ResponseController::json(Type::success, Action::update);
        } catch(Exception $e) {
            DB::rollBack();
            return ResponseController::json(Type::error, Action::update);
        }
    }

    /**
     * @return Response
     */
    public function all()
    {
        try {
            $nivel = Nivel::all();
            return ResponseController::json(Type::success, Action::all, $nivel);
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::all);
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
            $nivel = Nivel::findOrFail($id);
            $nivel->delete();

            if ($this->config->ativo() && !is_null($nivel->logic_live_id)) {
                $nivelLogicLive = $this->nivelResource->delete($nivel->logic_live_id);

                if (is_null($nivelLogicLive)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, 'Erro ao deletar nível no Logic live');
                }
            }
            DB::commit();
            return  ResponseController::json(Type::success, Action::destroy);
        } catch(Exception $e) {
            DB::rollBack();
            return  ResponseController::json(Type::error, Action::destroy);
        }
    }
}
