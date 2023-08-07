<?php

namespace App\Http\Controllers\Api\aluno\modulos;

use App\ExercicioMVFLP;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LogicLive\modulos\Jogador as ModulosJogador;
use App\Http\Controllers\LogicLive\modulos\Resposta;
use App\Jogador;
use App\LogicLive;
use Illuminate\Http\Request;

class EstudoConceitosController extends Controller
{
    private $logicLive_jogador;
    private $logiclive_resposta;

    public function __construct()
    {

        $this->logicLive_jogador =  new ModulosJogador;
        $this->logiclive_resposta = new Resposta;

 
    }



    public function concluir(Request $request, $id){


        if(!isset($request->usu_hash)){
            return response()->json(['success' => false, 'msg'=>'hash jogador não informado!', 'data'=>''],500);
        }

        if($id==1){
            $exercicio = LogicLive::where('tipo','=','exercicio1_modulo2' )->first();
        }
        else{
            $exercicio = LogicLive::where('tipo','=','exercicio2_modulo2' )->first(); 
        }
        
        $criadoLogicLive = $this->logicLive_jogador->getJogador($request->usu_hash);
        if($criadoLogicLive['success']=false){
            return response()->json(['success' => false, 'msg'=>$criadoLogicLive['msg'], 'data'=>''],500);
        }

        $dados=[
            'exe_hash'=>$exercicio->hash,
            'usx_completado'=>true,
            'uer_log'=>'exercicio completado',
            'tempo_exercicio'=>null,
        ];
        
    
        $resultado = $this->logiclive_resposta->enviarResposta($dados,$request->usu_hash);
        return $resultado;
        if(!$resultado['success']){
            return  response()->json([
                'success' => false, 
                'msg'=>'error ao enviar resposta!', 
                ]); 
        }

        return  response()->json([
            'success' => true, 
            'msg'=>'concluido',
             'data'=>''
            ]);
            
    }
}
