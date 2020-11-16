<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('auth/login', 'Api\AuthController@login');
Route::get('auth/me', 'Api\AuthController@me');

Route::group(['middleware'=>['apiJwt']],function(){
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
    
    
    Route::get('mvflp/exercicio/nivel/{id}', 'Api\admin\modulos\validacaoFormulas\ExercicioVFController@byIdNivel');
    Route::post('mvflp/exercicio', 'Api\admin\modulos\validacaoFormulas\ExercicioVFController@store');
    Route::delete('mvflp/exercicio/{id}', 'Api\admin\admin\@destroy');


    Route::post('arvore/otimizada', 'Api\admin\ArvoreRefutacaoController@criarArvoreOtimizada');
    Route::post('arvore/inicializacao/premisas-conclucao', 'Api\admin\ArvoreRefutacaoController@premissasConclusao');
    Route::post('arvore/inicializacao/adiciona-no', 'Api\admin\ArvoreRefutacaoController@adicionaNoIncializacao');
    Route::post('arvore/derivacao/adiciona-no', 'Api\admin\ArvoreRefutacaoController@derivar');
    Route::post('arvore/derivacao/fechar-no', 'Api\admin\ArvoreRefutacaoController@fecharNo');
    Route::post('arvore/derivacao/ticar-no', 'Api\admin\ArvoreRefutacaoController@ticarNo');


    // Requisições da configurar os modulos e o game
    Route::get('config/logiclive/', 'Api\logiclive\LogicLiveController@infoModulosEndGame');
    Route::post('config/logiclive/criar', 'Api\logiclive\LogicLiveController@criarModulosEndGame');
});



// Requisições do lado do aluno
Route::post('exercicio/validacao/{id}', 'Api\aluno\ExercicioVFController@buscarExercicio');
Route::get('exercicio/arvore/criar', 'Api\aluno\ExercicioVFController@criarArvoreExercicio');
Route::post('exercicio/tentarnovamente/{id}', 'Api\aluno\RespostaController@deletarResposta');



Route::post('aluno/arvore/otimizada', 'Api\aluno\ArvoreRefutacaoController@criarArvoreOtimizada');
Route::post('aluno/arvore/inicializacao/premisas-conclucao', 'Api\aluno\ArvoreRefutacaoController@premissasConclusao');
Route::post('aluno/arvore/inicializacao/adiciona-no', 'Api\aluno\ArvoreRefutacaoController@adicionaNo');
Route::post('aluno/arvore/derivacao/adiciona-no', 'Api\aluno\ArvoreRefutacaoController@derivar');
Route::post('aluno/arvore/derivacao/fechar-no', 'Api\aluno\ArvoreRefutacaoController@fecharNo');
Route::post('aluno/arvore/derivacao/ticar-no', 'Api\aluno\ArvoreRefutacaoController@ticarNo');