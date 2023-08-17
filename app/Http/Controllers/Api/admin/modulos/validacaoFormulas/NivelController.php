<?php

namespace App\Http\Controllers\Api\Admin\Modulos\ValidacaoFormulas;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LogicLive\Config\Configuracao;
use App\Http\Controllers\LogicLive\Modulos\ValidacaoFormulas\NivelVF;
use App\Models\LogicLive;
use App\Models\NivelMVFLP;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class NivelController extends Controller
{
    private $niveis;
    private $config;
    private $logicLive_nivel;

    public function __construct(NivelMVFLP $niveis)
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
     * @param  Request    $request
     * @param  NivelMVFLP $nivelMVFLP
     * @return Response
     */
    public function store(Request $request, NivelMVFLP $nivelMVFLP)
    {
        try {
            DB::beginTransaction();
            $nivelMVFLP->meu_id_logic_live = 0;
            $nivelMVFLP->id_modulo = isset($baseDados) ? $baseDados->meu_id : 0;
            $nivelMVFLP->nome = $request->nome;
            $nivelMVFLP->descricao = $request->descricao;
            $nivelMVFLP->ativo = $request->ativo;
            $nivelMVFLP->id_recompensa = $request->id_recompensa;
            $nivelMVFLP->save();

            if ($this->config->ativo()) {
                $baseDados = LogicLive::where('tipo', '=', 'modulo1')->get();
                $baseDados = $baseDados[0];
                $criadoLogicLive = $this->logicLive_nivel->criarNivel(['mod_codigo' => $baseDados->meu_id, 'niv_nome' => $request->nome, 'niv_descricao' => $request->descricao, 'niv_ativo' => $request->ativo]);

                if ($criadoLogicLive['success'] == false) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, $criadoLogicLive['msg']);
                }
                $nivelMVFLP->meu_id_logic_live = $criadoLogicLive['data']['niv_codigo'];
                $nivelMVFLP->save();
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
     * @param  Request  $request
     * @param  int      $id
     * @return Response
     */
    public function update(Request $request, int $id)
    {
        try {
            DB::beginTransaction();
            $nivelMVFLP = NivelMVFLP::findOrFail($id);
            $nivelMVFLP->update($request->all());
            $nivelMVFLP->save();

            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_nivel->atualizarNivel($nivelMVFLP->meu_id_logic_live, ['niv_nome' => $request->nome, 'niv_descricao' => $request->descricao, 'niv_ativo' => $request->ativo, 'mod_codigo' => $nivelMVFLP->id_modulo]);

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
            $nivelMVFLP = NivelMVFLP::all();
            return ResponseController::json(Type::success, Action::all, $nivelMVFLP);
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
            $nivelMVFLP = NivelMVFLP::findOrFail($id);
            $nivelMVFLP->delete();

            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_nivel->deletarNivel($nivelMVFLP->meu_id_logic_live);

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
