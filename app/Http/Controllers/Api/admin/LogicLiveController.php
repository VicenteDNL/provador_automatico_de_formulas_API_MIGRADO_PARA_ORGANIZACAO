<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\LogicLive\LogicLiveStatusRequest;
use App\LogicLive\Common\Enums\Types;
use App\LogicLive\Common\Models\ExercicioModel;
use App\LogicLive\Common\Models\GameModel;
use App\LogicLive\Common\Models\ModuloModel;
use App\LogicLive\Common\Models\NivelModel;
use App\LogicLive\Config;
use App\LogicLive\Managers\ArvoreRefutacaoGame;
use App\LogicLive\Managers\EstudoConceitos\EstudoConceitosExercicio;
use App\LogicLive\Managers\EstudoConceitos\EstudoConceitosModulo;
use App\LogicLive\Managers\EstudoConceitos\EstudoConceitosNivel;
use App\LogicLive\Managers\EstudoConceitos\EstudoConceitosRecompensa;
use App\LogicLive\Managers\EstudoLivre\EstudoLivreExercicio;
use App\LogicLive\Managers\EstudoLivre\EstudoLivreModulo;
use App\LogicLive\Managers\EstudoLivre\EstudoLivreNivel;
use App\LogicLive\Managers\EstudoLivre\EstudoLivreRecompensa;
use App\LogicLive\Managers\ValidacaoFormulas\ValidacaoFormulasModulo;
use App\LogicLive\Resources\ExercicioResource;
use App\LogicLive\Resources\GameResource;
use App\LogicLive\Resources\ModuloResource;
use App\LogicLive\Resources\NivelResource;
use App\LogicLive\Resources\RecompensaResource;
use App\Models\LogicLive;
use Illuminate\Support\Facades\DB;
use Throwable;

class LogicLiveController extends Controller
{
    private GameResource $gameResource;
    private ModuloResource $moduloResource;
    private NivelResource $nivelResource;
    private ExercicioResource $exercicioResource;
    private RecompensaResource $recompensaResource;
    private LogicLive $logicLive;
    private Config $config;

    public function __construct(LogicLive $logicLive)
    {
        $this->gameResource = new GameResource();
        $this->moduloResource = new ModuloResource();
        $this->nivelResource = new NivelResource();
        $this->exercicioResource = new ExercicioResource();
        $this->recompensaResource = new RecompensaResource();
        $this->logicLive = $logicLive;
        $this->config = new Config();
    }

    public function ativo()
    {
        return ResponseController::json(Type::success, Action::show, ['ativo' => $this->config->ativo()]);
    }

