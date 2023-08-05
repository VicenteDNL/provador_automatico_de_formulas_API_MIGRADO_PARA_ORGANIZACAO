<?php

namespace App\Http\Controllers\Api\admin\modulos\validacaoFormulas;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LogicLive\config\Configuracao;
use App\Http\Controllers\LogicLive\modulos\validacaoFormulas\NivelVF;
use App\LogicLive;
use App\NivelMVFLP;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NivelVFController extends Controller
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
            if ($this->config->ativo()) {
                $baseDados = LogicLive::where('tipo', '=', 'modulo1')->get();
                $baseDados = $baseDados[0];
                $criadoLogicLive = $this->logicLive_nivel->criarNivel(['mod_codigo' => $baseDados->meu_id, 'niv_nome' => $request->nome, 'niv_descricao' => $request->descricao, 'niv_ativo' => $request->ativo]);

                if ($criadoLogicLive['success'] = false) {
                    return ResponseController::json(Type::error, Action::store, null, $criadoLogicLive['msg']);
                }
            }
            $nivelMVFLP->meu_id_logic_live = isset($criadoLogicLive) ? $criadoLogicLive['data']['niv_codigo'] : 0;
            $nivelMVFLP->id_modulo = isset($baseDados) ? $baseDados->meu_id : 0;
            $nivelMVFLP->nome = $request->nome;
            $nivelMVFLP->descricao = $request->descricao;
            $nivelMVFLP->ativo = $request->ativo;
            $nivelMVFLP->id_recompensa = $request->id_recompensa;
            $nivelMVFLP->save();

            return  ResponseController::json(Type::success, Action::store);
        } catch(Exception $e) {
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
            $nivelMVFLP = NivelMVFLP::findOrFail($id);

            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_nivel->atualizarNivel($nivelMVFLP->meu_id_logic_live, ['niv_nome' => $request->nome, 'niv_descricao' => $request->descricao, 'niv_ativo' => $request->ativo, 'mod_codigo' => $nivelMVFLP->id_modulo]);

                if ($criadoLogicLive['success'] == false) {
                    return ResponseController::json(Type::error, Action::update, null, $criadoLogicLive['msg']);
                }
            }

            $nivelMVFLP->update($request->all());
            $nivelMVFLP->save();
            return ResponseController::json(Type::success, Action::update);
        } catch(Exception $e) {
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
            if ($this->config->ativo()) {
                $nivelMVFLP = NivelMVFLP::findOrFail($id);
                $criadoLogicLive = $this->logicLive_nivel->deletarNivel($nivelMVFLP->meu_id_logic_live);

                if ($criadoLogicLive['success'] == false) {
                    return ResponseController::json(Type::error, Action::destroy, null, $criadoLogicLive['msg']);
                }
            }

            $nivelMVFLP = NivelMVFLP::findOrFail($id);
            $nivelMVFLP->delete();
            return  ResponseController::json(Type::success, Action::destroy);
        } catch(Exception $e) {
            return  ResponseController::json(Type::error, Action::destroy);
        }
    }
}
