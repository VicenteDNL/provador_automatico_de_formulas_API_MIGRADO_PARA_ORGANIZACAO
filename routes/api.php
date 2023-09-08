<?php

use App\Http\Controllers\Api\Admin\ArvoreRefutacaoController as AdminArvoreRefutacaoController;
use App\Http\Controllers\Api\Admin\ExercicioController;
use App\Http\Controllers\Api\Admin\LogicLiveController;
use App\Http\Controllers\Api\Admin\NivelController;
use App\Http\Controllers\Api\Admin\RecompensaController;
use App\Http\Controllers\Api\Admin\RespostaController;
use App\Http\Controllers\Api\aluno\ArvoreRefutacaoController;
use App\Http\Controllers\Api\aluno\autenticacao\AuthHash;
use App\Http\Controllers\Api\aluno\ExercicioVFController as AlunoExercicioVFController;
use App\Http\Controllers\Api\aluno\modulos\EstudoConceitosController;
use App\Http\Controllers\Api\aluno\modulos\EstudoLivreController;
use App\Http\Controllers\Api\aluno\RespostaController as AlunoRespostaController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::get('usuarios', [UsuarioController::class, 'index']);
    Route::get('usuarios/{id}', [UsuarioController::class, 'show']);
    Route::put('usuarios/{id}', [UsuarioController::class, 'update']);
    Route::post('usuarios', [UsuarioController::class, 'store']);
    Route::delete('usuarios/{id}', [UsuarioController::class, 'destroy']);

    Route::get('recompensas', [RecompensaController::class, 'index']);
    Route::put('recompensas/{id}', [RecompensaController::class, 'update']);
    Route::post('recompensas', [RecompensaController::class, 'store']);
    Route::delete('recompensas/{id}', [RecompensaController::class, 'destroy']);

    Route::get('niveis', [NivelController::class, 'index']);
    Route::get('niveis/all', [NivelController::class, 'all']);
    Route::get('niveis/{id}', [NivelController::class, 'show']);
    Route::put('niveis/{id}', [NivelController::class, 'update']);
    Route::post('niveis', [NivelController::class, 'store']);
    Route::delete('niveis/{id}', [NivelController::class, 'destroy']);

    Route::get('exercicios', [ExercicioController::class, 'index']);
    Route::get('exercicios/{id}', [ExercicioController::class, 'show']);
    Route::put('exercicios/{id}', [ExercicioController::class, 'update']);
    Route::post('exercicios', [ExercicioController::class, 'store']);
    Route::delete('exercicios/{id}', [ExercicioController::class, 'destroy']);

    Route::get('respostas', [RespostaController::class, 'index']);

    Route::post('arvore/otimizada', [AdminArvoreRefutacaoController::class, 'arvore']);
    Route::post('arvore/inicia', [AdminArvoreRefutacaoController::class, 'inicia']);
    Route::post('arvore/adiciona', [AdminArvoreRefutacaoController::class, 'adiciona']);
    Route::post('arvore/deriva', [AdminArvoreRefutacaoController::class, 'deriva']);
    Route::post('arvore/tica', [AdminArvoreRefutacaoController::class, 'tica']);
    Route::post('arvore/fecha', [AdminArvoreRefutacaoController::class, 'fecha']);
    Route::post('arvore/recria', [AdminArvoreRefutacaoController::class, 'recria']);

    Route::get('logiclive/ativo', [LogicLiveController::class, 'ativo']);
    Route::get('logiclive/info', [LogicLiveController::class, 'info']);
    Route::post('logiclive/game/{id}/ativo', [LogicLiveController::class, 'ativoGame']);
    Route::post('logiclive/modulo/{id}/ativo', [LogicLiveController::class, 'ativoModulo']);
    Route::post('logiclive/nivel/{id}/ativo', [LogicLiveController::class, 'ativoNivel']);
    Route::post('logiclive/exercicio/{id}/ativo', [LogicLiveController::class, 'ativoExercicio']);
    Route::post('logiclive/game', [LogicLiveController::class, 'createGame']);
    Route::post('logiclive/game/{idGame}/modulos', [LogicLiveController::class, 'createModulos']);
    Route::post('logiclive/modulos/{idModulos}/niveis', [LogicLiveController::class, 'createNiveis']);
    Route::post('logiclive/niveis/{idNivel}/exercicios', [LogicLiveController::class, 'createExercicios']);
    Route::post('logiclive/reset', [LogicLiveController::class, 'reset']);
});

Route::post('aluno/hash', [AuthHash::class, 'hash']);   //Valida o HASH do aluno
Route::post('aluno/livre/inicia', [EstudoLivreController::class, 'inicia']);
Route::post('aluno/livre/arvore', [EstudoLivreController::class, 'arvore']);
Route::post('aluno/livre/adiciona', [EstudoLivreController::class, 'adiciona']);
Route::post('aluno/livre/deriva', [EstudoLivreController::class, 'deriva']);
Route::post('aluno/livre/tica', [EstudoLivreController::class, 'tica']);
Route::post('aluno/livre/fecha', [EstudoLivreController::class, 'fecha']);

Route::post('aluno/conceitos/concluir/{id}', [EstudoConceitosController::class, 'concluir']);

// Requisições do lado do aluno
Route::post('exercicio/validacao/resposta', [ArvoreRefutacaoController::class, 'validar']);
Route::post('exercicio/validacao/{id}', [AlunoExercicioVFController::class, 'buscarExercicio']);
Route::get('exercicio/arvore/criar', [AlunoExercicioVFController::class, 'criarArvoreExercicio']);
Route::post('exercicio/tentarnovamente/{id}', [AlunoRespostaController::class, 'deletarResposta']);

Route::post('aluno/arvore/otimizada', [ArvoreRefutacaoController::class, 'criarArvoreOtimizada']);
Route::post('aluno/arvore/inicializacao/premisas-conclucao', [ArvoreRefutacaoController::class, 'premissasConclusao']);
Route::post('aluno/arvore/inicializacao/adiciona-no', [ArvoreRefutacaoController::class, 'adicionaNo']);
Route::post('aluno/arvore/derivacao/adiciona-no', [ArvoreRefutacaoController::class, 'derivar']);
Route::post('aluno/arvore/derivacao/fechar-no', [ArvoreRefutacaoController::class, 'fecharNo']);
Route::post('aluno/arvore/derivacao/ticar-no', [ArvoreRefutacaoController::class, 'ticarNo']);
