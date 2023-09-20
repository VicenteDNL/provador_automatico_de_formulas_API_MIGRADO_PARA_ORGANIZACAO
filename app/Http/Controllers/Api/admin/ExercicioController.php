<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\Exercicio\ExercicioStoreRequest;
use App\Http\Requests\API\Admin\Exercicio\ExercicioUpdateRequest;
use App\LogicLive\Common\Models\ExercicioModel;
use App\LogicLive\Config;
use App\LogicLive\Resources\ExercicioResource;
use App\Models\Exercicio;
use App\Models\Formula;
use App\Models\Nivel;
use App\Models\Recompensa;
use App\Models\Resposta;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExercicioController extends Controller
{
    private $exercicio;
    private $config;
    private $exercicioResource;

    public function __construct(Exercicio $exercicio)
    {
        $this->exercicio = $exercicio;
        $this->config = new Config();
        $this->exercicioResource = new ExercicioResource();
    }

    /**
     * @return Response
     */
    public function index()
    {
        try {
            $data = $this->exercicio
            ->with('nivel')
            ->with('formula')
            ->with('recompensa')
            ->orderBy('exercicios.created_at', 'desc')->paginate(10);
            return ResponseController::json(Type::success, Action::index, $data);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::index);
        }
    }

    /**
     * @return Response
     */
    public function all()
    {
        try {
            $data = Exercicio::all();
            return  ResponseController::json(Type::success, Action::index, $data);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::index);
        }
    }

    /**
     * @param  ExercicioStoreRequest $request
     * @param  Exercicio             $exercicio
     * @return Response
     */
    public function store(ExercicioStoreRequest $request, Exercicio $exercicio)
    {
        try {
            DB::beginTransaction();

            //Salvando a formula
            $formula = new Formula();
            $formula->formula = $request->formula['formula'];
            $formula->xml = trim($request->formula['xml']);
            $formula->quantidade_regras = $request->formula['quantidade_regras'];
            $formula->ticar_automaticamente = $request->formula['ticar_automaticamente'];
            $formula->fechar_automaticamente = $request->formula['fechar_automaticamente'];
            $formula->inicio_personalizado = $request->formula['inicio_personalizado'];

            if ($request->formula['inicio_personalizado']) {
                $formula->lista_passos = json_encode($request->formula['lista_passos']);
                $formula->lista_derivacoes = json_encode($request->formula['lista_derivacoes']);
                $formula->lista_ticagem = json_encode($request->formula['lista_ticagem']);
                $formula->lista_fechamento = json_encode($request->formula['lista_fechamento']);
            }

            $formula->saveOrFail();

            //Salvando o exercicio
            $exercicio->recompensa_id = $request->recompensa_id;
            $exercicio->nivel_id = $request->nivel_id;
            $exercicio->nome = $request->nome;
            $exercicio->enunciado = $request->enunciado;
            $exercicio->tempo = $request->tempo;
            $exercicio->descricao = $request->descricao;
            $exercicio->ativo = $request->ativo;
            $exercicio->qndt_erros = $request->qndt_erros;
            $exercicio->hash = '';
            $exercicio->url = '';
            $exercicio->formula_id = $formula->id;
            $exercicio->saveOrFail();
            $exercicio->url = $this->config->urlGame('exercicio/validacao/' . $exercicio->id);

            if ($this->config->ativo()) {
                $recompensa = Recompensa::findOrFail($exercicio->recompensa_id);
                $nivel = Nivel::findOrFail($exercicio->nivel_id);

                if (empty($recompensa->logic_live_id)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, 'recompensa não está vinculada ao Logic Live');
                }

                if (empty($nivel->logic_live_id)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, 'nivel não está vinculado ao Logic Live');
                }

                $exercicioLogicLive = $this->exercicioResource->create(
                    new ExercicioModel([
                        'rec_codigo'        => $recompensa->logic_live_id,
                        'niv_codigo'        => $nivel->logic_live_id,
                        'exe_tempoexecucao' => $exercicio->tempo,
                        'exe_link'          => $exercicio->url,
                        'exe_nome'          => $exercicio->nome,
                        'exe_descricao'     => $exercicio->descricao,
                        'exe_ativo'         => $exercicio->ativo,
                    ])
                );

                if (is_null($exercicioLogicLive)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, 'Erro ao criar exercicio no Logic live');
                }

                $exercicio->hash = $exercicioLogicLive->getExeHash();
                $exercicio->logic_live_id = $exercicioLogicLive->getExeCodigo();
            }

            $exercicio->saveOrFail();
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
        try {
            $exercicio = $this->exercicio
            ->with('nivel')
            ->with('formula')
            ->where('id', '=', $id)->firstOrFail();

            $exercicio->formula->lista_passos = json_decode($exercicio->formula->lista_passos);
            $exercicio->formula->lista_derivacoes = json_decode($exercicio->formula->lista_derivacoes);
            $exercicio->formula->lista_ticagem = json_decode($exercicio->formula->lista_ticagem);
            $exercicio->formula->lista_fechamento = json_decode($exercicio->formula->lista_fechamento);
            return ResponseController::json(Type::success, Action::show, $exercicio);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::show);
        }
    }

    /**
     * @param  ExercicioUpdateRequest $request
     * @param  int                    $id
     * @return Response
     */
    public function update(ExercicioUpdateRequest $request, int $id)
    {
        try {
            DB::beginTransaction();
            $exercicio = Exercicio::findOrFail($id);

            // Caso exista uma resposta para o exercicio ele só poderá ser desativado
            if (count(Resposta::where('exercicio_id', '=', $exercicio->id)->get()) > 0) {
                $exercicio->ativo = $request->ativo;
                $exercicio->saveOrFail();

                if ($this->config->ativo() && !is_null($exercicio->logic_live_id)) {
                    $exercicioLogicLive = $this->exercicioResource->update(
                        $exercicio->logic_live_id,
                        new ExercicioModel([
                            'exe_ativo' => $exercicio->ativo,
                        ])
                    );

                    if (is_null($exercicioLogicLive)) {
                        DB::rollBack();
                        return ResponseController::json(Type::error, Action::store, null, 'Erro ao editar exercicio no Logic live');
                    }
                }

                DB::commit();
                return ResponseController::json(Type::success, Action::update);
            }

            //Salvando a formula
            $formula = $exercicio->formula()->firstOrFail();
            $formula->formula = $request->formula['formula'] ?? $formula->formula;
            $formula->xml = trim($request->formula['xml']) ?? $formula->xml;
            $formula->quantidade_regras = $request->formula['quantidade_regras'] ?? $formula->quantidade_regras;
            $formula->ticar_automaticamente = $request->formula['ticar_automaticamente'] ?? $formula->ticar_automaticamente;
            $formula->fechar_automaticamente = $request->formula['fechar_automaticamente'] ?? $formula->fechar_automaticamente;
            $formula->inicio_personalizado = $request->formula['inicio_personalizado'] ?? $formula->inicio_personalizado;

            if ($request->formula['inicio_personalizado']) {
                $formula->lista_passos = empty($request->formula['lista_passos'])
                    ? null
                    : json_encode($request->formula['lista_passos']) ;
                $formula->lista_derivacoes = empty($request->formula['lista_derivacoes'])
                    ? null
                    : json_encode($request->formula['lista_derivacoes']) ;
                $formula->lista_ticagem = empty($request->formula['lista_ticagem'])
                    ? null
                    : json_encode($request->formula['lista_ticagem']) ;
                $formula->lista_fechamento = empty($request->formula['lista_fechamento'])
                    ? null
                    : json_encode($request->formula['lista_fechamento']) ;
            } else {
                $formula->lista_passos = null;
                $formula->lista_derivacoes = null;
                $formula->lista_ticagem = null;
                $formula->lista_fechamento = null;
            }

            $formula->saveOrFail();

            $exercicio->recompensa_id = $request->recompensa_id;
            $exercicio->nivel_id = $request->nivel_id;
            $exercicio->nome = $request->nome;
            $exercicio->enunciado = $request->enunciado;
            $exercicio->tempo = $request->tempo;
            $exercicio->descricao = $request->descricao;
            $exercicio->ativo = $request->ativo;
            $exercicio->qndt_erros = $request->qndt_erros;
            $exercicio->saveOrFail();

            if ($this->config->ativo() && !is_null($exercicio->logic_live_id)) {
                $recompensa = Recompensa::findOrFail($exercicio->recompensa_id);
                $nivel = Nivel::findOrFail($exercicio->nivel_id);

                if (empty($recompensa->logic_live_id)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, 'recompensa não está vinculada ao Logic Live');
                }

                if (empty($nivel->logic_live_id)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, 'nivel não está vinculado ao Logic Live');
                }

                $exercicioLogicLive = $this->exercicioResource->update(
                    $exercicio->logic_live_id,
                    new ExercicioModel([
                        'rec_codigo'        => $recompensa->logic_live_id,
                        'niv_codigo'        => $nivel->logic_live_id,
                        'exe_tempoexecucao' => $exercicio->tempo,
                        'exe_link'          => $exercicio->url,
                        'exe_nome'          => $exercicio->nome,
                        'exe_descricao'     => $exercicio->descricao,
                        'exe_ativo'         => $exercicio->ativo,
                    ])
                );

                if (is_null($exercicioLogicLive)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, 'Erro ao editar exercicio no Logic live');
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
            $exercicio = Exercicio::findOrFail($id);
            $formula = Formula::findOrFail($exercicio->formula_id);

            // Caso exista uma resposta para o exercicio não deixa ele ser excluido
            if (count(Resposta::where('exercicio_id', '=', $exercicio->id)->get()) != 0) {
                return ResponseController::json(Type::error, Action::destroy, null, 'existe resposta para esse exercicio');
            }

            DB::beginTransaction();
            $formula->delete();
            $exercicio->delete();

            if ($this->config->ativo() && !is_null($exercicio->logic_live_id)) {
                $exercicioLogicLive = $this->exercicioResource->delete($exercicio->logic_live_id);

                if (is_null($exercicioLogicLive)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store, null, 'Erro ao deletar exercicio no Logic live');
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
