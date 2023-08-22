<?php

use App\Http\Controllers\Api\Admin\ArvoreRefutacaoController as AdminArvoreRefutacaoController;
use App\Http\Controllers\Api\Admin\Modulos\RecompensaController;
use App\Http\Controllers\Api\Admin\Modulos\ValidacaoFormulas\RespostaController;
use App\Http\Controllers\Api\aluno\ArvoreRefutacaoController;
use App\Http\Controllers\Api\aluno\autenticacao\AuthHash;
use App\Http\Controllers\Api\aluno\ExercicioVFController as AlunoExercicioVFController;
use App\Http\Controllers\Api\aluno\modulos\EstudoConceitosController;
use App\Http\Controllers\Api\aluno\modulos\EstudoLivreController;
use App\Http\Controllers\Api\aluno\RespostaController as AlunoRespostaController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LogicLive\LogicLiveController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::get('usuarios', [UsuarioController::class, 'index']);
    Route::post('usuarios', [UsuarioController::class, 'store']);
    Route::delete('usuarios/{id}', [UsuarioController::class, 'destroy']);
    Route::get('usuarios/{id}', [UsuarioController::class, 'show']);
    Route::put('usuarios/{id}', [UsuarioController::class, 'update']);

    Route::get('recompensas', [RecompensaController::class, 'index']);
    Route::post('recompensas', [RecompensaController::class, 'store']);
    Route::put('recompensas/{id}', [RecompensaController::class, 'update']);
    Route::delete('recompensas/{id}', [RecompensaController::class, 'destroy']);

    Route::get('mvflp/niveis', [NivelVFController::class, 'index']);
    Route::get('mvflp/niveis/listarTodos', [NivelVFController::class, 'all']);
    Route::get('mvflp/niveis/{id}', [NivelVFController::class, 'show']);
    Route::post('mvflp/niveis', [NivelVFController::class, 'store']);
    Route::put('mvflp/niveis/{id}', [NivelVFController::class, 'update']);
    Route::delete('mvflp/niveis/{id}', [NivelVFController::class, 'destroy']);

    Route::get('mvflp/exercicio/nivel/{id}', [ExercicioVFController::class, 'listByIdNivel']);
    Route::post('mvflp/exercicio', [ExercicioVFController::class, 'store']);
    Route::get('mvflp/exercicio/{id}', [ExercicioVFController::class, 'show']);
    Route::put('mvflp/exercicio/{id}', [ExercicioVFController::class, 'update']);
    Route::delete('mvflp/exercicio/{id}', [ExercicioVFController::class, 'destroy']);
    Route::get('mvflp/exercicio/{id}/formula', [ExercicioVFController::class, 'formula']);

    Route::get('mvflp/resposta', [RespostaController::class, 'index']);

    Route::post('arvore/otimizada', [AdminArvoreRefutacaoController::class, 'criarArvoreOtimizada']);
    Route::post('arvore/inicializacao/premisas-conclucao', [AdminArvoreRefutacaoController::class, 'premissasConclusao']);
    Route::post('arvore/inicializacao/adiciona-no', [AdminArvoreRefutacaoController::class, 'adicionaNoIncializacao']);
    Route::post('arvore/derivacao/adiciona-no', [AdminArvoreRefutacaoController::class, 'derivar']);
    Route::post('arvore/derivacao/fechar-no', [AdminArvoreRefutacaoController::class, 'fecharNo']);
    Route::post('arvore/derivacao/ticar-no', [AdminArvoreRefutacaoController::class, 'ticarNo']);

    //Requisições para configurar os modulos e o game
    Route::get('config/logiclive/', [LogicLiveController::class, 'infoModulosEndGame']);
    Route::post('config/logiclive/criar', [LogicLiveController::class, 'criarModulosEndGame']);
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
