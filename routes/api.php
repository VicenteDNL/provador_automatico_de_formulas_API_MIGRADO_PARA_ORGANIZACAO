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


    Route::get('recompensas', 'Api\admin\RecompensaController@index');

    Route::get('mvflp/niveis', 'Api\admin\NivelMvflpController@index');
    Route::get('mvflp/niveis/listarTodos', 'Api\admin\NivelMvflpController@all');
    Route::get('mvflp/niveis/{id}', 'Api\admin\NivelMvflpController@show');
    Route::post('mvflp/niveis', 'Api\admin\NivelMvflpController@store');
    Route::put('mvflp/niveis/{id}', 'Api\admin\NivelMvflpController@update');
    Route::delete('mvflp/niveis/{id}', 'Api\admin\NivelMvflpController@destroy');
    Route::get('mvflp/exercicio/nivel/{id}', 'Api\admin\ExercicioMvflpController@byIdNivel');
    Route::post('mvflp/exercicio', 'Api\admin\ExercicioMvflpController@store');
    Route::delete('mvflp/exercicio/{id}', 'Api\admin\admin\@destroy');


    Route::post('arvore/otimizada', 'Api\admin\ArvoreRefutacaoController@criarArvoreOtimizada');
    Route::post('arvore/inicializacao/premisas-conclucao', 'Api\admin\ArvoreRefutacaoController@premissasConclusao');
    Route::post('arvore/inicializacao/adiciona-no', 'Api\admin\ArvoreRefutacaoController@adicionaNoIncializacao');
    Route::post('arvore/derivacao/adiciona-no', 'Api\admin\ArvoreRefutacaoController@derivar');
    Route::post('arvore/derivacao/fechar-no', 'Api\admin\ArvoreRefutacaoController@fecharNo');
    Route::post('arvore/derivacao/ticar-no', 'Api\admin\ArvoreRefutacaoController@ticarNo');
});



// Requisições do lado do aluno
Route::get('exercicio/validacao/{id}', 'Api\aluno\ExercicioMvflpController@buscarExercicio');
Route::get('exercicio/arvore/criar', 'Api\aluno\ExercicioMvflpController@criarArvoreExercicio');



Route::post('aluno/arvore/otimizada', 'Api\aluno\ArvoreRefutacaoController@criarArvoreOtimizada');
Route::post('aluno/arvore/inicializacao/premisas-conclucao', 'Api\aluno\ArvoreRefutacaoController@premissasConclusao');
Route::post('aluno/arvore/inicializacao/adiciona-no', 'Api\aluno\ArvoreRefutacaoController@adicionaNoIncializacao');
Route::post('aluno/arvore/derivacao/adiciona-no', 'Api\aluno\ArvoreRefutacaoController@derivar');
Route::post('aluno/arvore/derivacao/fechar-no', 'Api\aluno\ArvoreRefutacaoController@fecharNo');
Route::post('aluno/arvore/derivacao/ticar-no', 'Api\aluno\ArvoreRefutacaoController@ticarNo');