    public function info()
    {
        if (!$this->config->ativo()) {
            return ResponseController::json(Type::error, Action::index, null, 'integração desativada');
        }

        $mapValues = function ($i) {
            return [
                'id'        => $i['meu_id'],
                'nome'      => $i['nome'],
                'descricao' => $i['descricao'],
                'ativo'     => $i['ativo'],
            ];
        };

        try {
            $response = [];

            $games = $this->logicLive->where(['tipo' => Types::GAME->descricao()])->get()->toArray();
            $response['games'] = array_map($mapValues, $games);

            foreach ($response['games'] as $key => $item) {
                $modulos = $this->logicLive->where([
                    'tipo'    => Types::MODULO->descricao(),
                    'game_id' => $item['id'],
                ])->get()->toArray();

                $response['games'][$key]['modulos'] = array_map($mapValues, $modulos);
            }

            foreach ($response['games'] as $keyGame => $itemGame) {
                foreach ($itemGame['modulos'] as $keyModulo => $itemModulo) {
                    $niveis = $this->logicLive->where([
                        'tipo'      => Types::NIVEL->descricao(),
                        'modulo_id' => $itemModulo['id'],
                    ])->get()->toArray();

                    $response['games'][$keyGame]['modulos'][$keyModulo]['niveis'] = array_map($mapValues, $niveis);
                }
            }

            foreach ($response['games'] as $keyGame => $itemGame) {
                foreach ($itemGame['modulos'] as $keyModulo => $itemModulo) {
                    foreach ($itemModulo['niveis'] as $keyNiveis => $itemNiveis) {
                        $exercicios = $this->logicLive->where([
                            'tipo'      => Types::EXERCICIO->descricao(),
                            'nivel_id'  => $itemNiveis['id'],
                        ])->get()->toArray();

                        $response['games'][$keyGame]['modulos'][$keyModulo]['niveis'][$keyNiveis]['exercicios']
                        = array_map($mapValues, $exercicios);
                    }
                }
            }

            return ResponseController::json(Type::success, Action::index, $response);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::index);
        }
    }

    public function createGame()
    {
        try {
            if (!$this->config->ativo()) {
                return ResponseController::json(Type::error, Action::store, null, 'integração desativada');
            }

            $item = LogicLive::where('tipo', '=', Types::GAME->descricao())->get();

            if (count($item) > 0) {
                return ResponseController::json(Type::error, Action::store, null, 'game já esta criado');
            }

            $game = new ArvoreRefutacaoGame();
            $game = $game->getDefaulModels();
            $gameResource = $this->gameResource->create($game);

            if (is_null($gameResource)) {
                return ResponseController::json(Type::error, Action::store);
            }

            $myGame = new LogicLive();
            $myGame->tipo = Types::GAME->descricao();
            $myGame->meu_id = $gameResource->getGamCodigo();
            $myGame->nome = $gameResource->getGamNome();
            $myGame->descricao = $gameResource->getGamDescricao();
            $myGame->ativo = $gameResource->getGamAtivo();
            $myGame->modelo = ArvoreRefutacaoGame::class;
            $myGame->save();

            $response = [
                'id'        => $myGame->meu_id,
                'nome'      => $myGame->nome,
                'descricao' => $myGame->descricao,
                'ativo'     => $myGame->ativo,
            ];

            return ResponseController::json(Type::success, Action::store, [$response]);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::store);
        }
    }

    public function ativoGame(LogicLiveStatusRequest $request, int $id)
    {
        try {
            if (!$this->config->ativo()) {
                return ResponseController::json(Type::error, Action::store, null, 'integração desativada');
            }

            $myGame = LogicLive::where(['tipo' => Types::GAME->descricao(), 'meu_id' => $id])->firstOrFail();

            $game = new GameModel([]);
            $game->setGamAtivo($request->ativo);
            $gameResource = $this->gameResource->update($id, $game);

            if (is_null($gameResource)) {
                return ResponseController::json(Type::error, Action::update);
            }

            $myGame->ativo = $request->ativo;
            $myGame->save();

            $response = [
                'id'        => $myGame->meu_id,
                'nome'      => $myGame->nome,
                'descricao' => $myGame->descricao,
                'ativo'     => $myGame->ativo,
            ];

            return ResponseController::json(Type::success, Action::update, $response);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::update);
        }
    }

    public function createModulos(int $idGame)
    {
        try {
            if (!$this->config->ativo()) {
                return ResponseController::json(Type::error, Action::store, null, 'integração desativada');
            }

            $myGame = LogicLive::where(['tipo' => Types::GAME->descricao(), 'meu_id' => $idGame])->firstOrFail();

            $myModulos = LogicLive::where(['tipo' => Types::MODULO->descricao()])->get();

            if (count($myModulos) > 0) {
                return ResponseController::json(Type::error, Action::store, null, 'modulos já foram criados');
            }
            $response = [];
            DB::beginTransaction();

            $moduloEstudoLivre = new EstudoLivreModulo();
            $moduloEstudoLivre = $moduloEstudoLivre->getDefaulModels();
            $moduloEstudoLivre->setGamCodigo($myGame->meu_id);
            $moduloResource = $this->moduloResource->create($moduloEstudoLivre);

            if (is_null($moduloResource)) {
                DB::rollBack();
                return ResponseController::json(Type::error, Action::store);
            }

            $myModuloEstudoLivre = new LogicLive();
            $myModuloEstudoLivre->tipo = Types::MODULO->descricao();
            $myModuloEstudoLivre->meu_id = $moduloResource->getModCodigo();
            $myModuloEstudoLivre->game_id = $moduloResource->getGamCodigo();
            $myModuloEstudoLivre->hash = $moduloResource->getModHash();
            $myModuloEstudoLivre->nome = $moduloResource->getModNome();
            $myModuloEstudoLivre->descricao = $moduloResource->getModDescricao();
            $myModuloEstudoLivre->ativo = $moduloResource->getModAtivo();
            $myModuloEstudoLivre->modelo = EstudoLivreModulo::class;
            $myModuloEstudoLivre->save();

            $response[] = [
                'id'        => $myModuloEstudoLivre->meu_id,
                'nome'      => $myModuloEstudoLivre->nome,
                'descricao' => $myModuloEstudoLivre->descricao,
                'ativo'     => $myModuloEstudoLivre->ativo,
            ];

            $moduloEstudoConceitos = new EstudoConceitosModulo();
            $moduloEstudoConceitos = $moduloEstudoConceitos->getDefaulModels();
            $moduloEstudoConceitos->setGamCodigo($myGame->meu_id);
            $moduloResource = $this->moduloResource->create($moduloEstudoConceitos);

            if (is_null($moduloResource)) {
                DB::rollBack();
                return ResponseController::json(Type::error, Action::store);
            }

            $myModuloEstudoConceitos = new LogicLive();
            $myModuloEstudoConceitos->tipo = Types::MODULO->descricao();
            $myModuloEstudoConceitos->meu_id = $moduloResource->getModCodigo();
            $myModuloEstudoConceitos->game_id = $moduloResource->getGamCodigo();
            $myModuloEstudoConceitos->hash = $moduloResource->getModHash();
            $myModuloEstudoConceitos->nome = $moduloResource->getModNome();
            $myModuloEstudoConceitos->descricao = $moduloResource->getModDescricao();
            $myModuloEstudoConceitos->ativo = $moduloResource->getModAtivo();
            $myModuloEstudoConceitos->modelo = EstudoConceitosModulo::class;
            $myModuloEstudoConceitos->save();

            $response[] = [
                'id'        => $myModuloEstudoConceitos->meu_id,
                'nome'      => $myModuloEstudoConceitos->nome,
                'descricao' => $myModuloEstudoConceitos->descricao,
                'ativo'     => $myModuloEstudoConceitos->ativo,
            ];

            $moduloValidacaoFormulas = new ValidacaoFormulasModulo();
            $moduloValidacaoFormulas = $moduloValidacaoFormulas->getDefaulModels();
            $moduloValidacaoFormulas->setGamCodigo($myGame->meu_id);
            $moduloResource = $this->moduloResource->create($moduloValidacaoFormulas);

            if (is_null($moduloResource)) {
                DB::rollBack();
                return ResponseController::json(Type::error, Action::store);
            }

            $myModuloValidacaoFormulas = new LogicLive();
            $myModuloValidacaoFormulas->tipo = Types::MODULO->descricao();
            $myModuloValidacaoFormulas->meu_id = $moduloResource->getModCodigo();
            $myModuloValidacaoFormulas->game_id = $moduloResource->getGamCodigo();
            $myModuloValidacaoFormulas->hash = $moduloResource->getModHash();
            $myModuloValidacaoFormulas->nome = $moduloResource->getModNome();
            $myModuloValidacaoFormulas->descricao = $moduloResource->getModDescricao();
            $myModuloValidacaoFormulas->ativo = $moduloResource->getModAtivo();
            $myModuloValidacaoFormulas->modelo = ValidacaoFormulasModulo::class;
            $myModuloValidacaoFormulas->save();

            $response[] = [
                'id'        => $myModuloValidacaoFormulas->meu_id,
                'nome'      => $myModuloValidacaoFormulas->nome,
                'descricao' => $myModuloValidacaoFormulas->descricao,
                'ativo'     => $myModuloValidacaoFormulas->ativo,
            ];
            DB::commit();
            return ResponseController::json(Type::success, Action::store, $response);
        } catch(Throwable $e) {
            DB::rollBack();
            return ResponseController::json(Type::error, Action::store);
        }
    }

    public function ativoModulo(LogicLiveStatusRequest $request, int $id)
    {
        try {
            if (!$this->config->ativo()) {
                return ResponseController::json(Type::error, Action::store, null, 'integração desativada');
            }
            $myModulo = LogicLive::where(['tipo' => Types::MODULO->descricao(), 'meu_id' => $id])->firstOrFail();

            $modulo = new ModuloModel();
            $modulo->setModAtivo($request->ativo);
            $moduloResource = $this->moduloResource->update($id, $modulo);

            if (is_null($moduloResource)) {
                return ResponseController::json(Type::error, Action::update);
            }

            $myModulo->ativo = $request->ativo;
            $myModulo->save();

            $response = [
                'id'        => $myModulo->meu_id,
                'nome'      => $myModulo->nome,
                'descricao' => $myModulo->descricao,
                'ativo'     => $myModulo->ativo,
            ];

            return ResponseController::json(Type::success, Action::update, $response);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::update);
        }
    }

    public function createNiveis(int $idModulo)
    {
        try {
            if (!$this->config->ativo()) {
                return ResponseController::json(Type::error, Action::store, null, 'integração desativada');
            }

            $myModulo = LogicLive::where(['tipo' => Types::MODULO->descricao(), 'meu_id' => $idModulo])->firstOrFail();

            $myNiveis = LogicLive::where(['tipo' => Types::NIVEL->descricao(), 'modulo_id' => $idModulo])->get();

            if (count($myNiveis) > 0) {
                return ResponseController::json(Type::error, Action::store, null, 'niveis já foram criados');
            }
            $modelo = null;
            $response = [];
            DB::beginTransaction();

            switch ($myModulo->modelo) {
                case EstudoLivreModulo::class:
                    $modelo = EstudoLivreNivel::class;
                    $niveis = new EstudoLivreNivel();
                    $niveis = $niveis->getDefaulModels();
                    break;
                case EstudoConceitosModulo::class:
                    $modelo = EstudoConceitosNivel::class;
                    $niveis = new EstudoConceitosNivel();
                    $niveis = $niveis->getDefaulModels();
                    break;
                case ValidacaoFormulasModulo::class:
                    return ResponseController::json(Type::error, Action::store, null, 'Os niveis desse módulo não são gerenciados por aqui');
                    break;
                default:
                    return ResponseController::json(Type::error, Action::store, null, 'Nenhuma entidade de nivel encontrada');
            }

            foreach ($niveis as $nivel) {
                $nivel->setModCodigo($myModulo->meu_id);
                $nivelResource = $this->nivelResource->create($nivel);

                if (is_null($nivelResource)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store);
                }
                $myNivel = new LogicLive();
                $myNivel->tipo = Types::NIVEL->descricao();
                $myNivel->meu_id = $nivelResource->getNivCodigo();
                $myNivel->modulo_id = $nivelResource->getModCodigo();
                $myNivel->nome = $nivelResource->getNivNome();
                $myNivel->descricao = $nivelResource->getNivDescricao();
                $myNivel->ativo = $nivelResource->getNivAtivo();
                $myNivel->modelo = $modelo;
                $myNivel->save();

                $response[] = [
                    'id'        => $myNivel->meu_id,
                    'nome'      => $myNivel->nome,
                    'descricao' => $myNivel->descricao,
                    'ativo'     => $myNivel->ativo,
                ];
            }
            DB::commit();
            return ResponseController::json(Type::success, Action::store, $response);
        } catch(Throwable $e) {
            DB::rollBack();
            return ResponseController::json(Type::error, Action::store);
        }
    }

    public function ativoNivel(LogicLiveStatusRequest $request, int $id)
    {
        try {
            if (!$this->config->ativo()) {
                return ResponseController::json(Type::error, Action::store, null, 'integração desativada');
            }

            $myNivel = LogicLive::where(['tipo' => Types::NIVEL->descricao(), 'meu_id' => $id])->firstOrFail();

            $nivel = new NivelModel();
            $nivel->setNivAtivo($request->ativo);
            $nivelResource = $this->nivelResource->update($id, $nivel);

            if (is_null($nivelResource)) {
                return ResponseController::json(Type::error, Action::update);
            }

            $myNivel->ativo = $request->ativo;
            $myNivel->save();

            $response = [
                'id'        => $myNivel->meu_id,
                'nome'      => $myNivel->nome,
                'descricao' => $myNivel->descricao,
                'ativo'     => $myNivel->ativo,
            ];

            return ResponseController::json(Type::success, Action::update, $response);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::update);
        }
    }

    public function createExercicios(int $idNivel)
    {
        try {
            if (!$this->config->ativo()) {
                return ResponseController::json(Type::error, Action::store, null, 'integração desativada');
            }

            $myNivel = LogicLive::where(['tipo' => Types::NIVEL->descricao(), 'meu_id' => $idNivel])->firstOrFail();

            $myNiveis = LogicLive::where(['tipo' => Types::EXERCICIO->descricao(), 'nivel_id' => $idNivel])->get();

            if (count($myNiveis) > 0) {
                return ResponseController::json(Type::error, Action::store, null, 'exercicios já foram criados');
            }

            $modelo = null;
            $modeloRec = null;
            $response = [];
            DB::beginTransaction();

            switch ($myNivel->modelo) {
                case EstudoLivreNivel::class:
                    $modelo = EstudoLivreExercicio::class;
                    $modeloRec = EstudoLivreRecompensa::class;
                    $exercicioManager = new EstudoLivreExercicio();
                    $exercicios = $exercicioManager->getDefaulModels();
                    $recompensas = $exercicioManager->getRecompensasModels();
                    break;
                case EstudoConceitosNivel::class:
                    $modelo = EstudoConceitosExercicio::class;
                    $modeloRec = EstudoConceitosRecompensa::class;
                    $exercicioManager = new EstudoConceitosExercicio();
                    $exercicios = $exercicioManager->getDefaulModels();
                    $recompensas = $exercicioManager->getRecompensasModels();

                    break;
                default:
                    return ResponseController::json(Type::error, Action::store, null, 'Nenhuma entidade de exercicio encontrada');
            }

            foreach ($exercicios as $keys => $exercicio) {
                $recompensa = $recompensas[$keys];
                $recompensaResource = $this->recompensaResource->create($recompensa);

                if (is_null($recompensaResource)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store);
                }

                $exercicio->setNivCodigo($myNivel->meu_id);
                $exercicio->setRecCodigo($recompensaResource->getRecCodigo());
                $exercicioResource = $this->exercicioResource->create($exercicio);

                if (is_null($exercicioResource)) {
                    DB::rollBack();
                    return ResponseController::json(Type::error, Action::store);
                }

                $myRecompensa = new LogicLive();
                $myRecompensa->tipo = Types::RECOMPENSA->descricao();
                $myRecompensa->meu_id = $recompensaResource->getRecCodigo();
                $myRecompensa->exercicio_id = $exercicioResource->getExeCodigo();
                $myRecompensa->nome = $recompensaResource->getRecNome();
                $myRecompensa->ativo = 1;
                $myRecompensa->modelo = $modeloRec;
                $myRecompensa->save();

                $myExercicio = new LogicLive();
                $myExercicio->tipo = Types::EXERCICIO->descricao();
                $myExercicio->meu_id = $exercicioResource->getExeCodigo();
                $myExercicio->nivel_id = $exercicioResource->getNivCodigo();
                $myExercicio->nome = $exercicioResource->getExeNome();
                $myExercicio->descricao = $exercicioResource->getExeDescricao();
                $myExercicio->ativo = $exercicioResource->getExeAtivo();
                $myExercicio->modelo = $modelo;
                $myExercicio->save();

                $response[] = [
                    'id'         => $myExercicio->meu_id,
                    'nome'       => $myExercicio->nome,
                    'descricao'  => $myExercicio->descricao,
                    'ativo'      => $myExercicio->ativo,
                ];
            }
            DB::commit();
            return ResponseController::json(Type::success, Action::store, $response);
        } catch(Throwable $e) {
            DB::rollBack();
            return ResponseController::json(Type::error, Action::store);
        }
    }

    public function ativoExercicio(LogicLiveStatusRequest $request, int $id)
    {
        try {
            if (!$this->config->ativo()) {
                return ResponseController::json(Type::error, Action::store, null, 'integração desativada');
            }

            $myExercicio = LogicLive::where(['tipo' => Types::EXERCICIO->descricao(), 'meu_id' => $id])->firstOrFail();

            $exercicio = new ExercicioModel();
            $exercicio->setExeAtivo($request->ativo);
            $exercicioResource = $this->exercicioResource->update($id, $exercicio);

            if (is_null($exercicioResource)) {
                return ResponseController::json(Type::error, Action::update);
            }

            $myExercicio->ativo = $request->ativo;
            $myExercicio->save();

            $response = [
                'id'        => $myExercicio->meu_id,
                'nome'      => $myExercicio->nome,
                'descricao' => $myExercicio->descricao,
                'ativo'     => $myExercicio->ativo,
            ];

            return ResponseController::json(Type::success, Action::update, $response);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::update);
        }
    }

    public function reset()
    {
        try {
            if (!$this->config->isDev()) {
                return ResponseController::json(Type::success, Action::destroy, null, 'recurso habilitado apenas para desenvolvimento');
            }

            $games = $this->gameResource->all();
            $modulos = $this->moduloResource->all();
            $recompensas = $this->recompensaResource->all();
            $niveis = $this->nivelResource->all();
            $exercicios = $this->exercicioResource->all();

            foreach ($exercicios as $exercicio) {
                $this->exercicioResource->delete($exercicio->getExeCodigo());
            }

            foreach ($niveis as $nivel) {
                $this->nivelResource->delete($nivel->getNivCodigo());
            }

            foreach ($recompensas as $recompensa) {
                $this->recompensaResource->delete($recompensa->getRecCodigo());
            }

            foreach ($modulos as $modulo) {
                $this->moduloResource->delete($modulo->getModCodigo());
            }

            foreach ($games as $game) {
                $this->gameResource->delete($game->getGamCodigo());
            }

            return ResponseController::json(Type::success, Action::destroy);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::destroy);
        }
    }
}
