<?php

namespace App\Http\Controllers\Api\admin\modulos\validacaoFormulas;

use App\ExercicioMVFLP;
use App\Formula;
use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LogicLive\config\Configuracao;
use App\Http\Controllers\LogicLive\modulos\validacaoFormulas\ExercicioVF;
use App\NivelMVFLP;
use App\Recompensa;
use App\Resposta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ExercicioVFController extends Controller
{
    private $exercicio ;
    private $config;
    private $logicLive_exercicio;

    public function __construct(ExercicioMVFLP $exercicio)
    {
        $this->exercicio = $exercicio;
        $this->config = new Configuracao();
        $this->logicLive_exercicio = new ExercicioVF();
    }

    /**
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * @param  Request        $request
     * @param  ExercicioMVFLP $exercicio
     * @return Response
     */
    public function store(Request $request, ExercicioMVFLP $exercicio)
    {
        try {
            // Verifica sê existe uma recompensa com o id da requisicao
            $recompensas = Recompensa::where('id', $request->id_recompensa)->get();

            if (count($recompensas) == 0) {
                return response()->json(['success' => false, 'msg' => 'recompensa informada nao foi encontrada', 'data' => ''], 500);
            }

            // Verifica sê existe um nivel com o id da requisicao
            $nivel = NivelMVFLP::where('id', $request->id_nivel)->get();

            if (count($nivel) == 0) {
                return response()->json(['success' => false, 'msg' => 'nivel informado nao foi encontrado', 'data' => ''], 500);
            }

            //Salvando a formula
            $formula = new Formula();
            $formula->formula = $request->formula['formula'];
            $formula->xml = $request->formula['xml'];
            $formula->quantidade_regras = $request->formula['quantidade_regras'];
            $formula->ticar_automaticamente = $request->formula['ticar_automaticamente'];
            $formula->fechar_automaticamente = $request->formula['fechar_automaticamente'];
            $formula->iniciar_zerada = $request->formula['iniciar_zerada'];
            $formula->inicio_personalizado = $request->formula['inicio_personalizado'];
            $formula->inicializacao_completa = $request->formula['inicializacao_completa'];

            if ($request->formula['inicio_personalizado'] == true && $request->formula['iniciar_zerada'] == false) {
                $formula->lista_passos = json_encode($request->formula['lista_passos']);
                $formula->lista_derivacoes = json_encode($request->formula['lista_derivacoes']);
                $formula->lista_ticagem = json_encode($request->formula['lista_ticagem']);
                $formula->lista_fechamento = json_encode($request->formula['lista_fechamento']);
            }

            $formula->save();

            //Salvando o exercicio
            $exercicio->id_recompensa = $request->id_recompensa;
            $exercicio->id_nivel = $request->id_nivel;
            $exercicio->nome = $request->nome;
            $exercicio->enunciado = $request->enunciado;
            $exercicio->tempo = $request->tempo;
            $exercicio->descricao = $request->descricao;
            $exercicio->ativo = $request->ativo;
            $exercicio->qndt_erros = $request->qndt_erros;
            $exercicio->hash = '';
            $exercicio->url = '';
            $exercicio->id_formula = $formula->id;
            $exercicio->save();
            $exercicio->url = $this->config->urlExercicioValidacao() . $exercicio->id;

            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_exercicio->criarExercicio([
                    'rec_codigo'        => $recompensas[0]->id_logic_live,
                    'niv_codigo'        => $nivel[0]->meu_id_logic_live,
                    'exe_tempoexecucao' => $exercicio->tempo,
                    'exe_link'          => $exercicio->url,
                    'exe_nome'          => $exercicio->nome,
                    'exe_descricao'     => $exercicio->descricao,
                    'exe_ativo'         => $exercicio->ativo,
                ]);

                if ($criadoLogicLive['success'] == false) {
                    $exercicio->delete();
                    $formula->delete();
                    return ResponseController::json(Type::error, Action::store, null, $criadoLogicLive['msg']);
                }
                $exercicio->hash = $criadoLogicLive['data']['exe_hash'];
                $exercicio->id_logic_live = $criadoLogicLive['data']['exe_codigo'];
            }

            $exercicio->save();

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
        try {
            $exercicio = $this->exercicio->find($id);
            $exercicio->formula = Formula::findOrFail($exercicio->id_formula);
            $exercicio->recompensa = Recompensa::findOrFail($exercicio->id_recompensa);
            return ResponseController::json(Type::success, Action::show, $exercicio);
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::show);
        }
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
            $exercicio = ExercicioMVFLP::findOrFail($id);
            $exercicio->update($request->all());
            $exercicio->save();

            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_exercicio->atualizarExercicio($exercicio->id_logic_live, [
                    'rec_codigo'        => Recompensa::findOrFail($exercicio->id_recompensa)->id_logic_live,
                    'niv_codigo'        => NivelMVFLP::findOrFail($exercicio->id_nivel)->meu_id_logic_live,
                    'exe_tempoexecucao' => $exercicio->tempo,
                    'exe_link'          => $exercicio->url,
                    'exe_nome'          => $exercicio->nome,
                    'exe_descricao'     => $exercicio->descricao,
                    'exe_ativo'         => $exercicio->ativo,
                ]);

                if ($criadoLogicLive['success'] == false) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::update, null, $criadoLogicLive['msg']);
                }
            }
            DB::commit();
            return ResponseController::json(Type::success, Action::update);
            ;
        } catch(Exception $e) {
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
            $exercicio = ExercicioMVFLP::findOrFail($id);
            $formula = Formula::findOrFail($exercicio->id_formula);

            // Caso exista uma resposta para o exercicio não deixa ele ser excluido
            if (count(Resposta::where('id_exercicio', '=', $exercicio->id)->get()) != 0) {
                return ResponseController::json(Type::error, Action::destroy, null, 'existe resposta para esse exercicio');
            }

            DB::beginTransaction();
            $formula->delete();
            $exercicio->delete();

            if ($this->config->ativo()) {
                $criadoLogicLive = $this->logicLive_exercicio->deletarExercicio($exercicio->id_logic_live);

                if ($criadoLogicLive['success'] == false) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::destroy, null, $criadoLogicLive['msg']);
                }
            }

            DB::commit();
            return ResponseController::json(Type::success, Action::destroy);
        } catch(Exception $e) {
            DB::rollBack();
            return ResponseController::json(Type::error, Action::destroy);
        }
    }

    /**
     * @param  int      $id
     * @return Response
     */
    public function listByIdNivel(int $id)
    {
        try {
            $nivelMVFLP = NivelMVFLP::find($id);

            if ($nivelMVFLP == null) {
                return ResponseController::json(Type::error, Action::listbyId);
            }

            $exercicios = ExercicioMVFLP::where('id_nivel', $nivelMVFLP->id)->paginate(10);
            return ResponseController::json(Type::success, Action::listbyId, $exercicios);
        } catch(Exception $e) {
            return ResponseController::json(Type::error, Action::listbyId);
        }
    }
}
