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


    Route::get('recompensas', 'Api\RecompensaController@index');


    Route::get('mvflp/niveis', 'Api\NivelMvflpController@index');
    Route::get('mvflp/niveis/listarTodos', 'Api\NivelMvflpController@all');
    Route::get('mvflp/niveis/{id}', 'Api\NivelMvflpController@show');
    Route::post('mvflp/niveis', 'Api\NivelMvflpController@store');
    Route::put('mvflp/niveis/{id}', 'Api\NivelMvflpController@update');
    Route::delete('mvflp/niveis/{id}', 'Api\NivelMvflpController@destroy');


    Route::get('mvflp/exercicio/nivel/{id}', 'Api\ExercicioMvflpController@byIdNivel');
    Route::post('mvflp/exercicio', 'Api\ExercicioMvflpController@store');



    Route::post('arvore/otimizada', 'Api\ArvoreRefutacaoController@criarArvoreOtimizada');

    Route::post('arvore/inicializacao/premisas-conclucao', 'Api\ArvoreRefutacaoController@premissasConclusao');
    Route::post('arvore/inicializacao/adiciona-no', 'Api\ArvoreRefutacaoController@adicionaNoIncializacao');

    Route::post('arvore/derivacao/adiciona-no', 'Api\ArvoreRefutacaoController@derivar');
    Route::post('arvore/derivacao/fechar-no', 'Api\ArvoreRefutacaoController@fecharNo');
    Route::post('arvore/derivacao/ticar-no', 'Api\ArvoreRefutacaoController@ticarNo');
});

