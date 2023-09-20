<?php

use App\Http\Controllers\Api\Admin\ArvoreRefutacaoController as AdminArvoreRefutacaoController;
use App\Http\Controllers\Api\Admin\ExercicioController;
use App\Http\Controllers\Api\Admin\JogadorController as AdminJogadorController;
use App\Http\Controllers\Api\Admin\LogicLiveController;
use App\Http\Controllers\Api\Admin\NivelController;
use App\Http\Controllers\Api\Admin\RecompensaController;
use App\Http\Controllers\Api\Admin\RespostaController;
use App\Http\Controllers\Api\Aluno\ArvoreRefutacaoController;
use App\Http\Controllers\Api\Aluno\EstudoConceitosController;
use App\Http\Controllers\Api\Aluno\EstudoLivreController;
use App\Http\Controllers\Api\Aluno\JogadorController;
use App\Http\Controllers\Api\Aluno\ValidacaoFormulasController;
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
    Route::get('exercicios/all', [ExercicioController::class, 'all']);
    Route::get('exercicios/{id}', [ExercicioController::class, 'show']);
    Route::put('exercicios/{id}', [ExercicioController::class, 'update']);
    Route::post('exercicios', [ExercicioController::class, 'store']);
    Route::delete('exercicios/{id}', [ExercicioController::class, 'destroy']);

    Route::post('respostas', [RespostaController::class, 'index']);

    Route::get('jogadores/all', [AdminJogadorController::class, 'all']);

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

Route::middleware(['jogadorHash', 'exercicioHash'])->group(function () {
    Route::get('aluno/validacao-formula/inicia', [ValidacaoFormulasController::class, 'inicia']);
    Route::post('aluno/validacao-formula/adiciona', [ValidacaoFormulasController::class, 'adiciona']);
    Route::post('aluno/validacao-formula/deriva', [ValidacaoFormulasController::class, 'deriva']);
    Route::post('aluno/validacao-formula/tica', [ValidacaoFormulasController::class, 'tica']);
    Route::post('aluno/validacao-formula/fecha', [ValidacaoFormulasController::class, 'fecha']);
    Route::post('aluno/validacao-formula/concluir', [ValidacaoFormulasController::class, 'concluir']);
    Route::get('aluno/validacao-formula/tentar-novamente', [ValidacaoFormulasController::class, 'reiniciar']);

    Route::post('aluno/estudo-conceitos/concluir', [EstudoConceitosController::class, 'concluir']);
    Route::post('aluno/estudo-livre/concluir', [EstudoLivreController::class, 'concluir']);
});

Route::post('aluno/hash', [JogadorController::class, 'hash']);
Route::post('aluno/arvore/inicia', [ArvoreRefutacaoController::class, 'inicia']);
Route::post('aluno/arvore/arvore', [ArvoreRefutacaoController::class, 'arvore']);
Route::post('aluno/arvore/adiciona', [ArvoreRefutacaoController::class, 'adiciona']);
Route::post('aluno/arvore/deriva', [ArvoreRefutacaoController::class, 'deriva']);
Route::post('aluno/arvore/tica', [ArvoreRefutacaoController::class, 'tica']);
Route::post('aluno/arvore/fecha', [ArvoreRefutacaoController::class, 'fecha']);
