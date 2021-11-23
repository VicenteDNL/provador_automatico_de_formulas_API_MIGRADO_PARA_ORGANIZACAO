<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\AuthController;
use \App\Http\Controllers\Api\logicLive\LogicLiveController;
use \App\Http\Controllers\Api\aluno\ArvoreRefutacaoController;
use \App\Http\Controllers\Api\admin\modulos\validacaoFormulas\ExercicioVFController;

Route::post('auth/login', [AuthController::class, 'login']);

Route::group(['middleware'=>['auth:sanctum']],function(){
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', 'Api\AuthController@logout');
    Route::get('users', 'Api\UserController@index');

    Route::get('recompensas', 'Api\admin\modulos\RecompensaController@index');
    Route::post('recompensas', 'Api\admin\modulos\RecompensaController@store');
    Route::put('recompensas/{id}', 'Api\admin\modulos\RecompensaController@update');
    Route::delete('recompensas/{id}', 'Api\admin\modulos\RecompensaController@destroy');

    Route::get('mvflp/niveis', 'Api\admin\modulos\validacaoFormulas\NivelVFController@index');
    Route::get('mvflp/niveis/listarTodos', 'Api\admin\modulos\validacaoFormulas\NivelVFController@all');
    Route::get('mvflp/niveis/{id}', 'Api\admin\modulos\validacaoFormulas\NivelVFController@show');
    Route::post('mvflp/niveis', 'Api\admin\modulos\validacaoFormulas\NivelVFController@store');
    Route::put('mvflp/niveis/{id}', 'Api\admin\modulos\validacaoFormulas\NivelVFController@update');
    Route::delete('mvflp/niveis/{id}', 'Api\admin\modulos\validacaoFormulas\NivelVFController@destroy');


    Route::get('mvflp/exercicio/nivel/{id}', [ExercicioVFController::class, 'byIdNivel']);
    Route::post('mvflp/exercicio', [ExercicioVFController::class, 'store']);
    Route::get('mvflp/exercicio/{id}', [ExercicioVFController::class, 'show']);
    Route::put('mvflp/exercicio/{id}', [ExercicioVFController::class, 'update']);
    Route::delete('mvflp/exercicio/{id}', [ExercicioVFController::class, 'destroy']);


    Route::get('mvflp/resposta', 'Api\admin\modulos\validacaoFormulas\RespostaController@index');



    Route::post('arvore/otimizada', 'Api\admin\ArvoreRefutacaoController@criarArvoreOtimizada');
    Route::post('arvore/inicializacao/premisas-conclucao', 'Api\admin\ArvoreRefutacaoController@premissasConclusao');
    Route::post('arvore/inicializacao/adiciona-no', 'Api\admin\ArvoreRefutacaoController@adicionaNoIncializacao');
    Route::post('arvore/derivacao/adiciona-no', 'Api\admin\ArvoreRefutacaoController@derivar');
    Route::post('arvore/derivacao/fechar-no', 'Api\admin\ArvoreRefutacaoController@fecharNo');
    Route::post('arvore/derivacao/ticar-no', 'Api\admin\ArvoreRefutacaoController@ticarNo');


    //Requisições para configurar os modulos e o game
    Route::get('config/logiclive/', [LogicLiveController::class, 'infoModulosEndGame']);
    Route::post('config/logiclive/criar', [LogicLiveController::class, 'criarModulosEndGame']);
});

Route::post('aluno/hash', 'Api\aluno\autenticacao\AuthHash@hash');   //Valida o HASH do aluno
Route::post('aluno/livre/iniciar', 'Api\aluno\modulos\EstudoLivreController@iniciar');
Route::post('aluno/livre/arvore', 'Api\aluno\modulos\EstudoLivreController@arvore');
Route::post('aluno/livre/adiciona', 'Api\aluno\modulos\EstudoLivreController@adiciona');
Route::post('aluno/livre/deriva', 'Api\aluno\modulos\EstudoLivreController@deriva');
Route::post('aluno/livre/tica', 'Api\aluno\modulos\EstudoLivreController@tica');
Route::post('aluno/livre/fecha', 'Api\aluno\modulos\EstudoLivreController@fecha');


Route::post('aluno/conceitos/concluir/{id}', 'Api\aluno\modulos\EstudoConceitosController@concluir');



// Requisições do lado do aluno
Route::post('exercicio/validacao/resposta', 'Api\aluno\ArvoreRefutacaoController@validar');
Route::post('exercicio/validacao/{id}', 'Api\aluno\ExercicioVFController@buscarExercicio');
Route::get('exercicio/arvore/criar', 'Api\aluno\ExercicioVFController@criarArvoreExercicio');
Route::post('exercicio/tentarnovamente/{id}', 'Api\aluno\RespostaController@deletarResposta');


Route::post('aluno/hash', 'Api\aluno\autenticacao\AuthHash@hash');


Route::post('aluno/arvore/otimizada', 'Api\aluno\ArvoreRefutacaoController@criarArvoreOtimizada');
Route::post('aluno/arvore/inicializacao/premisas-conclucao', 'Api\aluno\ArvoreRefutacaoController@premissasConclusao');
Route::post('aluno/arvore/inicializacao/adiciona-no', [ArvoreRefutacaoController::class, 'adicionaNo']);
Route::post('aluno/arvore/derivacao/adiciona-no', [ArvoreRefutacaoController::class, 'derivar']);
Route::post('aluno/arvore/derivacao/fechar-no', [ArvoreRefutacaoController::class, 'fecharNo']);
Route::post('aluno/arvore/derivacao/ticar-no', [ArvoreRefutacaoController::class, 'ticarNo']);
