<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LogicLive\Config\Configuracao;
use App\Http\Controllers\LogicLive\Modulos\ValidacaoFormulas\NivelVF;
use App\Http\Requests\API\Admin\Nivel\NivelStoreRequest;
use App\Http\Requests\API\Admin\Nivel\NivelUpdateRequest;
use App\Models\LogicLive;
use App\Models\Nivel;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class NivelController extends Controller
{
    private $niveis;
    private $config;
    private $logicLive_nivel;

    public function __construct(Nivel $niveis)
    {
        $this->niveis = $niveis;
        $this->config = new Configuracao();
        $this->logicLive_nivel = new NivelVF();
    }

    /**
     * @return Response
     */
    public function index()
    {
        try {
            $data = $this->niveis->orderBy('created_at', 'desc')->paginate(10);
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
            $nivel->meu_logic_live_id = 0;
            $nivel->modulo_id = isset($baseDados) ? $baseDados->meu_id : 0;
            $nivel->nome = $request->nome;
            $nivel->descricao = $request->descricao;
            $nivel->ativo = $request->ativo;
            $nivel->recompensa_id = $request->recompensa_id;
            $nivel->save();

            if ($this->config->ativo()) {
                $baseDados = LogicLive::where('tipo', '=', 'modulo1')->get();
                $baseDados = $baseDados[0];
                $criadoLogicLive = $this->logicLive_nivel->criarNivel(['mod_codigo' => $baseDados->meu_id, 'niv_nome' => $request->nome, 'niv_descricao' => $request->descricao, 'niv_ativo' => $request->ativo]);

                if ($criadoLogicLive['success'] == false) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, $criadoLogicLive['msg']);
                }
                $nivel->meu_logic_live_id = $criadoLogicLive['data']['niv_codigo'];
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

            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_nivel->atualizarNivel($nivel->meu_logic_live_id, ['niv_nome' => $request->nome, 'niv_descricao' => $request->descricao, 'niv_ativo' => $request->ativo, 'mod_codigo' => $nivel->modulo_id]);

                if ($criadoLogicLive['success'] == false) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::update, null, $criadoLogicLive['msg']);
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

            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_nivel->deletarNivel($nivel->meu_logic_live_id);

                if ($criadoLogicLive['success'] == false) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::destroy, null, $criadoLogicLive['msg']);
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
